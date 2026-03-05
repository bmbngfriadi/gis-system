<?php
error_reporting(0);
ini_set('display_errors', 0);
ini_set('memory_limit', '256M'); 

session_start();
require 'db.php'; 
require 'helper.php';

if($conn) { $conn->set_charset("utf8mb4"); }
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header('Content-Type: application/json; charset=utf-8');

date_default_timezone_set('Asia/Jakarta'); 
$conn->query("SET time_zone = '+07:00'");

if(!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Sesi habis. Silakan login ulang.', 'code' => 401]);
    exit;
}

$serverUser = $_SESSION['user_data']['username'] ?? '';
$serverRole = $_SESSION['user_data']['role'] ?? '';
$serverDept = $_SESSION['user_data']['department'] ?? '';
$serverName = $_SESSION['user_data']['fullname'] ?? '';

// MIGRATION: Tambahkan kolom price & erp_no
$migrations = [
    "ALTER TABLE gis_requests ADD COLUMN erp_gi_no VARCHAR(100) NULL AFTER req_id",
    "ALTER TABLE gis_requests ADD COLUMN issue_photo VARCHAR(255) NULL",
    "ALTER TABLE gis_requests ADD COLUMN receive_photo VARCHAR(255) NULL",
    "ALTER TABLE gis_requests ADD COLUMN received_by VARCHAR(100) NULL",
    "ALTER TABLE gis_requests ADD COLUMN receive_time DATETIME NULL",
    "ALTER TABLE gis_receives ADD COLUMN erp_gr_no VARCHAR(100) NULL AFTER gr_id",
    "ALTER TABLE gis_receives ADD COLUMN gr_photo VARCHAR(255) NULL",
    "ALTER TABLE gis_inventory ADD COLUMN price DECIMAL(15,2) NOT NULL DEFAULT 0 AFTER uom"
];
foreach($migrations as $m) {
    try { $conn->query($m); } catch (Exception $e) {}
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$now = date('Y-m-d H:i:s');

function checkAccessSession($perm) {
    global $serverRole, $serverDept;
    if($serverRole === 'Administrator') return true;
    $rights = json_decode($_SESSION['user_data']['access_rights'] ?? '[]', true) ?: [];
    // Fallback hak akses jika role WH belum di-set di database
    if (empty($rights) && in_array($serverRole, ['Warehouse']) || ($serverRole === 'TeamLeader' && strtolower($serverDept) === 'warehouse')) {
        $rights = ['gi_submit', 'gr_submit', 'item_add', 'item_edit', 'stock_edit', 'export_data', 'price_add', 'price_edit', 'item_delete'];
    }
    return in_array($perm, $rights);
}

function safeSendJson($data) {
    echo json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
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
    $text = "📦 *DETAIL BARANG:*\n";
    $text .= "┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈\n";
    $grandTotal = 0;
    foreach($itemsArray as $it) {
        $code = $conn->real_escape_string($it['code']);
        $name = $it['name'];
        $qty = $it['qty'];
        $uom = $it['uom'] ?? '';
        $price = floatval($it['price'] ?? 0);
        $total = $price * intval($qty);
        $grandTotal += $total;

        $stkQ = $conn->query("SELECT stock FROM gis_inventory WHERE item_code='$code'")->fetch_assoc();
        $currentStock = $stkQ ? $stkQ['stock'] : 0;
        
        $text .= "🔸 *$code*\n";
        $text .= "   ▪ Nama: $name\n";
        $text .= "   ▪ Qty : *$qty $uom*\n";
        if ($price > 0) {
            $text .= "   ▪ Harga: Rp " . number_format($price, 0, ',', '.') . "\n";
            $text .= "   ▪ Total: Rp " . number_format($total, 0, ',', '.') . "\n";
        }
        $text .= "   ▪ Sisa: $currentStock $uom\n\n";
    }
    if ($grandTotal > 0) {
        $text .= "┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈\n";
        $text .= "💰 *GRAND TOTAL: Rp " . number_format($grandTotal, 0, ',', '.') . "*\n";
    }
    $text .= "┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈";
    return $text;
}

if($action == 'getInventory') {
    $res = $conn->query("SELECT * FROM gis_inventory ORDER BY item_name ASC");
    $data = [];
    if($res) { while($row = $res->fetch_assoc()) { $data[] = $row; } }
    safeSendJson($data);
}

// ------------------- HAPUS ITEM (DELETE) -------------------
if($action == 'deleteItem') {
    if(!checkAccessSession('item_delete')) safeSendJson(['success'=>false, 'message'=>'Unauthorized.', 'code'=>403]);
    
    $code = $conn->real_escape_string($input['item_code'] ?? '');
    if(empty($code)) safeSendJson(['success'=>false, 'message'=>'Kode item kosong.']);
    
    if($conn->query("DELETE FROM gis_inventory WHERE item_code='$code'")) {
        safeSendJson(['success'=>true, 'message'=>"Item $code berhasil dihapus."]);
    } else {
        safeSendJson(['success'=>false, 'message'=>$conn->error]);
    }
}

if($action == 'saveItem') {
    $isEdit = !empty($input['is_edit']) && $input['is_edit'] == '1';
    if(!$isEdit && !checkAccessSession('item_add')) safeSendJson(['success'=>false, 'message'=>'Access Denied', 'code'=>403]);
    if($isEdit && !checkAccessSession('item_edit') && !checkAccessSession('stock_edit')) safeSendJson(['success'=>false, 'message'=>'Access Denied', 'code'=>403]);
    
    $code = $conn->real_escape_string($input['item_code'] ?? '');
    $name = $conn->real_escape_string($input['item_name'] ?? '');
    $spec = $conn->real_escape_string($input['item_spec'] ?? '');
    $cat = $conn->real_escape_string($input['category'] ?? '');
    $uom = $conn->real_escape_string($input['uom'] ?? '');
    $stock = intval($input['stock'] ?? 0);
    $price = floatval($input['price'] ?? 0);
    
    $canEditInfo = checkAccessSession('item_edit');
    $canEditStock = checkAccessSession('stock_edit');
    
    // PEMISAHAN LOGIKA HAK AKSES HARGA
    $canEditPriceExisting = checkAccessSession('price_edit');
    $canAddPriceNew = checkAccessSession('price_add');

    if($isEdit) {
        $updates = [];
        if($canEditInfo) {
            $updates[] = "item_name='$name'"; $updates[] = "item_spec='$spec'";
            $updates[] = "category='$cat'"; $updates[] = "uom='$uom'";
        }
        if($canEditStock) { $updates[] = "stock=$stock"; }
        if($canEditPriceExisting) { $updates[] = "price=$price"; }
        $updates[] = "last_updated='$now'";
        
        $sql = "UPDATE gis_inventory SET " . implode(', ', $updates) . " WHERE item_code='$code'";
    } else {
        // Jika belum punya akses harga baru, paksa harga = 0
        $insertPrice = $canAddPriceNew ? $price : 0;
        
        $dupUpdates = [];
        $dupUpdates[] = "item_name='$name'"; $dupUpdates[] = "item_spec='$spec'"; 
        $dupUpdates[] = "category='$cat'"; $dupUpdates[] = "uom='$uom'"; $dupUpdates[] = "stock=$stock";
        if($canEditPriceExisting) { $dupUpdates[] = "price=$price"; } // Update duplicate (kategori existing)
        $dupUpdates[] = "last_updated='$now'";

        $sql = "INSERT INTO gis_inventory (item_code, item_name, item_spec, category, uom, price, stock, last_updated) 
                VALUES ('$code', '$name', '$spec', '$cat', '$uom', $insertPrice, $stock, '$now') 
                ON DUPLICATE KEY UPDATE " . implode(', ', $dupUpdates);
    }
            
    if($conn->query($sql)) safeSendJson(['success'=>true, 'message'=>'Item saved.']);
    else safeSendJson(['success'=>false, 'message'=>$conn->error]);
}

if($action == 'importItems') {
    if($serverRole !== 'Administrator') safeSendJson(['success'=>false, 'message'=>'Unauthorized.', 'code'=>403]);
    
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
            $price = floatval($it['price'] ?? 0);
            
            if(!empty($code) && !empty($name)) {
                $sql = "INSERT INTO gis_inventory (item_code, item_name, item_spec, category, uom, price, stock, last_updated) 
                        VALUES ('$code', '$name', '$spec', '$cat', '$uom', $price, $stock, '$now') 
                        ON DUPLICATE KEY UPDATE item_name='$name', item_spec='$spec', category='$cat', uom='$uom', price=$price, stock=$stock, last_updated='$now'";
                $conn->query($sql);
            }
        }
        $conn->commit();
        safeSendJson(['success'=>true, 'message'=>'Bulk import master item successful.']);
    } catch(Exception $e) {
        $conn->rollback();
        safeSendJson(['success'=>false, 'message'=>$e->getMessage()]);
    }
}

