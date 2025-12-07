<?php
// register_process.php: 회원가입 처리 로직
require_once 'includes/db.php'; // DB 연결 준비

// 1. POST 방식으로 데이터가 들어왔는지 확인
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $email === '' || $password === '') {
        echo "<script>alert('모든 입력값을 채워주세요.'); history.back();</script>";
        exit;
    }

    // 2. 이메일 중복 체크 (이미 가입된 이메일인지 확인)
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        echo "<script>alert('이미 존재하는 이메일입니다.'); history.back();</script>";
        exit;
    }

    // 3. 비밀번호 암호화 (보안 필수 사항)
    // 계획서에 명시된 password_hash() 사용
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    if ($hashed_password === false) {
        echo "<script>alert('비밀번호 해시 생성에 실패했습니다.'); history.back();</script>";
        exit;
    }

    // 4. DB에 정보 저장 (Prepared Statement 사용으로 보안 강화)
    try {
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$username, $email, $hashed_password]);

        if ($result) {
            // 가입 성공 시 메인 화면(또는 로그인 화면)으로 이동
            echo "<script>
                alert('회원가입이 완료되었습니다!');
                window.location.href = 'index.php'; 
            </script>";
        } else {
            echo "회원가입 실패 (DB 오류)";
        }
    } catch (PDOException $e) {
        echo "에러 발생: " . $e->getMessage();
    }
} else {
    // 주소창에 직접 입력해서 들어온 경우 차단
    echo "잘못된 접근입니다.";
}
?>