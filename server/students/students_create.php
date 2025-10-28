<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../db/db.php';
$input = json_decode(file_get_contents('php://input'), true);
$name = $input['name'] ?? ''; $grade = isset($input['grade']) ? $input['grade'] : null; $status = $input['status'] ?? 'active';
if (!$name) { http_response_code(400); echo json_encode(['error'=>'name required']); exit; }
try { $pdo->beginTransaction(); $stmt = $pdo->prepare('INSERT INTO students (name,grade,status) VALUES (?,?,?)'); $stmt->execute([$name,$grade,$status]); $id = $pdo->lastInsertId(); $pdo->commit();
echo json_encode(['id'=>$id,'name'=>$name,'grade'=>$grade,'status'=>$status]); } catch (Exception $e) { $pdo->rollBack(); http_response_code(500); echo json_encode(['error'=>$e->getMessage()]); }
?>