if($action == 'getReceives') {
    $res = $conn->query("SELECT * FROM gis_receives ORDER BY created_at DESC LIMIT 100");
    $data = [];
    if($res) {
        while($row = $res->fetch_assoc()) {
            $row['items'] = json_decode($row['items_json'], true) ?: [];
            $data[] = $row;
        }
    }
    safeSendJson($data);
}

if($action == 'submitGR') {
    if(!checkAccessSession('gr_submit')) safeSendJson(['success'=>false, 'message'=>'Unauthorized.', 'code'=>403]);

    $grId = "GR-" . time();
    $erpGrNo = $conn->real_escape_string($input['erp_gr_no'] ?? '');
    $remarks = $conn->real_escape_string($input['remarks']);
    $items = $input['items']; 
    
    $itemsJson = $conn->real_escape_string(json_encode($items));

    if(empty($input['photoBase64'])) safeSendJson(['success'=>false, 'message'=>'Bukti foto penerimaan wajib dilampirkan.']);
    $photoUrl = uploadGisPhoto($input['photoBase64'], "GR_" . preg_replace('/[^a-zA-Z0-9]/', '', $grId));
    if(!$photoUrl) safeSendJson(['success'=>false, 'message'=>'Gagal menyimpan foto GR.']);

    $conn->begin_transaction();
    try {
        $sql = "INSERT INTO gis_receives (gr_id, erp_gr_no, username, fullname, remarks, gr_photo, items_json, created_at) 
                VALUES ('$grId', '$erpGrNo', '$serverUser', '$serverName', '$remarks', '$photoUrl', '$itemsJson', '$now')";
        $conn->query($sql);

        // Karena GR menimpa harga item yang sudah ada
        $canEditPrice = checkAccessSession('price_edit');

        foreach($items as $it) {
            $ic = $conn->real_escape_string($it['code']); 
            $qty = intval($it['qty']);
            $price = floatval($it['price'] ?? 0);
            
            $updSql = "UPDATE gis_inventory SET stock = stock + $qty, last_updated = '$now'";
            if($canEditPrice && $price > 0) { $updSql .= ", price = $price"; } 
            $updSql .= " WHERE item_code = '$ic'";
            $conn->query($updSql);
        }
        $conn->commit();
        
        $itemsText = buildItemListText($conn, $items);
        
        $msgHeader = "📥 *GOOD RECEIVE (BARANG MASUK)* 📥\n";
        $msgHeader .= "┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈\n";
        $msgHeader .= "🔖 *ID GR* : $grId\n";
        $msgHeader .= "🧾 *No ERP* : $erpGrNo\n";
        $msgHeader .= "👤 *Penerima* : $serverName\n";
        $msgHeader .= "📝 *Catatan* : $remarks\n";
        $msgHeader .= "┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈\n\n";
        $msgHeader .= $itemsText;
        
        $whPhones = array_unique(array_merge((array)getPhones($conn, 'Warehouse'), (array)getPhones($conn, 'TeamLeader', 'Warehouse'), (array)getPhones($conn, 'Administrator')));
        foreach($whPhones as $ph) sendWA($ph, $msgHeader . "\n\n💡 *Info:* Stok Master telah berhasil ditambahkan secara otomatis.");

        safeSendJson(['success'=>true, 'message'=>'Good Receive berhasil diproses. Stok bertambah.']);
    } catch (Exception $e) { 
        $conn->rollback(); 
        safeSendJson(['success'=>false, 'message'=>$e->getMessage()]); 
    }
}

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
            $row['items'] = json_decode($row['items_json'], true) ?: [];
            $data[] = $row;
        }
    }
    safeSendJson($data);
}

