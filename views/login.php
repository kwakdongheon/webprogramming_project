<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>LifeLog - 로그인</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        form { max-width: 300px; margin: 20px 0; }
        label { display: block; margin-top: 10px; }
        input { width: 100%; padding: 8px; margin-top: 5px; }
        button { margin-top: 15px; padding: 10px; width: 100%; background: #007BFF; color: white; border: none; cursor: pointer; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h2>로그인</h2>
    
    <form action="login_process.php" method="POST">
        <label>이메일:</label>
        <input type="email" name="email" required placeholder="가입한 이메일 입력">
        
        <label>비밀번호:</label>
        <input type="password" name="password" required placeholder="비밀번호">
        
        <button type="submit">로그인</button>
    </form>
    
    <p>아직 계정이 없으신가요? <a href="register.php">회원가입 하기</a></p>
</body>
</html>