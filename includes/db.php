<?php
// db.php - 데이터베이스 연결 설정

$host = 'localhost';
$db_name = 'lifelog_db';
$username = 'root'; // XAMPP 기본 사용자
$password = '';     // XAMPP 기본 비밀번호 (보통 비어있음)

try {
    // PDO 객체 생성 (DB 연결)
    $dsn = "mysql:host=$host;dbname=$db_name;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    
    // 에러 발생 시 예외를 던지도록 설정
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    // 연결 실패 시 에러 메시지 출력 후 종료
    die("데이터베이스 연결 실패: " . $e->getMessage());
}

// MySQLi 연결 (일부 파일에서 사용)
$conn = new mysqli($host, $username, $password, $db_name);

// 연결 확인
if ($conn->connect_error) {
    die("MySQLi 연결 실패: " . $conn->connect_error);
}

// UTF-8 설정
$conn->set_charset("utf8mb4");
?>