if($action == 'submitRequest') {
    if(!checkAccessSession('gi_submit')) safeSendJson(['success'=>false, 'message'=>'Unauthorized.', 'code'=>403]);

    $reqId = "GIF-" . time(); 
    $sec = $conn->real_escape_string($input['section']); 
    $purpose = $conn->real_escape_string($input['purpose']);
    $items = $input['items']; 
    
    foreach($items as $it) {
        $ic = $conn->real_escape_string($it['code']);
        $reqQty = intval($it['qty']);
        $cc = trim((string)($it['cost_center'] ?? ''));
        if(empty($cc)) safeSendJson(['success'=>false, 'message'=>"Cost Center WAJIB diisi."]);

        $cek = $conn->query("SELECT stock, item_name FROM gis_inventory WHERE item_code = '$ic'")->fetch_assoc();
        if(!$cek || $cek['stock'] < $reqQty) safeSendJson(['success'=>false, 'message'=>"Stock tidak cukup untuk $ic"]);
    }

    $itemsJson = $conn->real_escape_string(json_encode($items));
    $sql = "INSERT INTO gis_requests (req_id, username, fullname, department, section, purpose, items_json, created_at) 
            VALUES ('$reqId', '$serverUser', '$serverName', '$serverDept', '$sec', '$purpose', '$itemsJson', '$now')";
            
    if($conn->query($sql)) {
        $itemsText = buildItemListText($conn, $items);
        
        $msgHeader = "🚨 *NEW GOOD ISSUE REQUEST* 🚨\n";
        $msgHeader .= "┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈\n";
        $msgHeader .= "🔖 *ID GIF* : $reqId\n";
        $msgHeader .= "👤 *Pemohon* : $serverName\n";
        $msgHeader .= "🏢 *Dept/Sec* : $serverDept / $sec\n";
        $msgHeader .= "📝 *Keperluan*: $purpose\n";
        $msgHeader .= "┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈\n\n";
        $msgHeader .= $itemsText;

        $headPhones = (array)getPhones($conn, ['SectionHead', 'TeamLeader'], $serverDept);
        foreach($headPhones as $ph) sendWA($ph, $msgHeader . "\n\n💡 *Action:* Silakan login ke sistem untuk melakukan Approval.");
        
        $userPhone = getUserPhone($conn, $serverUser);
        if($userPhone) sendWA($userPhone, $msgHeader . "\n\n💡 *Status:* Menunggu persetujuan Dept Head.");

        $whPhones = array_unique(array_merge((array)getPhones($conn, 'Warehouse'), (array)getPhones($conn, 'TeamLeader', 'Warehouse')));
        foreach($whPhones as $ph) sendWA($ph, $msgHeader . "\n\n💡 *Status:* Menunggu persetujuan Dept Head.");

        safeSendJson(['success'=>true, 'message'=>'Request submitted.']);
    } else safeSendJson(['success'=>false, 'message'=>$conn->error]);
}

