<?php
define('WA_FOOTER', "\n\n--------------------------------\n🤖 _System Notification. Do Not Reply._");

function sendJson($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function writeLog($msg) { file_put_contents('debug_wa.txt', "[" . date('Y-m-d H:i:s') . "] " . $msg . PHP_EOL, FILE_APPEND); }

function sendWA($target, $message) {
    if (empty($target)) return false;
    $apiKey = "jkPPFevPkw4DBtPQMsDn"; // API KEY FONNTE ANDA
    $target = preg_replace('/[^0-9]/', '', $target);
    if (substr($target, 0, 1) == '0') $target = '62' . substr($target, 1);
    elseif (substr($target, 0, 1) == '8') $target = '62' . $target;

    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.fonnte.com/send',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => array('target' => $target, 'message' => $message . WA_FOOTER, 'countryCode' => '62'),
      CURLOPT_HTTPHEADER => array("Authorization: $apiKey"),
      CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => false
    ));
    $response = curl_exec($curl); curl_close($curl);
    return $response;
}

function getPhones($conn, $roles, $dept = null) {
    $phones = [];
    if (!is_array($roles)) $roles = [$roles];
    foreach ($roles as $role) {
        $sql = "SELECT phone FROM users WHERE role = '$role'";
        if ($dept && !in_array($role, ['PlantHead', 'Administrator', 'Warehouse'])) {
            $sql .= " AND LOWER(department) = LOWER('".$conn->real_escape_string($dept)."')";
        }
        $res = $conn->query($sql);
        if ($res && $res->num_rows > 0) {
            while($row = $res->fetch_assoc()) if(!empty($row['phone'])) $phones[] = $row['phone'];
        }
        if (!empty($phones)) break; 
    }
    return $phones;
}

function getUserPhone($conn, $username) {
    $u = $conn->real_escape_string($username);
    $res = $conn->query("SELECT phone FROM users WHERE LOWER(username) = LOWER('$u') LIMIT 1");
    if ($res && $row = $res->fetch_assoc()) return $row['phone'];
    return null;
}
?>