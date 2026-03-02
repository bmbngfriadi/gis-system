<?php
require 'db.php'; 
require 'helper.php';

date_default_timezone_set('Asia/Jakarta'); 
$conn->query("SET time_zone = '+07:00'");

// Auto-migration untuk kolom baru
try {
    $conn->query("ALTER TABLE gis_requests ADD COLUMN issue_photo VARCHAR(255) NULL AFTER app_wh");
    $conn->query("ALTER TABLE gis_requests ADD COLUMN receive_photo VARCHAR(255) NULL AFTER issue_photo");
    $conn->query("ALTER TABLE gis_requests ADD COLUMN received_by VARCHAR(100) NULL AFTER receive_photo");
    $conn->query("ALTER TABLE gis_requests ADD COLUMN receive_time DATETIME NULL AFTER received_by");
} catch (Exception $e) {}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$now = date('Y-m-d H:i:s');

// Helper Check Permissions
function checkAccess($conn, $username, $perm) {
    $u = $conn->real_escape_string($username);
    $q = $conn->query("SELECT role, access_rights, department FROM users WHERE username='$u'")->fetch_assoc();
    if($q['role'] === 'Administrator') return true;
    
    $rights = json_decode($q['access_rights'] ?? '[]', true) ?: [];
    if (empty($rights) && in_array($q['role'], ['Warehouse']) || ($q['role'] === 'TeamLeader' && strtolower($q['department']) === 'warehouse')) {
        $rights = ['gi_submit', 'gr_submit', 'item_add', 'item_edit', 'stock_edit', 'export_data'];
    }
    return in_array($perm, $rights);
}

// Helper Upload Image Base64
function uploadGisPhoto($base64Data, $prefix) {
    $uploadDir = "../uploads/gis/"; 
    if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }
    if (strpos($base64Data, 'base64,') !== false) {
        $base64Data = explode('base64,', $base64Data)[1];
    }
    $decodedData = base64_decode($base64Data);
    if ($decodedData === false) return false;
    $fileName = $prefix . "_" . time() . "_" . rand(100,999) . ".jpg";
    $filePath = $uploadDir . $fileName;
    if (file_put_contents($filePath, $decodedData)) {
        return "uploads/gis/" . $fileName; 
    }
    return false;
}

// Helper Generator Teks WA
function buildItemListText($conn, $itemsArray) {
    $text = "📋 *Daftar Barang & Sisa Stok:*\n";
    foreach($itemsArray as $it) {
        $code = $conn->real_escape_string($it['code']);
        $name = $it['name'];
        $qty = $it['qty'];
        $uom = $it['uom'] ?? '';
        
        $stkQ = $conn->query("SELECT stock FROM gis_inventory WHERE item_code='$code'")->fetch_assoc();
        $currentStock = $stkQ ? $stkQ['stock'] : 0;
        
        $text .= "▪️ $code - $name\n   Diminta/Diambil: $qty $uom | Sisa Stok: $currentStock $uom\n";
    }
    return $text;
}

// --- 1. INVENTORY MANAGEMENT ---
if($action == 'getInventory') {
    $res = $conn->query("SELECT * FROM gis_inventory ORDER BY item_name ASC");
    sendJson($res->fetch_all(MYSQLI_ASSOC));
}

if($action == 'saveItem') {
    $isEdit = !empty($input['is_edit']) && $input['is_edit'] == '1';
    $username = $input['username'] ?? '';
    
    if(!$isEdit && !checkAccess($conn, $username, 'item_add')) sendJson(['success'=>false, 'message'=>'Tidak ada akses menambah item.']);
    if($isEdit && !checkAccess($conn, $username, 'item_edit') && !checkAccess($conn, $username, 'stock_edit')) sendJson(['success'=>false, 'message'=>'Tidak ada akses mengubah item/stok.']);
    
    $code = $conn->real_escape_string($input['item_code']);
    $name = $conn->real_escape_string($input['item_name']);
    $spec = $conn->real_escape_string($input['item_spec'] ?? '');
    $cat = $conn->real_escape_string($input['category']);
    $uom = $conn->real_escape_string($input['uom']);
    $stock = intval($input['stock']);
    
    $canEditInfo = checkAccess($conn, $username, 'item_edit');
    $canEditStock = checkAccess($conn, $username, 'stock_edit');

    if($isEdit) {
        $updates = [];
        if($canEditInfo) {
            $updates[] = "item_name='$name'"; $updates[] = "item_spec='$spec'";
            $updates[] = "category='$cat'"; $updates[] = "uom='$uom'";
        }
        if($canEditStock) { $updates[] = "stock=$stock"; }
        $updates[] = "last_updated='$now'";
        
        $sql = "UPDATE gis_inventory SET " . implode(', ', $updates) . " WHERE item_code='$code'";
    } else {
        $sql = "INSERT INTO gis_inventory (item_code, item_name, item_spec, category, uom, stock, last_updated) 
                VALUES ('$code', '$name', '$spec', '$cat', '$uom', $stock, '$now') 
                ON DUPLICATE KEY UPDATE item_name='$name', item_spec='$spec', category='$cat', uom='$uom', stock=$stock, last_updated='$now'";
    }
            
    if($conn->query($sql)) sendJson(['success'=>true, 'message'=>'Item saved.']);
    else sendJson(['success'=>false, 'message'=>$conn->error]);
}

