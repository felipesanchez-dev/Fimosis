<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/db.php';
$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $username = $input['username'] ?? '';
    $password = $input['password'] ?? '';
    if (!$username || !$password) { http_response_code(400); echo json_encode(['error'=>'username and password required']); exit; }
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]); $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user['username']; $_SESSION['role'] = $user['role'];
        echo json_encode(['message'=>'ok','role'=>$user['role']]); exit;
    } else {
        http_response_code(401); echo json_encode(['error'=>'invalid credentials']); exit;
    }
}
http_response_code(405); echo json_encode(['error'=>'method not allowed']);
?>