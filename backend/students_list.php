<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user'])) { http_response_code(401); echo json_encode(['error'=>'auth required']); exit; }
require_once __DIR__ . '/db.php';
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
$stmt = $pdo->prepare('SELECT * FROM students ORDER BY id LIMIT ? OFFSET ?');
$stmt->bindValue(1, $limit, PDO::PARAM_INT); $stmt->bindValue(2, $offset, PDO::PARAM_INT); $stmt->execute();
echo json_encode($stmt->fetchAll());
?>