if($action == 'editRequest') {
    $reqId = $conn->real_escape_string($input['reqId']);
    
    $cekReq = $conn->query("SELECT status, username, department FROM gis_requests WHERE req_id='$reqId'")->fetch_assoc();
    if(!$cekReq || $cekReq['username'] !== $serverUser || $cekReq['status'] !== 'Pending Head') {
        safeSendJson(['success'=>false, 'message'=>'Data sudah diproses atau bukan milik Anda.', 'code'=>403]);
    }

    $sec = $conn->real_escape_string($input['section']);
    $purpose = $conn->real_escape_string($input['purpose']);
    $items = $input['items'];
    
    foreach($items as $it) {
        $ic = $conn->real_escape_string($it['code']);
        $reqQty = intval($it['qty']);
        if(empty(trim((string)($it['cost_center'] ?? '')))) safeSendJson(['success'=>false, 'message'=>"Cost Center WAJIB diisi."]);

        $cek = $conn->query("SELECT stock FROM gis_inventory WHERE item_code = '$ic'")->fetch_assoc();
        if(!$cek || $cek['stock'] < $reqQty) safeSendJson(['success'=>false, 'message'=>"Stock tidak cukup"]);
    }

    $itemsJson = $conn->real_escape_string(json_encode($items));
    $sql = "UPDATE gis_requests SET section='$sec', purpose='$purpose', items_json='$itemsJson' WHERE req_id='$reqId'";
    
    if($conn->query($sql)) {
        $itemsText = buildItemListText($conn, $items);
        
        $msgHeader = "✏️ *GOOD ISSUE UPDATED* ✏️\n";
        $msgHeader .= "┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈\n";
        $msgHeader .= "🔖 *ID GIF* : $reqId\n";
        $msgHeader .= "👤 *Diubah By*: $serverName\n";
        $msgHeader .= "📝 *Keperluan*: $purpose\n";
        $msgHeader .= "┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈\n\n";
        $msgHeader .= $itemsText;
        
        $headPhones = (array)getPhones($conn, ['SectionHead', 'TeamLeader'], $cekReq['department']);
        foreach($headPhones as $ph) sendWA($ph, $msgHeader . "\n\n💡 *Action:* Form telah diubah oleh User. Silakan login untuk Approve.");

        $userPhone = getUserPhone($conn, $serverUser);
        if($userPhone) sendWA($userPhone, $msgHeader . "\n\n💡 *Status:* Form berhasil diubah, menunggu persetujuan Dept Head.");

        $whPhones = array_unique(array_merge((array)getPhones($conn, 'Warehouse'), (array)getPhones($conn, 'TeamLeader', 'Warehouse')));
        foreach($whPhones as $ph) sendWA($ph, $msgHeader . "\n\n💡 *Status:* Form telah diubah User. Menunggu persetujuan Dept Head.");

        safeSendJson(['success'=>true, 'message'=>'Request updated successfully.']);
    } else safeSendJson(['success'=>false, 'message'=>$conn->error]);
}

