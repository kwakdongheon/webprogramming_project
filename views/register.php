<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>LifeLog - 회원가입</title>
    <style>
        /* 간단한 스타일링 (나중에 CSS 파일로 분리 가능) */
        body { font-family: sans-serif; padding: 20px; }
        form { max-width: 300px; margin: 20px 0; }
        label { display: block; margin-top: 10px; }
        input { width: 100%; padding: 8px; margin-top: 5px; }
        button { margin-top: 15px; padding: 10px; width: 100%; background: #4CAF50; color: white; border: none; cursor: pointer; }
        button:hover { background: #45a049; }
    </style>
</head>
<body>
    <h2>회원가입</h2>
    <p>나만의 일상을 기록하기 위해 가입해주세요.</p>
    
    <form action="register_process.php" method="POST">
        <label>사용자 이름 (Username):</label>
        <input type="text" name="username" required placeholder="예: 홍길동">
        
        <label>이메일 (Email):</label>
        <input type="email" name="email" required placeholder="example@email.com">
        
        <label>비밀번호 (Password):</label>
        <input type="password" name="password" required placeholder="비밀번호 입력">
        
        <button type="submit">가입하기</button>
    </form>
    
    <p>이미 계정이 있으신가요? <a href="login.php">로그인 하러가기</a></p>
</body>
</html>