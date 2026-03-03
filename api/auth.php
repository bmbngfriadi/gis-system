<?php
session_start(); // MEMULAI SESI SERVER TERENKRIPSI
require 'db.php';
require 'helper.php';

// Security Headers
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

if ($action == 'login') {
    $u = $conn->real_escape_string($input['username']);
    $p = $input['password'];

    $res = $conn->query("SELECT * FROM users WHERE username = '$u'");
    if ($res && $res->num_rows > 0) {
        $user = $res->fetch_assoc();
        if (password_verify($p, $user['password'])) {
            unset($user['password']); // Jangan pernah kirim password ke frontend
            
            // SIMPAN DATA KE SESSION SERVER (TIDAK BISA DIHACK DARI LUAR)
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_data'] = $user;
            
            sendJson(['success' => true, 'message' => 'Login success', 'user' => $user]);
        } else {
            sendJson(['success' => false, 'message' => 'Password salah']);
        }
    } else {
        sendJson(['success' => false, 'message' => 'Username tidak ditemukan']);
    }
}

if ($action == 'checkSession') {
    if(isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
        sendJson(['success' => true, 'user' => $_SESSION['user_data']]);
    } else {
        sendJson(['success' => false, 'code' => 401]);
    }
}

if ($action == 'logout') {
    session_destroy();
    sendJson(['success' => true]);
}

if ($action == 'requestReset') {
    $u = $conn->real_escape_string($input['username']);
    $res = $conn->query("SELECT phone FROM users WHERE username = '$u'");
    if ($res && $res->num_rows > 0) {
        $phone = $res->fetch_assoc()['phone'];
        if (!$phone) sendJson(['success' => false, 'message' => 'Nomor WA tidak terdaftar untuk user ini.']);
        
        $token = bin2hex(random_bytes(16));
        $conn->query("UPDATE users SET reset_token = '$token' WHERE username = '$u'");
        
        $domain = $_SERVER['HTTP_HOST'];
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $resetLink = $protocol . "://" . $domain . "/reset.php?token=" . $token;
        
        $msg = "🔐 *Permintaan Reset Password*\n\nKlik link berikut untuk membuat password baru Anda:\n$resetLink\n\nJika Anda tidak meminta ini, abaikan pesan ini.";
        sendWA($phone, $msg);
        
        sendJson(['success' => true, 'message' => 'Link reset password telah dikirim ke WA Anda.']);
    } else {
        sendJson(['success' => false, 'message' => 'Username tidak ditemukan.']);
    }
}

if ($action == 'confirmReset') {
    $token = $conn->real_escape_string($input['token']);
    $newPass = password_hash($input['newPassword'], PASSWORD_DEFAULT);
    
    $res = $conn->query("SELECT id FROM users WHERE reset_token = '$token'");
    if ($res && $res->num_rows > 0) {
        $conn->query("UPDATE users SET password = '$newPass', reset_token = NULL WHERE reset_token = '$token'");
        sendJson(['success' => true, 'message' => 'Password berhasil diperbarui.']);
    } else {
        sendJson(['success' => false, 'message' => 'Token tidak valid atau sudah kadaluarsa.']);
    }
}
?>