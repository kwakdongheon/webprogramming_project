<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeLog - 로그인</title>
    <!-- CSS 연결 -->
    <link rel="stylesheet" href="../public/css/calendar.css">
</head>
<body>
    
    <div class="auth-container">
        <div class="auth-card">
            <div style="font-size: 3rem; margin-bottom: 10px;">🔐</div>
            <h2>로그인</h2>
            
            <form action="../login_process.php" method="POST">
                <div class="form-group">
                    <label>이메일</label>
                    <input type="email" name="email" class="auth-input" required placeholder="example@email.com">
                </div>
                
                <div class="form-group">
                    <label>비밀번호</label>
                    <input type="password" name="password" class="auth-input" required placeholder="비밀번호 입력">
                </div>
                
                <button type="submit" class="btn full-width" style="margin-top: 20px;">로그인</button>
            </form>
            
            <div class="auth-link">
                아직 계정이 없으신가요? <a href="register.php">회원가입 하기</a>
            </div>
            <div class="auth-link">
                <a href="../index.php">← 메인으로 돌아가기</a>
            </div>
        </div>
    </div>

</body>
</html>