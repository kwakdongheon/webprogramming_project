<?php
// auth_guard.php: 로그인 여부를 확인하는 보안관
// 이 파일은 단독으로 실행되지 않고, 다른 페이지에 포함(require)되어 작동합니다.

// 세션이 아직 시작되지 않았다면 시작
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 로그인 세션이 없으면 (로그인 안 한 상태)
if (!isset($_SESSION['user_id'])) {
    // 경고창을 띄우고 로그인 페이지로 강제 이동
    echo "<script>
        alert('로그인이 필요한 페이지입니다.');
        window.location.href = 'login.php';
    </script>";
    exit; // 중요: 아래 코드가 더 이상 실행되지 않도록 여기서 스크립트 종료
}

// 로그인이 되어 있다면 아무 일도 하지 않고 통과시켜 줍니다.
?>