<?php
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$dbname = 'code';
try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbname`");
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin','student') NOT NULL DEFAULT 'student',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $pdo->exec("CREATE TABLE IF NOT EXISTS students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(200) NOT NULL,
        grade DECIMAL(4,2),
        status VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $stmt = $pdo->prepare("SELECT COUNT(*) as c FROM users"); $stmt->execute(); $r = $stmt->fetch();
    if ($r['c'] == 0) {
        $ins = $pdo->prepare("INSERT INTO users (username,password,role) VALUES (?,?,?)");
        $ins->execute(['admin', password_hash('adminpass', PASSWORD_DEFAULT), 'admin']);
        $ins->execute(['student1', password_hash('pass1', PASSWORD_DEFAULT), 'student']);
    }
    $stmt = $pdo->prepare("SELECT COUNT(*) as c FROM students"); $stmt->execute(); $r = $stmt->fetch();
    if ($r['c'] == 0) {
        $students = [
            ['Ana Pérez', 4.50, 'active'],
            ['Carlos Ruiz', 3.20, 'inactive'],
            ['Luisa Gómez', 4.80, 'active'],
            ['Pedro Martínez', 2.90, 'probation'],
            ['María López', 3.90, 'active'],
            ['Diego Torres', 3.50, 'active'],
            ['Sofia Rojas', 4.10, 'active']
        ];
        $ins = $pdo->prepare("INSERT INTO students (name,grade,status) VALUES (?,?,?)");
        foreach ($students as $s) { $ins->execute($s); }
    }
    echo "<h3>Base de datos '$dbname' creada y poblada correctamente.</h3>";
    echo "<p>Usuarios: admin/adminpass | student1/pass1</p>";
    echo "<p><a href='../frontend/login.html'>Ir al login</a></p>";
} catch (PDOException $e) { echo 'Error: ' . $e->getMessage(); }
?>