if($action == 'cancelRequest') {
    $reqId = $conn->real_escape_string($input['reqId']);
    $cekReq = $conn->query("SELECT status, department, section, purpose, items_json FROM gis_requests WHERE req_id='$reqId' AND username='$serverUser'")->fetch_assoc();

    if(!$cekReq || $cekReq['status'] !== 'Pending Head') {
        safeSendJson(['success'=>false, 'message'=>'Data tidak dapat dibatalkan.', 'code'=>403]);
    }

    if($conn->query("UPDATE gis_requests SET status='Cancelled' WHERE req_id='$reqId'")) {
        $items = json_decode($cekReq['items_json'], true) ?: [];
        $itemsText = buildItemListText($conn, $items);
        
        $msgHeader = "🚫 *GOOD ISSUE CANCELLED* 🚫\n";
        $msgHeader .= "┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈\n";
        $msgHeader .= "🔖 *ID GIF* : $reqId\n";
        $msgHeader .= "👤 *Batal By* : $serverName\n";
        $msgHeader .= "🏢 *Dept/Sec* : {$cekReq['department']} / {$cekReq['section']}\n";
        $msgHeader .= "┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈\n\n";
        $msgHeader .= $itemsText;

        $userPhone = getUserPhone($conn, $serverUser);
        if($userPhone) sendWA($userPhone, $msgHeader . "\n\n💡 *Info:* Anda telah berhasil membatalkan permintaan ini.");

        $headPhones = (array)getPhones($conn, ['SectionHead', 'TeamLeader'], $cekReq['department']);
        foreach($headPhones as $ph) sendWA($ph, $msgHeader . "\n\n💡 *Info:* Permintaan dibatalkan oleh pemohon. Harap abaikan pengajuan ini.");

        $whPhones = array_unique(array_merge((array)getPhones($conn, 'Warehouse'), (array)getPhones($conn, 'TeamLeader', 'Warehouse')));
        foreach($whPhones as $ph) sendWA($ph, $msgHeader . "\n\n💡 *Info:* Permintaan telah dibatalkan oleh User.");

        safeSendJson(['success'=>true, 'message'=>'Request cancelled successfully.']);
    } else {
        safeSendJson(['success'=>false, 'message'=>$conn->error]);
    }
}

