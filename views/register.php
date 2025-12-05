<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeLog - 회원가입</title>
    <link rel="stylesheet" href="../public/css/calendar.css">
</head>
<body>

    <div class="auth-container">
        <div class="auth-card">
            <div style="font-size: 3rem; margin-bottom: 10px;">👋</div>
            <h2>회원가입</h2>
            <p style="color:var(--text-sub); margin-bottom:20px;">나만의 소중한 일상을 기록해보세요.</p>
            
            <form action="../register_process.php" method="POST">
                <div class="form-group">
                    <label>사용자 이름 (닉네임)</label>
                    <input type="text" name="username" class="auth-input" required placeholder="예: 라이프로거">
                </div>

                <div class="form-group">
                    <label>이메일</label>
                    <input type="email" name="email" class="auth-input" required placeholder="example@email.com">
                </div>
                
                <div class="form-group">
                    <label>비밀번호</label>
                    <input type="password" name="password" class="auth-input" required placeholder="비밀번호 설정">
                </div>
                
                <button type="submit" class="btn btn-secondary full-width">가입하기</button>
            </form>
            
            <div class="auth-link">
                이미 계정이 있으신가요? <a href="login.php">로그인 하러가기</a>
            </div>
             <div class="auth-link">
                <a href="../index.php">← 메인으로 돌아가기</a>
            </div>
        </div>
    </div>

</body>
</html>