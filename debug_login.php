<?php
// 운영 환경에서 로그인 문제를 빠르게 진단하기 위한 페이지
// 이메일로 사용자 조회 및 입력한 비밀번호와의 password_verify 결과를 출력

require_once __DIR__ . '/includes/db.php';

function h($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

$result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        $stmt = $pdo->prepare('SELECT id, username, email, password FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $verify = password_verify($password, $user['password']);
            $result = [
                'found' => true,
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'hash_prefix' => substr($user['password'], 0, 10),
                'password_verify' => $verify,
            ];
        } else {
            $result = [ 'found' => false ];
        }
    } catch (Throwable $e) {
        $result = [ 'error' => $e->getMessage() ];
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>디버그: 로그인 확인</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        form { max-width: 360px; margin-bottom: 20px; }
        label { display:block; margin-top:10px; }
        input { width:100%; padding:8px; margin-top:5px; }
        button { margin-top:12px; padding:10px; width:100%; }
        pre { background:#f5f5f5; padding:10px; }
    </style>
</head>
<body>
    <h2>디버그: 로그인 확인</h2>
    <p>운영 DB에서 사용자 조회 및 비밀번호 검증 결과를 확인합니다.</p>
    <form method="POST">
        <label>이메일</label>
        <input type="email" name="email" required>
        <label>비밀번호(평문)</label>
        <input type="password" name="password" required>
        <button type="submit">확인</button>
    </form>

    <?php if ($result !== null): ?>
        <h3>결과</h3>
        <pre><?php echo h(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
        <p>
            참고: <code>hash_prefix</code>는 저장된 비밀번호 해시의 앞부분입니다. 
            만약 해시가 아닌 평문으로 저장되어 있다면 로그인은 실패합니다.
        </p>
    <?php endif; ?>

    <p><a href="views/login.php">로그인 페이지로 돌아가기</a></p>
</body>
</html>
