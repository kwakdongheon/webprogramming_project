<?php
// 임시 도구: 주어진 평문 비밀번호에 대한 password_hash 생성
// 업로드 후 브라우저에서 접근하여 해시를 만든 뒤, DB에 업데이트하세요.

function h($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

$hash = null;
$password = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    if ($password !== '') {
        $hash = password_hash($password, PASSWORD_DEFAULT);
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>비밀번호 해시 생성기</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        form { max-width: 360px; }
        label { display:block; margin-top:10px; }
        input { width:100%; padding:8px; margin-top:5px; }
        button { margin-top:12px; padding:10px; width:100%; }
        code { word-break: break-all; display:block; background:#f5f5f5; padding:10px; }
    </style>
</head>
<body>
    <h2>비밀번호 해시 생성기</h2>
    <form method="POST">
        <label>평문 비밀번호</label>
        <input type="password" name="password" required>
        <button type="submit">해시 생성</button>
    </form>

    <?php if ($hash): ?>
        <h3>생성된 해시</h3>
        <code><?php echo h($hash); ?></code>
        <p>이 값을 DB의 `users.password` 컬럼에 업데이트하세요.</p>
    <?php elseif ($password === '' && $_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <p>비밀번호를 입력하세요.</p>
    <?php endif; ?>

    <p><a href="debug_login.php">디버그 로그인으로 돌아가기</a></p>
</body>
</html>
