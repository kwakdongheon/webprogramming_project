<?php
// db.php - 데이터베이스 연결 설정

// 설정 파일 로드
$config_file = __DIR__ . '/../config.ini';
if (!file_exists($config_file)) {
    die("설정 파일이 없습니다. config.ini.example을 복사하여 config.ini를 생성하세요.");
}

$config = parse_ini_file($config_file);

// config.ini에서 값 가져오기
$host = $config['DB_HOST'];
$username = $config['DB_USER'];
$password = $config['DB_PASS'];
$db_name = $config['DB_NAME'];

try {
    // PDO 객체 생성 (DB 연결) - MariaDB 10.4+ 인증 문제 해결
    // 포트 3306 명시, unix_socket 대신 TCP 사용
    // $dsn = "mysql:host=$host;port=3306;dbname=$db_name;charset=utf8mb4";
    $dsn = "mysql:host=$host;dbname=$db_name;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
        PDO::MYSQL_ATTR_LOCAL_INFILE => true
    ];
    $pdo = new PDO($dsn, $username, $password, $options);
    
} catch (PDOException $e) {
    // MariaDB 인증 플러그인 문제 안내
    $error_msg = $e->getMessage();
    if (strpos($error_msg, 'auth_gssapi_client') !== false) {
        die("데이터베이스 연결 실패: MariaDB 인증 플러그인 문제<br><br>
            <strong>해결 방법:</strong><br>
            1. phpMyAdmin에서 root 사용자의 인증 방식을 변경하세요:<br>
            &nbsp;&nbsp; - phpMyAdmin 접속 → 사용자 계정 → root → 편집<br>
            &nbsp;&nbsp; - 플러그인: 'mysql_native_password' 선택<br>
            &nbsp;&nbsp; - 비밀번호 설정 (또는 공백 유지)<br>
            <br>
            2. 또는 MySQL 콘솔에서:<br>
            &nbsp;&nbsp; <code>ALTER USER 'root'@'localhost' IDENTIFIED VIA mysql_native_password;</code><br>
            <br>
            원본 오류: " . htmlspecialchars($error_msg));
    }
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