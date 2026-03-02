<?php
require 'db.php';
require 'helper.php';

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$baseUrl = "$protocol://{$_SERVER['HTTP_HOST']}" . str_replace('/api', '', dirname($_SERVER['PHP_SELF']));

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

if ($action == 'login') {
    $u = $conn->real_escape_string($input['username']);
    $p = $input['password'];
    $res = $conn->query("SELECT * FROM users WHERE username = '$u'");

    if ($res->num_rows > 0) {
        $user = $res->fetch_assoc();
        if (password_verify($p, $user['password']) || $user['password'] === $p) {
            if($user['password'] === $p) $conn->query("UPDATE users SET password = '".password_hash($p, PASSWORD_DEFAULT)."' WHERE id = ".$user['id']);
            unset($user['password'], $user['reset_token'], $user['reset_expiry']);
            sendJson(['success' => true, 'user' => $user]);
        }
    }
    sendJson(['success' => false, 'message' => 'Username atau Password Salah']);
}

if ($action == 'requestReset') {
    $u = $conn->real_escape_string($input['username']);
    $res = $conn->query("SELECT * FROM users WHERE username = '$u'");
    if ($res->num_rows > 0) {
        $user = $res->fetch_assoc();
        if (empty($user['phone'])) sendJson(['success' => false, 'message' => 'User tidak memiliki nomor WA.']);
        $token = bin2hex(random_bytes(16));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $conn->query("UPDATE users SET reset_token = '$token', reset_expiry = '$expiry' WHERE username = '$u'");
        $resetLink = "$baseUrl/reset.php?token=$token";
        
        $msg = "🔐 *GIS RESET PASSWORD*\n\nHalo {$user['fullname']},\nKlik link di bawah untuk reset password:\n$resetLink\n\n_Berlaku 1 jam._";
        sendWA($user['phone'], $msg);
        sendJson(['success' => true, 'message' => 'Link reset dikirim ke WhatsApp.']);
    } else { sendJson(['success' => false, 'message' => 'Username tidak ditemukan.']); }
}

if ($action == 'confirmReset') {
    $token = $conn->real_escape_string($input['token']);
    $newPass = password_hash($input['newPassword'], PASSWORD_DEFAULT);
    $now = date('Y-m-d H:i:s');
    
    $res = $conn->query("SELECT id FROM users WHERE reset_token = '$token' AND reset_expiry > '$now'");
    if ($res->num_rows > 0) {
        $conn->query("UPDATE users SET password = '$newPass', reset_token = NULL, reset_expiry = NULL WHERE reset_token = '$token'");
        sendJson(['success' => true, 'message' => 'Password berhasil diubah.']);
    } else { sendJson(['success' => false, 'message' => 'Link tidak valid/kadaluarsa.']); }
}
?>