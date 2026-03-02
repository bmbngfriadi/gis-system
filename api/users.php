<?php
require 'db.php';
require 'helper.php';

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

if ($action == 'getAllUsers') {
    $res = $conn->query("SELECT id, username, fullname, nik, department, role, phone, access_rights FROM users ORDER BY fullname ASC");
    sendJson($res->fetch_all(MYSQLI_ASSOC));
}

if ($action == 'getOptions') {
    $deptRes = $conn->query("SELECT DISTINCT department FROM users WHERE department != '' ORDER BY department");
    $roleRes = $conn->query("SELECT DISTINCT role FROM users WHERE role != '' ORDER BY role");
    $depts = []; $roles = [];
    while($r = $deptRes->fetch_assoc()) $depts[] = $r['department'];
    while($r = $roleRes->fetch_assoc()) $roles[] = $r['role'];
    sendJson(['departments' => $depts, 'roles' => $roles]);
}

if ($action == 'saveUser') {
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
    $username = $input['username'];
    $phone = $conn->real_escape_string($input['phone']);
    $newPass = $input['newPass'] ?? '';
    $sql = "UPDATE users SET phone = '$phone'";
    if (!empty($newPass)) { $sql .= ", password = '".password_hash($newPass, PASSWORD_DEFAULT)."'"; }
    $sql .= " WHERE username = '$username'";
    if ($conn->query($sql)) sendJson(['success' => true, 'message' => 'Profile updated successfully']);
    else sendJson(['success' => false, 'message' => 'Update failed: ' . $conn->error]);
}

if ($action == 'deleteUser') {
    $u = $conn->real_escape_string($input['username']);
    if (strtolower($u) == 'admin') sendJson(['success' => false, 'message' => 'Cannot delete Admin.']);
    if ($conn->query("DELETE FROM users WHERE username = '$u'")) sendJson(['success' => true, 'message' => 'User deleted.']);
    else sendJson(['success' => false, 'message' => $conn->error]);
}
?>