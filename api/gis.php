<?php
session_start();
require 'db.php'; 
require 'helper.php';

header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");

// SECURITY CHECK
if(!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    sendJson(['success' => false, 'message' => 'Sesi habis. Silakan login ulang.', 'code' => 401]);
}

// AMBIL DATA PATEN DARI SERVER (ANTI-SPOOFING)
$serverUser = $_SESSION['user_data']['username'];
$serverRole = $_SESSION['user_data']['role'];
$serverDept = $_SESSION['user_data']['department'];
$serverName = $_SESSION['user_data']['fullname'];

date_default_timezone_set('Asia/Jakarta'); 
$conn->query("SET time_zone = '+07:00'");

// Auto-migration
try {
    $conn->query("ALTER TABLE gis_requests ADD COLUMN issue_photo VARCHAR(255) NULL AFTER app_wh");
    $conn->query("ALTER TABLE gis_requests ADD COLUMN receive_photo VARCHAR(255) NULL AFTER issue_photo");
    $conn->query("ALTER TABLE gis_requests ADD COLUMN received_by VARCHAR(100) NULL AFTER receive_photo");
    $conn->query("ALTER TABLE gis_requests ADD COLUMN receive_time DATETIME NULL AFTER received_by");
} catch (Exception $e) {}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$now = date('Y-m-d H:i:s');

// Helper Check Permissions from Session
function checkAccessSession($perm) {
    global $serverRole, $serverDept;
    if($serverRole === 'Administrator') return true;
    $rights = json_decode($_SESSION['user_data']['access_rights'] ?? '[]', true) ?: [];
    if (empty($rights) && in_array($serverRole, ['Warehouse']) || ($serverRole === 'TeamLeader' && strtolower($serverDept) === 'warehouse')) {
        $rights = ['gi_submit', 'gr_submit', 'item_add', 'item_edit', 'stock_edit', 'export_data'];
    }
    return in_array($perm, $rights);
}

function uploadGisPhoto($base64Data, $prefix) {
    $uploadDir = "../uploads/gis/"; 
    if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }
    if (strpos($base64Data, 'base64,') !== false) { $base64Data = explode('base64,', $base64Data)[1]; }
    $decodedData = base64_decode($base64Data);
    if ($decodedData === false) return false;
    $fileName = $prefix . "_" . time() . "_" . rand(100,999) . ".jpg";
    $filePath = $uploadDir . $fileName;
    if (file_put_contents($filePath, $decodedData)) { return "uploads/gis/" . $fileName; }
    return false;
}

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
    $data = [];
    if($res) { while($row = $res->fetch_assoc()) { $data[] = $row; } }
    sendJson($data);
}

if($action == 'saveItem') {
    $isEdit = !empty($input['is_edit']) && $input['is_edit'] == '1';
    if(!$isEdit && !checkAccessSession('item_add')) sendJson(['success'=>false, 'message'=>'Access Denied', 'code'=>403]);
    if($isEdit && !checkAccessSession('item_edit') && !checkAccessSession('stock_edit')) sendJson(['success'=>false, 'message'=>'Access Denied', 'code'=>403]);
    
    $code = $conn->real_escape_string($input['item_code']);
    $name = $conn->real_escape_string($input['item_name']);
    $spec = $conn->real_escape_string($input['item_spec'] ?? '');
    $cat = $conn->real_escape_string($input['category']);
    $uom = $conn->real_escape_string($input['uom']);
    $stock = intval($input['stock']);
    
    $canEditInfo = checkAccessSession('item_edit');
    $canEditStock = checkAccessSession('stock_edit');

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
    if($serverRole !== 'Administrator') sendJson(['success'=>false, 'message'=>'Unauthorized.', 'code'=>403]);
    
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
    if($res) {
        while($row = $res->fetch_assoc()) {
            $row['items'] = json_decode($row['items_json'], true);
            $data[] = $row;
        }
    }
    sendJson($data);
}