if($action == 'importItems') {
    $role = $input['role'];
    if($role !== 'Administrator') sendJson(['success'=>false, 'message'=>'Unauthorized. Only Admin can import.']);
    
    $items = $input['data'];
    $conn->begin_transaction();
    try {
        foreach($items as $it) {
            $code = $conn->real_escape_string($it['item_code']);
            $name = $conn->real_escape_string($it['item_name']);
            $spec = $conn->real_escape_string($it['item_spec'] ?? '');
            $cat = $conn->real_escape_string($it['category'] ?? 'General');
            $uom = $conn->real_escape_string($it['uom'] ?? 'Pcs');
            $stock = intval($it['stock'] ?? 0);
            
            if(!empty($code) && !empty($name)) {
                $sql = "INSERT INTO gis_inventory (item_code, item_name, item_spec, category, uom, stock, last_updated) 
                        VALUES ('$code', '$name', '$spec', '$cat', '$uom', $stock, '$now') 
                        ON DUPLICATE KEY UPDATE item_name='$name', item_spec='$spec', category='$cat', uom='$uom', stock=$stock, last_updated='$now'";
                $conn->query($sql);
            }
        }
        $conn->commit();
        sendJson(['success'=>true, 'message'=>'Bulk import master item successful.']);
    } catch(Exception $e) {
        $conn->rollback();
        sendJson(['success'=>false, 'message'=>$e->getMessage()]);
    }
}

// --- 2. GOOD RECEIVE (BARANG MASUK) ---
if($action == 'getReceives') {
    $res = $conn->query("SELECT * FROM gis_receives ORDER BY created_at DESC LIMIT 100");
    $data = [];
    while($row = $res->fetch_assoc()) {
        $row['items'] = json_decode($row['items_json'], true);
        $data[] = $row;
    }
    sendJson($data);
}

if($action == 'submitGR') {
    $u = $conn->real_escape_string($input['username']);
    if(!checkAccess($conn, $u, 'gr_submit')) sendJson(['success'=>false, 'message'=>'Unauthorized. No Access for GR.']);

    $grId = "GR-" . time();
    $f = $conn->real_escape_string($input['fullname']);
    $remarks = $conn->real_escape_string($input['remarks']);
    $items = $input['items']; 
    $itemsJson = json_encode($items);

    $conn->begin_transaction();
    try {
        $sql = "INSERT INTO gis_receives (gr_id, username, fullname, remarks, items_json, created_at) 
                VALUES ('$grId', '$u', '$f', '$remarks', '$itemsJson', '$now')";
        $conn->query($sql);

        foreach($items as $it) {
            $ic = $conn->real_escape_string($it['code']); 
            $qty = intval($it['qty']);
            $conn->query("UPDATE gis_inventory SET stock = stock + $qty, last_updated = '$now' WHERE item_code = '$ic'");
        }
        $conn->commit();
        
        $itemsText = buildItemListText($conn, $items);
        $msgHeader = "📥 *GOOD RECEIVE (BARANG MASUK)*\nGR ID: $grId\nReceived by: $f\nSupplier/Remarks: $remarks\n\n$itemsText";
        
        $whPhones = array_unique(array_merge(getPhones($conn, 'Warehouse'), getPhones($conn, 'TeamLeader', 'Warehouse'), getPhones($conn, 'Administrator')));
        foreach($whPhones as $ph) sendWA($ph, $msgHeader . "\n👉 Stok Master telah berhasil ditambahkan otomatis.");

        sendJson(['success'=>true, 'message'=>'Good Receive berhasil diproses. Stok bertambah.']);
    } catch (Exception $e) { 
        $conn->rollback(); 
        sendJson(['success'=>false, 'message'=>$e->getMessage()]); 
    }
}

