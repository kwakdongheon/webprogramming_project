<?php
// auth_guard.php: 로그인 여부를 확인하는 보안관 (공통 include)

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    // 호출 스크립트가 views 하위인지에 따라 로그인 페이지 경로 결정
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    $inViews = stripos($script, '/views/') !== false;
    $loginPath = $inViews ? 'login.php' : 'views/login.php';
    echo "<script>alert('로그인이 필요한 페이지입니다.'); window.location.href='" . $loginPath . "';</script>";
    exit;
}
?>