if($action == 'submitGR') {
    if(!checkAccessSession('gr_submit')) sendJson(['success'=>false, 'message'=>'Unauthorized.', 'code'=>403]);

    $grId = "GR-" . time();
    $remarks = $conn->real_escape_string($input['remarks']);
    $items = $input['items']; 
    $itemsJson = json_encode($items);

    $conn->begin_transaction();
    try {
        $sql = "INSERT INTO gis_receives (gr_id, username, fullname, remarks, items_json, created_at) 
                VALUES ('$grId', '$serverUser', '$serverName', '$remarks', '$itemsJson', '$now')";
        $conn->query($sql);

        foreach($items as $it) {
            $ic = $conn->real_escape_string($it['code']); 
            $qty = intval($it['qty']);
            $conn->query("UPDATE gis_inventory SET stock = stock + $qty, last_updated = '$now' WHERE item_code = '$ic'");
        }
        $conn->commit();
        
        $itemsText = buildItemListText($conn, $items);
        $msgHeader = "📥 *GOOD RECEIVE (BARANG MASUK)*\nGR ID: $grId\nReceived by: $serverName\nSupplier/Remarks: $remarks\n\n$itemsText";
        
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
    $sql = "SELECT * FROM gis_requests WHERE 1=1";
    if(!in_array($serverRole, ['Administrator', 'Warehouse', 'PlantHead']) && !($serverRole === 'TeamLeader' && strtolower($serverDept) === 'warehouse')) {
        if(in_array($serverRole, ['SectionHead', 'TeamLeader'])) $sql .= " AND department = '$serverDept'";
        else $sql .= " AND username = '$serverUser'";
    }
    $sql .= " ORDER BY created_at DESC LIMIT 100";
    
    $res = $conn->query($sql);
    $data = [];
    if($res) {
        while($row = $res->fetch_assoc()) {
            $row['items'] = json_decode($row['items_json'], true);
            $data[] = $row;
        }
    }
    sendJson($data);
}

if($action == 'submitRequest') {
    if(!checkAccessSession('gi_submit')) sendJson(['success'=>false, 'message'=>'Unauthorized.', 'code'=>403]);

    $reqId = "GIF-" . time(); 
    $sec = $conn->real_escape_string($input['section']); 
    $purpose = $conn->real_escape_string($input['purpose']);
    $items = $input['items']; 
    
    foreach($items as $it) {
        $ic = $conn->real_escape_string($it['code']);
        $reqQty = intval($it['qty']);
        $cc = trim($it['cost_center'] ?? '');
        if(empty($cc)) sendJson(['success'=>false, 'message'=>"Cost Center WAJIB diisi."]);

        $cek = $conn->query("SELECT stock, item_name FROM gis_inventory WHERE item_code = '$ic'")->fetch_assoc();
        if(!$cek || $cek['stock'] < $reqQty) sendJson(['success'=>false, 'message'=>"Stock tidak cukup."]);
    }

    $itemsJson = json_encode($items);
    $sql = "INSERT INTO gis_requests (req_id, username, fullname, department, section, purpose, items_json, created_at) 
            VALUES ('$reqId', '$serverUser', '$serverName', '$serverDept', '$sec', '$purpose', '$itemsJson', '$now')";
            
    if($conn->query($sql)) {
        $itemsText = buildItemListText($conn, $items);
        $msgHeader = "📦 *NEW GOOD ISSUE REQUEST*\nNO GIF: $reqId\nUser: $serverName ($serverDept / $sec)\nAct. Desc: $purpose\n\n$itemsText";

        $headPhones = getPhones($conn, ['SectionHead', 'TeamLeader'], $serverDept);
        foreach($headPhones as $ph) sendWA($ph, $msgHeader . "\n👉 Silakan login ke sistem untuk Approve.");
        
        $userPhone = getUserPhone($conn, $serverUser);
        if($userPhone) sendWA($userPhone, $msgHeader . "\n👉 Menunggu persetujuan Dept Head.");

        $whPhones = array_unique(array_merge(getPhones($conn, 'Warehouse'), getPhones($conn, 'TeamLeader', 'Warehouse')));
        foreach($whPhones as $ph) sendWA($ph, $msgHeader . "\n👉 Status saat ini: Menunggu persetujuan Dept Head.");

        sendJson(['success'=>true, 'message'=>'Request submitted.']);
    } else sendJson(['success'=>false, 'message'=>$conn->error]);
}

if($action == 'editRequest') {
    $reqId = $conn->real_escape_string($input['reqId']);
    
    // Keamanan ekstra, pastikan ini punya user tersebut
    $cekReq = $conn->query("SELECT status, username, department FROM gis_requests WHERE req_id='$reqId'")->fetch_assoc();
    if(!$cekReq || $cekReq['username'] !== $serverUser || $cekReq['status'] !== 'Pending Head') {
        sendJson(['success'=>false, 'message'=>'Data sudah diproses atau bukan milik Anda.', 'code'=>403]);
    }

    $sec = $conn->real_escape_string($input['section']);
    $purpose = $conn->real_escape_string($input['purpose']);
    $items = $input['items'];
    
    foreach($items as $it) {
        $ic = $conn->real_escape_string($it['code']);
        $reqQty = intval($it['qty']);
        if(empty(trim($it['cost_center'] ?? ''))) sendJson(['success'=>false, 'message'=>"Cost Center WAJIB diisi."]);

        $cek = $conn->query("SELECT stock, item_name FROM gis_inventory WHERE item_code = '$ic'")->fetch_assoc();
        if(!$cek || $cek['stock'] < $reqQty) sendJson(['success'=>false, 'message'=>"Stock tidak cukup"]);
    }

    $itemsJson = json_encode($items);
    $sql = "UPDATE gis_requests SET section='$sec', purpose='$purpose', items_json='$itemsJson' WHERE req_id='$reqId'";
    
    if($conn->query($sql)) {
        $itemsText = buildItemListText($conn, $items);
        $msgHeader = "✏️ *GOOD ISSUE EDITED*\nNO GIF: $reqId\nDiubah oleh: $serverUser\nAct. Desc: $purpose\n\n$itemsText";
        
        $headPhones = getPhones($conn, ['SectionHead', 'TeamLeader'], $cekReq['department']);
        foreach($headPhones as $ph) sendWA($ph, $msgHeader . "\n👉 Silakan login untuk Approve form yang baru diubah.");

        $userPhone = getUserPhone($conn, $serverUser);
        if($userPhone) sendWA($userPhone, $msgHeader . "\n👉 Form berhasil diubah, menunggu persetujuan Dept Head.");

        sendJson(['success'=>true, 'message'=>'Request updated successfully.']);
    } else sendJson(['success'=>false, 'message'=>$conn->error]);
}

if($action == 'cancelRequest') {
    $reqId = $conn->real_escape_string($input['reqId']);
    $cekReq = $conn->query("SELECT status, department, section, purpose, items_json FROM gis_requests WHERE req_id='$reqId' AND username='$serverUser'")->fetch_assoc();

    if(!$cekReq || $cekReq['status'] !== 'Pending Head') {
        sendJson(['success'=>false, 'message'=>'Data tidak dapat dibatalkan.', 'code'=>403]);
    }

    if($conn->query("UPDATE gis_requests SET status='Cancelled' WHERE req_id='$reqId'")) {
        $items = json_decode($cekReq['items_json'], true);
        $itemsText = buildItemListText($conn, $items);
        $msgHeader = "🚫 *GOOD ISSUE CANCELLED*\nNO GIF: $reqId\nDibatalkan oleh: $serverName\nDept/Sec: {$cekReq['department']} / {$cekReq['section']}\n\n$itemsText";

        $userPhone = getUserPhone($conn, $serverUser);
        if($userPhone) sendWA($userPhone, $msgHeader . "\n👉 Anda telah membatalkan permintaan ini.");

        $headPhones = getPhones($conn, ['SectionHead', 'TeamLeader'], $cekReq['department']);
        foreach($headPhones as $ph) sendWA($ph, $msgHeader . "\n👉 Permintaan dibatalkan oleh pemohon. Abaikan pengajuan ini.");

        $whPhones = array_unique(array_merge(getPhones($conn, 'Warehouse'), getPhones($conn, 'TeamLeader', 'Warehouse')));
        foreach($whPhones as $ph) sendWA($ph, $msgHeader . "\n👉 Permintaan batal masuk ke gudang.");

        sendJson(['success'=>true, 'message'=>'Request cancelled successfully.']);
    } else {
        sendJson(['success'=>false, 'message'=>$conn->error]);
    }
}

if($action == 'updateStatus') {
    $id = $conn->real_escape_string($input['reqId']); 
    $act = $input['act']; 
    $reason = $conn->real_escape_string($input['reason'] ?? '');
    
    $req = $conn->query("SELECT * FROM gis_requests WHERE req_id = '$id'")->fetch_assoc();
    if(!$req) sendJson(['success'=>false, 'message'=>'Data not found']);
    
    $reqPhone = getUserPhone($conn, $req['username']);
    $items = json_decode($req['items_json'], true);

    if($act == 'approve') {
        if($req['status'] == 'Pending Head') {
            $conn->query("UPDATE gis_requests SET status='Pending Warehouse', app_head='Approved by $serverName', head_time='$now' WHERE req_id='$id'");
            
            $itemsText = buildItemListText($conn, $items);
            $msgHeader = "✅ *GI APPROVED BY HEAD*\nNO GIF: $id\nApproved by: $serverName\n\n$itemsText";

            $whPhones = array_unique(array_merge(getPhones($conn, 'Warehouse'), getPhones($conn, 'TeamLeader', 'Warehouse')));
            foreach($whPhones as $ph) sendWA($ph, $msgHeader . "\n👉 Silakan siapkan barang dan proses (Issue) di sistem.");
            if($reqPhone) sendWA($reqPhone, $msgHeader . "\n👉 Permintaan Anda telah disetujui Head dan sedang disiapkan Gudang.");
            
            sendJson(['success'=>true, 'message'=>'Approved by Head.']);
        }
    }
    elseif($act == 'issue') {
        $isWarehouseAdmin = in_array($serverRole, ['Administrator', 'Warehouse']) || ($serverRole === 'TeamLeader' && strtolower($serverDept) === 'warehouse');
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
                
                $conn->query("UPDATE gis_requests SET status='Pending Receive', app_wh='Issued by $serverName', wh_time='$now', issue_photo='$photoUrl' WHERE req_id='$id'");
                $conn->commit();
                
                $itemsText = buildItemListText($conn, $items); 
                $msgHeader = "🚚 *GI ISSUED BY WAREHOUSE*\nNO GIF: $id\nIssued by: $serverName\n\n$itemsText";

                if($reqPhone) sendWA($reqPhone, $msgHeader . "\n👉 Barang fisik sudah disiapkan/dikeluarkan. Silakan ambil barang dan lakukan konfirmasi penerimaan (Receive) di sistem.");
                
                $whPhones = array_unique(array_merge(getPhones($conn, 'Warehouse'), getPhones($conn, 'TeamLeader', 'Warehouse')));
                foreach($whPhones as $ph) sendWA($ph, $msgHeader . "\n👉 Menunggu konfirmasi penerimaan (Receive) dari User.");
                
                sendJson(['success'=>true, 'message'=>'Barang berhasil di-Issue. Menunggu konfirmasi penerimaan user.']);
            } catch (Exception $e) { $conn->rollback(); sendJson(['success'=>false, 'message'=>$e->getMessage()]); }
        }
    }
    elseif($act == 'receive') {
        if($req['status'] == 'Pending Receive' && $req['username'] == $serverUser) {
            if(empty($input['photoBase64'])) sendJson(['success'=>false, 'message'=>'Bukti foto penerimaan wajib dilampirkan.']);
            
            $photoUrl = uploadGisPhoto($input['photoBase64'], "RECV_" . preg_replace('/[^a-zA-Z0-9]/', '', $id));
            if(!$photoUrl) sendJson(['success'=>false, 'message'=>'Gagal upload foto.']);

            $sql = "UPDATE gis_requests SET status='Completed', received_by='$serverName', receive_time='$now', receive_photo='$photoUrl' WHERE req_id='$id'";
            if($conn->query($sql)) {
                
                $itemsText = buildItemListText($conn, $items);
                $msgHeader = "🏁 *GI COMPLETED (RECEIVED)*\nNO GIF: $id\nReceived by: $serverName\n\n$itemsText";

                if($reqPhone) sendWA($reqPhone, $msgHeader . "\n👉 Proses pengeluaran barang selesai. Terima kasih.");

                $whPhones = array_unique(array_merge(getPhones($conn, 'Warehouse'), getPhones($conn, 'TeamLeader', 'Warehouse')));
                foreach($whPhones as $ph) sendWA($ph, $msgHeader . "\n👉 User telah menerima barang fisik dengan baik.");

                sendJson(['success'=>true, 'message'=>'Barang berhasil diterima. Proses selesai.']);
            } else {
                sendJson(['success'=>false, 'message'=>$conn->error]);
            }
        }
    }
    elseif($act == 'reject') {
        $sql = "UPDATE gis_requests SET status='Rejected', reject_reason='$reason'";
        if($req['status'] == 'Pending Head') $sql .= ", app_head='Rejected by $serverName', head_time='$now'";
        if($req['status'] == 'Pending Warehouse') $sql .= ", app_wh='Rejected by $serverName', wh_time='$now'";
        $sql .= " WHERE req_id='$id'";
        
        $conn->query($sql);
        
        $itemsText = buildItemListText($conn, $items);
        $msgHeader = "❌ *GI REJECTED*\nNO GIF: $id\nRejected by: $serverName\nAlasan: $reason\n\n$itemsText";

        if($reqPhone) sendWA($reqPhone, $msgHeader . "\n👉 Permintaan Anda ditolak.");
        $whPhones = array_unique(array_merge(getPhones($conn, 'Warehouse'), getPhones($conn, 'TeamLeader', 'Warehouse')));
        foreach($whPhones as $ph) sendWA($ph, $msgHeader . "\n👉 Permintaan GI ini telah ditolak di sistem.");

        sendJson(['success'=>true, 'message'=>'Request rejected.']);
    }
}

