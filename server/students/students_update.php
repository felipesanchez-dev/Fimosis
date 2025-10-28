<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') { http_response_code(403); echo json_encode(['error'=>'admin required']); exit; }
require_once __DIR__ . '/db.php';
$input = json_decode(file_get_contents('php://input'), true);
$id = isset($input['id']) ? (int)$input['id'] : 0; $name = $input['name'] ?? ''; $grade = isset($input['grade']) ? $input['grade'] : null; $status = $input['status'] ?? null;
if (!$id || !$name) { http_response_code(400); echo json_encode(['error'=>'id and name required']); exit; }
try { $pdo->beginTransaction(); $stmt = $pdo->prepare('UPDATE students SET name=?, grade=?, status=? WHERE id=?'); $stmt->execute([$name,$grade,$status,$id]); $pdo->commit(); echo json_encode(['id'=>$id,'name'=>$name,'grade'=>$grade,'status'=>$status]); } catch (Exception $e) { $pdo->rollBack(); http_response_code(500); echo json_encode(['error'=>$e->getMessage()]); }
?>