if($action == 'updateStatus') {
    $id = $conn->real_escape_string($input['reqId']); 
    $act = $input['act']; 
    $reason = $conn->real_escape_string($input['reason'] ?? '');
    
    $req = $conn->query("SELECT * FROM gis_requests WHERE req_id = '$id'")->fetch_assoc();
    if(!$req) safeSendJson(['success'=>false, 'message'=>'Data not found']);
    
    $reqPhone = getUserPhone($conn, $req['username']);
    $items = json_decode($req['items_json'], true) ?: [];

    if($act == 'approve') {
        if($req['status'] == 'Pending Head') {
            $conn->query("UPDATE gis_requests SET status='Pending Warehouse', app_head='Approved by $serverName', head_time='$now' WHERE req_id='$id'");
            
            $itemsText = buildItemListText($conn, $items);
            $msgHeader = "✅ *GI APPROVED BY HEAD* ✅\n";
            $msgHeader .= "┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈\n";
            $msgHeader .= "🔖 *ID GIF* : $id\n";
            $msgHeader .= "👤 *Approve By*: $serverName\n";
            $msgHeader .= "┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈\n\n";
            $msgHeader .= $itemsText;

            $whPhones = array_unique(array_merge((array)getPhones($conn, 'Warehouse'), (array)getPhones($conn, 'TeamLeader', 'Warehouse')));
            foreach($whPhones as $ph) sendWA($ph, $msgHeader . "\n\n💡 *Action:* Silakan siapkan barang fisik dan lakukan pengeluaran (Issue) di sistem.");
            if($reqPhone) sendWA($reqPhone, $msgHeader . "\n\n💡 *Status:* Permintaan Anda telah disetujui Head dan saat ini sedang disiapkan oleh pihak Gudang.");
            
            safeSendJson(['success'=>true, 'message'=>'Approved by Head.']);
        }
    }
    elseif($act == 'issue') {
        $isWarehouseAdmin = in_array($serverRole, ['Administrator', 'Warehouse']) || ($serverRole === 'TeamLeader' && strtolower($serverDept) === 'warehouse');
        if($req['status'] == 'Pending Warehouse' && $isWarehouseAdmin) {
            if(empty($input['photoBase64'])) safeSendJson(['success'=>false, 'message'=>'Bukti foto wajib dilampirkan.']);
            
            $photoUrl = uploadGisPhoto($input['photoBase64'], "ISSUE_" . preg_replace('/[^a-zA-Z0-9]/', '', $id));
            if(!$photoUrl) safeSendJson(['success'=>false, 'message'=>'Gagal upload foto.']);

            $conn->begin_transaction();
            try {
                foreach($items as $it) {
                    $ic = $conn->real_escape_string($it['code']); $qty = intval($it['qty']);
                    $conn->query("UPDATE gis_inventory SET stock = stock - $qty, last_updated = '$now' WHERE item_code = '$ic'");
                }
                
                $conn->query("UPDATE gis_requests SET status='Pending Receive', app_wh='Issued by $serverName', wh_time='$now', issue_photo='$photoUrl' WHERE req_id='$id'");
                $conn->commit();
                
                $itemsText = buildItemListText($conn, $items); 
                $msgHeader = "🚚 *GI ISSUED BY WAREHOUSE* 🚚\n";
                $msgHeader .= "┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈\n";
                $msgHeader .= "🔖 *ID GIF* : $id\n";
                $msgHeader .= "👤 *Issued By*: $serverName\n";
                $msgHeader .= "┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈\n\n";
                $msgHeader .= $itemsText;

                if($reqPhone) sendWA($reqPhone, $msgHeader . "\n\n💡 *Action:* Barang fisik sudah disiapkan/dikeluarkan dari gudang. Silakan ambil barang dan lakukan Konfirmasi Penerimaan (Receive) di sistem.");
                
                $whPhones = array_unique(array_merge((array)getPhones($conn, 'Warehouse'), (array)getPhones($conn, 'TeamLeader', 'Warehouse')));
                foreach($whPhones as $ph) sendWA($ph, $msgHeader . "\n\n💡 *Status:* Menunggu konfirmasi penerimaan dari User bersangkutan.");
                
                safeSendJson(['success'=>true, 'message'=>'Barang berhasil di-Issue. Menunggu konfirmasi penerimaan user.']);
            } catch (Exception $e) { $conn->rollback(); safeSendJson(['success'=>false, 'message'=>$e->getMessage()]); }
        }
    }
    elseif($act == 'receive') {
        if($req['status'] == 'Pending Receive' && $req['username'] == $serverUser) {
            if(empty($input['photoBase64'])) safeSendJson(['success'=>false, 'message'=>'Bukti foto penerimaan wajib dilampirkan.']);
            
            $photoUrl = uploadGisPhoto($input['photoBase64'], "RECV_" . preg_replace('/[^a-zA-Z0-9]/', '', $id));
            if(!$photoUrl) safeSendJson(['success'=>false, 'message'=>'Gagal upload foto.']);

            $sql = "UPDATE gis_requests SET status='Pending No GI (ERP)', received_by='$serverName', receive_time='$now', receive_photo='$photoUrl' WHERE req_id='$id'";
            if($conn->query($sql)) {
                
                $itemsText = buildItemListText($conn, $items);
                $msgHeader = "🏁 *GI RECEIVED BY USER* 🏁\n";
                $msgHeader .= "┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈\n";
                $msgHeader .= "🔖 *ID GIF* : $id\n";
                $msgHeader .= "👤 *Diterima By*: $serverName\n";
                $msgHeader .= "┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈\n\n";
                $msgHeader .= $itemsText;

                if($reqPhone) sendWA($reqPhone, $msgHeader . "\n\n💡 *Status:* Barang telah Anda konfirmasi. Saat ini menunggu pihak Warehouse menginput Nomor GI ERP.");

                $whPhones = array_unique(array_merge((array)getPhones($conn, 'Warehouse'), (array)getPhones($conn, 'TeamLeader', 'Warehouse')));
                foreach($whPhones as $ph) sendWA($ph, $msgHeader . "\n\n💡 *Action:* User telah menerima fisik barang. *SILAKAN INPUT NOMOR GI ERP* di sistem GIS untuk menyelesaikan (Complete) transaksi ini.");

                safeSendJson(['success'=>true, 'message'=>'Barang berhasil diterima. Menunggu input Nomor GI ERP dari Gudang.']);
            } else {
                safeSendJson(['success'=>false, 'message'=>$conn->error]);
            }
        } else {
            safeSendJson(['success'=>false, 'message'=>'Unauthorized to receive.']);
        }
    }
    elseif($act == 'complete_erp') {
        $isWarehouseAdmin = in_array($serverRole, ['Administrator', 'Warehouse']) || ($serverRole === 'TeamLeader' && strtolower($serverDept) === 'warehouse');
        if($req['status'] == 'Pending No GI (ERP)' && $isWarehouseAdmin) {
            $erpNo = $conn->real_escape_string($input['erp_gi_no'] ?? '');
            if(empty($erpNo)) safeSendJson(['success'=>false, 'message'=>'Nomor GI ERP tidak boleh kosong.']);

            $sql = "UPDATE gis_requests SET status='Completed', erp_gi_no='$erpNo' WHERE req_id='$id'";
            if($conn->query($sql)) {
                $msgHeader = "🎉 *GI COMPLETED (FULL)* 🎉\n";
                $msgHeader .= "┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈\n";
                $msgHeader .= "🔖 *ID GIF* : $id\n";
                $msgHeader .= "🧾 *No ERP* : $erpNo\n";
                $msgHeader .= "👤 *Admin WH* : $serverName\n";
                $msgHeader .= "┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈\n";

                if($reqPhone) sendWA($reqPhone, $msgHeader . "\n\n💡 *Info:* Proses pengeluaran barang dan pendataan di sistem ERP telah selesai sepenuhnya. Terima kasih.");
                
                safeSendJson(['success'=>true, 'message'=>'Nomor GI ERP berhasil disimpan. Status transaksi selesai.']);
            } else {
                safeSendJson(['success'=>false, 'message'=>$conn->error]);
            }
        } else {
            safeSendJson(['success'=>false, 'message'=>'Akses ditolak atau status tidak valid.']);
        }
    }
    elseif($act == 'reject') {
        $sql = "UPDATE gis_requests SET status='Rejected', reject_reason='$reason'";
        if($req['status'] == 'Pending Head') $sql .= ", app_head='Rejected by $serverName', head_time='$now'";
        if($req['status'] == 'Pending Warehouse') $sql .= ", app_wh='Rejected by $serverName', wh_time='$now'";
        $sql .= " WHERE req_id='$id'";
        
        $conn->query($sql);
        
        $itemsText = buildItemListText($conn, $items);
        $msgHeader = "❌ *GI REJECTED* ❌\n";
        $msgHeader .= "┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈\n";
        $msgHeader .= "🔖 *ID GIF* : $id\n";
        $msgHeader .= "👤 *Ditolak By*: $serverName\n";
        $msgHeader .= "💬 *Alasan* : $reason\n";
        $msgHeader .= "┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈\n\n";
        $msgHeader .= $itemsText;

        if($reqPhone) sendWA($reqPhone, $msgHeader . "\n\n💡 *Info:* Mohon maaf, permintaan Good Issue Anda telah ditolak.");
        $whPhones = array_unique(array_merge((array)getPhones($conn, 'Warehouse'), (array)getPhones($conn, 'TeamLeader', 'Warehouse')));
        foreach($whPhones as $ph) sendWA($ph, $msgHeader . "\n\n💡 *Info:* Permintaan GI ini telah ditolak dan tidak akan diproses lebih lanjut.");

        safeSendJson(['success'=>true, 'message'=>'Request rejected.']);
    }
}

