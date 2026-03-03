<?php
session_start();
require 'db.php';
require 'helper.php';

header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");

// SECURITY CHECK: PASTIKAN YANG MENGAKSES API ADALAH USER VALID
if(!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    sendJson(['success' => false, 'message' => 'Unauthorized Access', 'code' => 401]);
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

// SECURITY CHECK: HANYA ADMIN YANG BOLEH KELOLA USER LAIN
$isAdmin = ($_SESSION['user_data']['role'] === 'Administrator');

if ($action == 'getAllUsers') {
    if(!$isAdmin) sendJson(['success'=>false, 'message'=>'Access Denied', 'code'=>403]);
    $res = $conn->query("SELECT id, username, fullname, nik, department, role, phone, access_rights FROM users ORDER BY fullname ASC");
    $data = [];
    if($res) { while($row = $res->fetch_assoc()) { $data[] = $row; } }
    sendJson($data);
}

if ($action == 'saveUser') {
    if(!$isAdmin) sendJson(['success'=>false, 'message'=>'Access Denied', 'code'=>403]);
    
    $isEdit = $input['isEdit']; $data = $input['data'];
    $u = $conn->real_escape_string($data['username']); $f = $conn->real_escape_string($data['fullname']);
    $n = $conn->real_escape_string($data['nik']); $d = $conn->real_escape_string($data['department']);
    $r = $conn->real_escape_string($data['role']); $ph = $conn->real_escape_string($data['phone']);
    $acc = isset($data['access_rights']) ? $conn->real_escape_string($data['access_rights']) : '[]';
    $p = $data['password'];

    if (!$isEdit) {
        if ($conn->query("SELECT id FROM users WHERE username = '$u'")->num_rows > 0) sendJson(['success' => false, 'message' => 'Username exists!']);
        $hashedPass = password_hash($p, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, password, fullname, nik, department, role, phone, access_rights) VALUES ('$u', '$hashedPass', '$f', '$n', '$d', '$r', '$ph', '$acc')";
    } else {
        $sql = "UPDATE users SET fullname='$f', nik='$n', department='$d', role='$r', phone='$ph', access_rights='$acc'";
        if (!empty($p)) { $sql .= ", password='".password_hash($p, PASSWORD_DEFAULT)."'"; }
        $sql .= " WHERE username='$u'";
    }
    if ($conn->query($sql)) sendJson(['success' => true, 'message' => 'User saved.']);
    else sendJson(['success' => false, 'message' => $conn->error]);
}

if ($action == 'updateProfile') {
    // SECURITY: Hanya boleh mengubah profil miliknya sendiri berdasarkan SESSION SERVER
    $sessionUsername = $_SESSION['user_data']['username'];
    $reqUsername = $input['username'];
    
    if ($sessionUsername !== $reqUsername && !$isAdmin) {
        sendJson(['success'=>false, 'message'=>'Anda tidak berhak merubah profil orang lain!', 'code'=>403]);
    }
    
    $phone = $conn->real_escape_string($input['phone']);
    $newPass = $input['newPass'] ?? '';
    $sql = "UPDATE users SET phone = '$phone'";
    if (!empty($newPass)) { $sql .= ", password = '".password_hash($newPass, PASSWORD_DEFAULT)."'"; }
    $sql .= " WHERE username = '$reqUsername'";
    
    if ($conn->query($sql)) {
        if($sessionUsername === $reqUsername) { $_SESSION['user_data']['phone'] = $phone; }
        sendJson(['success' => true, 'message' => 'Profile updated successfully']);
    } else {
        sendJson(['success' => false, 'message' => 'Update failed: ' . $conn->error]);
    }
}

if ($action == 'deleteUser') {
    if(!$isAdmin) sendJson(['success'=>false, 'message'=>'Access Denied', 'code'=>403]);
    $u = $conn->real_escape_string($input['username']);
    if (strtolower($u) == 'admin') sendJson(['success' => false, 'message' => 'Cannot delete Admin.']);
    if ($conn->query("DELETE FROM users WHERE username = '$u'")) sendJson(['success' => true, 'message' => 'User deleted.']);
    else sendJson(['success' => false, 'message' => $conn->error]);
}
?>