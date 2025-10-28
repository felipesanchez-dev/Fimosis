<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') { http_response_code(403); echo json_encode(['error'=>'admin required']); exit; }
require_once __DIR__ . '/db.php';
$input = json_decode(file_get_contents('php://input'), true);
$id = isset($input['id']) ? (int)$input['id'] : 0; if (!$id) { http_response_code(400); echo json_encode(['error'=>'id required']); exit; }
try { $pdo->beginTransaction(); $stmt = $pdo->prepare('DELETE FROM students WHERE id=?'); $stmt->execute([$id]); $pdo->commit(); echo json_encode(['deleted'=>$id]); } catch (Exception $e) { $pdo->rollBack(); http_response_code(500); echo json_encode(['error'=>$e->getMessage()]); }
?>