// --- 3. GOOD ISSUE (REQUEST & HISTORY) ---
if($action == 'getRequests') {
    $role = $input['role']; $dept = $conn->real_escape_string($input['department']); $u = $conn->real_escape_string($input['username']);
    $sql = "SELECT * FROM gis_requests WHERE 1=1";
    
    if(!in_array($role, ['Administrator', 'Warehouse', 'PlantHead']) && !($role === 'TeamLeader' && strtolower($dept) === 'warehouse')) {
        if(in_array($role, ['SectionHead', 'TeamLeader'])) $sql .= " AND department = '$dept'";
        else $sql .= " AND username = '$u'";
    }
    $sql .= " ORDER BY created_at DESC LIMIT 100";
    
    $res = $conn->query($sql);
    $data = [];
    while($row = $res->fetch_assoc()) {
        $row['items'] = json_decode($row['items_json'], true);
        $data[] = $row;
    }
    sendJson($data);
}

if($action == 'submitRequest') {
    $u = $conn->real_escape_string($input['username']);
    if(!checkAccess($conn, $u, 'gi_submit')) sendJson(['success'=>false, 'message'=>'Unauthorized. No Access for GI.']);

    $reqId = "GIF-" . time(); 
    $f = $input['fullname']; 
    $d = $input['department'];
    $sec = $conn->real_escape_string($input['section']); 
    $purpose = $conn->real_escape_string($input['purpose']);
    $items = $input['items']; 
    
    foreach($items as $it) {
        $ic = $conn->real_escape_string($it['code']);
        $reqQty = intval($it['qty']);
        $cc = trim($it['cost_center'] ?? '');
        
        if(empty($cc)) {
            sendJson(['success'=>false, 'message'=>"Cost Center WAJIB diisi untuk barang " . ($it['name'] ?? $ic)]);
        }

        $cek = $conn->query("SELECT stock, item_name FROM gis_inventory WHERE item_code = '$ic'")->fetch_assoc();
        if(!$cek || $cek['stock'] < $reqQty) {
            sendJson(['success'=>false, 'message'=>"Stock tidak cukup untuk " . ($cek['item_name'] ?? $ic) . " (Sisa: " . ($cek['stock']??0) . ")"]);
        }
    }

    $itemsJson = json_encode($items);
    $sql = "INSERT INTO gis_requests (req_id, username, fullname, department, section, purpose, items_json, created_at) 
            VALUES ('$reqId', '$u', '$f', '$d', '$sec', '$purpose', '$itemsJson', '$now')";
            
    if($conn->query($sql)) {
        
        $itemsText = buildItemListText($conn, $items);
        $msgHeader = "📦 *NEW GOOD ISSUE REQUEST*\nNO GIF: $reqId\nUser: $f ($d / $sec)\nAct. Desc: $purpose\n\n$itemsText";

        $headPhones = getPhones($conn, ['SectionHead', 'TeamLeader'], $d);
        foreach($headPhones as $ph) sendWA($ph, $msgHeader . "\n👉 Silakan login ke sistem untuk Approve.");
        
        $userPhone = getUserPhone($conn, $u);
        if($userPhone) sendWA($userPhone, $msgHeader . "\n👉 Menunggu persetujuan Dept Head.");

        $whPhones = array_unique(array_merge(getPhones($conn, 'Warehouse'), getPhones($conn, 'TeamLeader', 'Warehouse')));
        foreach($whPhones as $ph) sendWA($ph, $msgHeader . "\n👉 Status saat ini: Menunggu persetujuan Dept Head.");

        sendJson(['success'=>true, 'message'=>'Request submitted.']);
    } else sendJson(['success'=>false, 'message'=>$conn->error]);
}

