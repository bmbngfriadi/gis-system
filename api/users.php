<?php
error_reporting(0);
ini_set('display_errors', 0);

session_start();
require 'db.php';
require 'helper.php';

if($conn) { $conn->set_charset("utf8mb4"); }

header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header('Content-Type: application/json; charset=utf-8');

function safeSendJson($data) {
    echo json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

if(!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    safeSendJson(['success' => false, 'message' => 'Sesi habis.', 'code' => 401]);
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$isAdmin = ($_SESSION['user_data']['role'] === 'Administrator');

if ($action == 'getAllUsers') {
    if(!$isAdmin) safeSendJson(['success'=>false, 'message'=>'Access Denied', 'code'=>403]);
    $res = $conn->query("SELECT id, username, fullname, nik, department, role, phone, access_rights FROM users ORDER BY fullname ASC");
    $data = [];
    if($res) { while($row = $res->fetch_assoc()) { $data[] = $row; } }
    safeSendJson($data);
}

if ($action == 'saveUser') {
    if(!$isAdmin) safeSendJson(['success'=>false, 'message'=>'Access Denied', 'code'=>403]);
    
    $isEdit = $input['isEdit']; $data = $input['data'];
    $u = $conn->real_escape_string($data['username']); $f = $conn->real_escape_string($data['fullname']);
    $n = $conn->real_escape_string($data['nik']); $d = $conn->real_escape_string($data['department']);
    $r = $conn->real_escape_string($data['role']); $ph = $conn->real_escape_string($data['phone']);
    $acc = isset($data['access_rights']) ? $conn->real_escape_string($data['access_rights']) : '[]';
    $p = $data['password'] ?? '';

    if (!$isEdit) {
        if ($conn->query("SELECT id FROM users WHERE username = '$u'")->num_rows > 0) safeSendJson(['success' => false, 'message' => 'Username exists!']);
        $hashedPass = password_hash($p, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, password, fullname, nik, department, role, phone, access_rights) VALUES ('$u', '$hashedPass', '$f', '$n', '$d', '$r', '$ph', '$acc')";
    } else {
        $sql = "UPDATE users SET fullname='$f', nik='$n', department='$d', role='$r', phone='$ph', access_rights='$acc'";
        if (!empty($p)) { $sql .= ", password='".password_hash($p, PASSWORD_DEFAULT)."'"; }
        $sql .= " WHERE username='$u'";
    }
    if ($conn->query($sql)) safeSendJson(['success' => true, 'message' => 'User saved.']);
    else safeSendJson(['success' => false, 'message' => $conn->error]);
}

if ($action == 'updateProfile') {
    $sessionUsername = $_SESSION['user_data']['username'];
    $reqUsername = $input['username'];
    
    if ($sessionUsername !== $reqUsername && !$isAdmin) {
        safeSendJson(['success'=>false, 'message'=>'Akses ditolak!', 'code'=>403]);
    }
    
    $phone = $conn->real_escape_string($input['phone']);
    $newPass = $input['newPass'] ?? '';
    $sql = "UPDATE users SET phone = '$phone'";
    if (!empty($newPass)) { $sql .= ", password = '".password_hash($newPass, PASSWORD_DEFAULT)."'"; }
    $sql .= " WHERE username = '$reqUsername'";
    
    if ($conn->query($sql)) {
        if($sessionUsername === $reqUsername) { $_SESSION['user_data']['phone'] = $phone; }
        safeSendJson(['success' => true, 'message' => 'Profile updated successfully']);
    } else {
        safeSendJson(['success' => false, 'message' => 'Update failed: ' . $conn->error]);
    }
}

if ($action == 'deleteUser') {
    if(!$isAdmin) safeSendJson(['success'=>false, 'message'=>'Access Denied', 'code'=>403]);
    $u = $conn->real_escape_string($input['username']);
    if (strtolower($u) == 'admin') safeSendJson(['success' => false, 'message' => 'Cannot delete Admin.']);
    if ($conn->query("DELETE FROM users WHERE username = '$u'")) safeSendJson(['success' => true, 'message' => 'User deleted.']);
    else safeSendJson(['success' => false, 'message' => $conn->error]);
}
?>