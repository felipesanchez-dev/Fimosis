// backend/students_search.php
<?php
require_once 'db.php';
header('Content-Type: application/json');

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
if ($q === '') {
  $stmt = $pdo->query("SELECT * FROM students ORDER BY id DESC");
} else {
  $stmt = $pdo->prepare("SELECT * FROM students WHERE name LIKE ? ORDER BY id DESC");
  $stmt->execute(["%$q%"]);
}
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