if($action == 'editRequest') {
    $reqId = $conn->real_escape_string($input['reqId']);
    $u = $conn->real_escape_string($input['username']);
    
    $cekReq = $conn->query("SELECT status, username, department FROM gis_requests WHERE req_id='$reqId'")->fetch_assoc();
    if(!$cekReq || $cekReq['username'] !== $u || $cekReq['status'] !== 'Pending Head') {
        sendJson(['success'=>false, 'message'=>'Data sudah diproses, tidak dapat diedit lagi.']);
    }

    $sec = $conn->real_escape_string($input['section']);
    $purpose = $conn->real_escape_string($input['purpose']);
    $items = $input['items'];
    
    foreach($items as $it) {
        $ic = $conn->real_escape_string($it['code']);
        $reqQty = intval($it['qty']);
        $cc = trim($it['cost_center'] ?? '');
        
        if(empty($cc)) {
            sendJson(['success'=>false, 'message'=>"Cost Center WAJIB diisi untuk barang " . ($it['name'] ?? $ic)]);
        }

        $cek = $conn->query("SELECT stock, item_name FROM gis_inventory WHERE item_code = '$ic'")->fetch_assoc();
        if(!$cek || $cek['stock'] < $reqQty) {
            sendJson(['success'=>false, 'message'=>"Stock tidak cukup untuk " . ($cek['item_name'] ?? $ic)]);
        }
    }

    $itemsJson = json_encode($items);
    $sql = "UPDATE gis_requests SET section='$sec', purpose='$purpose', items_json='$itemsJson' WHERE req_id='$reqId'";
    
    if($conn->query($sql)) {
        $itemsText = buildItemListText($conn, $items);
        $msgHeader = "✏️ *GOOD ISSUE EDITED*\nNO GIF: $reqId\nDiubah oleh: $u\nAct. Desc: $purpose\n\n$itemsText";
        
        $headPhones = getPhones($conn, ['SectionHead', 'TeamLeader'], $cekReq['department']);
        foreach($headPhones as $ph) sendWA($ph, $msgHeader . "\n👉 Silakan login untuk Approve form yang baru diubah.");

        $userPhone = getUserPhone($conn, $u);
        if($userPhone) sendWA($userPhone, $msgHeader . "\n👉 Form berhasil diubah, menunggu persetujuan Dept Head.");

        $whPhones = array_unique(array_merge(getPhones($conn, 'Warehouse'), getPhones($conn, 'TeamLeader', 'Warehouse')));
        foreach($whPhones as $ph) sendWA($ph, $msgHeader . "\n👉 Status saat ini: Menunggu persetujuan Dept Head.");

        sendJson(['success'=>true, 'message'=>'Request updated successfully.']);
    } else sendJson(['success'=>false, 'message'=>$conn->error]);
}

// --- FUNGSI CANCEL REQUEST ---
if($action == 'cancelRequest') {
    $reqId = $conn->real_escape_string($input['reqId']);
    $u = $conn->real_escape_string($input['username']);
    $f = $conn->real_escape_string($input['fullname']);

    $cekReq = $conn->query("SELECT status, department, section, purpose, items_json FROM gis_requests WHERE req_id='$reqId' AND username='$u'")->fetch_assoc();

    if(!$cekReq || $cekReq['status'] !== 'Pending Head') {
        sendJson(['success'=>false, 'message'=>'Data tidak dapat dibatalkan (sudah diproses oleh Head/Warehouse atau bukan milik Anda).']);
    }

    $sql = "UPDATE gis_requests SET status='Cancelled' WHERE req_id='$reqId'";
    
    if($conn->query($sql)) {
        $items = json_decode($cekReq['items_json'], true);
        $itemsText = buildItemListText($conn, $items);
        $msgHeader = "🚫 *GOOD ISSUE CANCELLED*\nNO GIF: $reqId\nDibatalkan oleh: $f\nDept/Sec: {$cekReq['department']} / {$cekReq['section']}\n\n$itemsText";

        // Notify User
        $userPhone = getUserPhone($conn, $u);
        if($userPhone) sendWA($userPhone, $msgHeader . "\n👉 Anda telah membatalkan permintaan ini.");

        // Notify Head
        $headPhones = getPhones($conn, ['SectionHead', 'TeamLeader'], $cekReq['department']);
        foreach($headPhones as $ph) sendWA($ph, $msgHeader . "\n👉 Permintaan ini telah dibatalkan oleh pemohon. Tidak perlu tindakan persetujuan lebih lanjut.");

        // Notify WH
        $whPhones = array_unique(array_merge(getPhones($conn, 'Warehouse'), getPhones($conn, 'TeamLeader', 'Warehouse')));
        foreach($whPhones as $ph) sendWA($ph, $msgHeader . "\n👉 Permintaan ini batal masuk ke gudang.");

        sendJson(['success'=>true, 'message'=>'Request cancelled successfully.']);
    } else {
        sendJson(['success'=>false, 'message'=>$conn->error]);
    }
}