if($action == 'exportData') {
    if(!checkAccessSession('export_data')) safeSendJson(['success'=>false, 'message'=>'Unauthorized.', 'code'=>403]);

    $type = $input['export_type'] ?? '';
    $start_date = $input['start_date'] ?? '';
    $end_date = $input['end_date'] ?? '';
    $start = $conn->real_escape_string($start_date) . " 00:00:00";
    $end = $conn->real_escape_string($end_date) . " 23:59:59";
    
    $data = [];
    if ($type === 'GI') {
        $sql = "SELECT * FROM gis_requests WHERE created_at BETWEEN '$start' AND '$end' ORDER BY created_at ASC";
        $res = $conn->query($sql);
        if($res){ while($row = $res->fetch_assoc()) { $row['items'] = json_decode($row['items_json'], true) ?: []; $data[] = $row; } }
    } elseif ($type === 'GR') {
        $sql = "SELECT * FROM gis_receives WHERE created_at BETWEEN '$start' AND '$end' ORDER BY created_at ASC";
        $res = $conn->query($sql);
        if($res){ while($row = $res->fetch_assoc()) { $row['items'] = json_decode($row['items_json'], true) ?: []; $data[] = $row; } }
    } elseif ($type === 'INV') {
        $sql = "SELECT * FROM gis_inventory ORDER BY item_name ASC";
        $res = $conn->query($sql);
        if($res){ while($row = $res->fetch_assoc()) { $data[] = $row; } }
    }
    safeSendJson(['success'=>true, 'data'=>$data]);
}
?>