<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../db/db.php';
$input = json_decode(file_get_contents('php://input'), true); $ids = $input['ids'] ?? [];
if (!is_array($ids) || empty($ids)) { http_response_code(400); echo json_encode(['error'=>'ids required']); exit; }
try { $pdo->beginTransaction(); $stmt = $pdo->prepare('DELETE FROM students WHERE id = ?'); foreach ($ids as $id) { $stmt->execute([(int)$id]); } $pdo->commit(); echo json_encode(['deleted'=>$ids]); } catch (Exception $e) { $pdo->rollBack(); http_response_code(500); echo json_encode(['error'=>$e->getMessage()]); }
?>