if($action == 'updateStatus') {
    $id = $input['reqId']; 
    $act = $input['act']; 
    $role = $input['role']; 
    $fullname = $conn->real_escape_string($input['fullname']);
    $reason = $conn->real_escape_string($input['reason'] ?? '');
    
    $req = $conn->query("SELECT * FROM gis_requests WHERE req_id = '$id'")->fetch_assoc();
    if(!$req) sendJson(['success'=>false, 'message'=>'Data not found']);
    
    $reqPhone = getUserPhone($conn, $req['username']);
    $items = json_decode($req['items_json'], true);

    if($act == 'approve') {
        if($req['status'] == 'Pending Head') {
            $conn->query("UPDATE gis_requests SET status='Pending Warehouse', app_head='Approved by $fullname', head_time='$now' WHERE req_id='$id'");
            
            $itemsText = buildItemListText($conn, $items);
            $msgHeader = "✅ *GI APPROVED BY HEAD*\nNO GIF: $id\nApproved by: $fullname\n\n$itemsText";

            $whPhones = array_unique(array_merge(getPhones($conn, 'Warehouse'), getPhones($conn, 'TeamLeader', 'Warehouse')));
            foreach($whPhones as $ph) sendWA($ph, $msgHeader . "\n👉 Silakan siapkan barang dan proses (Issue) di sistem.");

            if($reqPhone) sendWA($reqPhone, $msgHeader . "\n👉 Permintaan Anda telah disetujui Head dan sedang disiapkan Gudang.");
            
            sendJson(['success'=>true, 'message'=>'Approved by Head.']);
        }
    }
    elseif($act == 'issue') {
        $isWarehouseAdmin = in_array($role, ['Administrator', 'Warehouse']) || ($role === 'TeamLeader' && strtolower($input['department'] ?? '') === 'warehouse');
        if($req['status'] == 'Pending Warehouse' && $isWarehouseAdmin) {
            
            if(empty($input['photoBase64'])) sendJson(['success'=>false, 'message'=>'Bukti foto wajib dilampirkan.']);
            
            $photoUrl = uploadGisPhoto($input['photoBase64'], "ISSUE_" . preg_replace('/[^a-zA-Z0-9]/', '', $id));
            if(!$photoUrl) sendJson(['success'=>false, 'message'=>'Gagal upload foto.']);

            $conn->begin_transaction();
            try {
                foreach($items as $it) {
                    $ic = $conn->real_escape_string($it['code']); $qty = intval($it['qty']);
                    $conn->query("UPDATE gis_inventory SET stock = stock - $qty, last_updated = '$now' WHERE item_code = '$ic'");
                }
                
                $conn->query("UPDATE gis_requests SET status='Pending Receive', app_wh='Issued by $fullname', wh_time='$now', issue_photo='$photoUrl' WHERE req_id='$id'");
                $conn->commit();
                
                $itemsText = buildItemListText($conn, $items); 
                $msgHeader = "🚚 *GI ISSUED BY WAREHOUSE*\nNO GIF: $id\nIssued by: $fullname\n\n$itemsText";

                if($reqPhone) sendWA($reqPhone, $msgHeader . "\n👉 Barang fisik sudah disiapkan/dikeluarkan. Silakan ambil barang dan lakukan konfirmasi penerimaan (Receive) di sistem.");
                
                $whPhones = array_unique(array_merge(getPhones($conn, 'Warehouse'), getPhones($conn, 'TeamLeader', 'Warehouse')));
                foreach($whPhones as $ph) sendWA($ph, $msgHeader . "\n👉 Menunggu konfirmasi penerimaan (Receive) dari User.");
                
                sendJson(['success'=>true, 'message'=>'Barang berhasil di-Issue. Menunggu konfirmasi penerimaan user.']);
            } catch (Exception $e) { $conn->rollback(); sendJson(['success'=>false, 'message'=>$e->getMessage()]); }
        }
    }
    elseif($act == 'receive') {
        if($req['status'] == 'Pending Receive' && $req['username'] == $input['username']) {
            if(empty($input['photoBase64'])) sendJson(['success'=>false, 'message'=>'Bukti foto penerimaan wajib dilampirkan.']);
            
            $photoUrl = uploadGisPhoto($input['photoBase64'], "RECV_" . preg_replace('/[^a-zA-Z0-9]/', '', $id));
            if(!$photoUrl) sendJson(['success'=>false, 'message'=>'Gagal upload foto.']);

            $sql = "UPDATE gis_requests SET status='Completed', received_by='$fullname', receive_time='$now', receive_photo='$photoUrl' WHERE req_id='$id'";
            if($conn->query($sql)) {
                
                $itemsText = buildItemListText($conn, $items);
                $msgHeader = "🏁 *GI COMPLETED (RECEIVED)*\nNO GIF: $id\nReceived by: $fullname\n\n$itemsText";

                if($reqPhone) sendWA($reqPhone, $msgHeader . "\n👉 Proses pengeluaran barang selesai. Terima kasih.");

                $whPhones = array_unique(array_merge(getPhones($conn, 'Warehouse'), getPhones($conn, 'TeamLeader', 'Warehouse')));
                foreach($whPhones as $ph) sendWA($ph, $msgHeader . "\n👉 User telah menerima barang fisik dengan baik.");

                sendJson(['success'=>true, 'message'=>'Barang berhasil diterima. Proses selesai.']);
            } else {
                sendJson(['success'=>false, 'message'=>$conn->error]);
            }
        } else {
            sendJson(['success'=>false, 'message'=>'Unauthorized to receive.']);
        }
    }
    elseif($act == 'reject') {
        $sql = "UPDATE gis_requests SET status='Rejected', reject_reason='$reason'";
        if($req['status'] == 'Pending Head') $sql .= ", app_head='Rejected by $fullname', head_time='$now'";
        if($req['status'] == 'Pending Warehouse') $sql .= ", app_wh='Rejected by $fullname', wh_time='$now'";
        $sql .= " WHERE req_id='$id'";
        
        $conn->query($sql);
        
        $itemsText = buildItemListText($conn, $items);
        $msgHeader = "❌ *GI REJECTED*\nNO GIF: $id\nRejected by: $fullname\nAlasan: $reason\n\n$itemsText";

        if($reqPhone) sendWA($reqPhone, $msgHeader . "\n👉 Permintaan Anda ditolak.");

        $whPhones = array_unique(array_merge(getPhones($conn, 'Warehouse'), getPhones($conn, 'TeamLeader', 'Warehouse')));
        foreach($whPhones as $ph) sendWA($ph, $msgHeader . "\n👉 Permintaan GI ini telah ditolak di sistem.");

        sendJson(['success'=>true, 'message'=>'Request rejected.']);
    }
}