// --- 4. EXPORT DATA ---
if($action == 'exportData') {
    if(!checkAccessSession('export_data')) sendJson(['success'=>false, 'message'=>'Unauthorized.', 'code'=>403]);

    $type = $input['export_type'];
    $start = $conn->real_escape_string($input['start_date']) . " 00:00:00";
    $end = $conn->real_escape_string($input['end_date']) . " 23:59:59";
    
    $data = [];
    if ($type === 'GI') {
        $sql = "SELECT * FROM gis_requests WHERE created_at BETWEEN '$start' AND '$end' ORDER BY created_at ASC";
        $res = $conn->query($sql);
        if($res){ while($row = $res->fetch_assoc()) { $row['items'] = json_decode($row['items_json'], true); $data[] = $row; } }
    } elseif ($type === 'GR') {
        $sql = "SELECT * FROM gis_receives WHERE created_at BETWEEN '$start' AND '$end' ORDER BY created_at ASC";
        $res = $conn->query($sql);
        if($res){ while($row = $res->fetch_assoc()) { $row['items'] = json_decode($row['items_json'], true); $data[] = $row; } }
    } elseif ($type === 'INV') {
        $sql = "SELECT * FROM gis_inventory ORDER BY item_name ASC";
        $res = $conn->query($sql);
        if($res){ while($row = $res->fetch_assoc()) { $data[] = $row; } }
    }
    
    sendJson(['success'=>true, 'data'=>$data]);
}
?>