// --- 4. EXPORT DATA ---
if($action == 'exportData') {
    $u = $input['username'];
    if(!checkAccess($conn, $u, 'export_data')) sendJson(['success'=>false, 'message'=>'Unauthorized. No Access for Export.']);

    $type = $input['export_type'];
    $start = $conn->real_escape_string($input['start_date']) . " 00:00:00";
    $end = $conn->real_escape_string($input['end_date']) . " 23:59:59";
    
    $data = [];
    if ($type === 'GI') {
        $sql = "SELECT * FROM gis_requests WHERE created_at BETWEEN '$start' AND '$end' ORDER BY created_at ASC";
        $res = $conn->query($sql);
        while($row = $res->fetch_assoc()) {
            $row['items'] = json_decode($row['items_json'], true);
            $data[] = $row;
        }
    } elseif ($type === 'GR') {
        $sql = "SELECT * FROM gis_receives WHERE created_at BETWEEN '$start' AND '$end' ORDER BY created_at ASC";
        $res = $conn->query($sql);
        while($row = $res->fetch_assoc()) {
            $row['items'] = json_decode($row['items_json'], true);
            $data[] = $row;
        }
    } elseif ($type === 'INV') {
        $sql = "SELECT * FROM gis_inventory ORDER BY item_name ASC";
        $res = $conn->query($sql);
        $data = $res->fetch_all(MYSQLI_ASSOC);
    }
    
    sendJson(['success'=>true, 'data'=>$data]);
}
?>