<?php
session_start();
require_once 'includes/db.php';
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeLog - 일상 기록 캘린더</title>
    <link rel="stylesheet" href="./public/css/calendar.css">
</head>
<body>
    <!-- 헤더 -->
    <header>
        <h1>📅 LifeLog - 일상 기록 서비스</h1>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="user-info">
                <span>👋 안녕하세요, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>님!</span>
                <button onclick="location.href='views/write_screen.php'">✏️ 새 기록 작성</button>
                <button class="logout-btn" onclick="location.href='logout.php'">로그아웃</button>
            </div>
        <?php else: ?>
            <div class="user-info">
                <p>로그인이 필요합니다.</p>
                <button onclick="location.href='views/login.php'">로그인</button>
                <button onclick="location.href='views/register.php'">회원가입</button>
            </div>
        <?php endif; ?>
    </header>

    <?php if (isset($_SESSION['user_id'])): ?>
        <!-- 캘린더 네비게이션 -->
        <div class="calendar-header">
            <button id="prevMonth">◀ 이전 달</button>
            <h2 id="currentYearMonth">2025년 12월</h2>
            <button id="nextMonth">다음 달 ▶</button>
        </div>

        <!-- 캘린더 그리드 -->
        <div class="calendar-grid" id="calendarGrid">
            <!-- JavaScript로 동적 생성 -->
        </div>

        <!-- 선택한 날짜의 게시글 목록 -->
        <div class="day-detail">
            <h3 id="detailTitle">날짜를 선택하면 게시글이 표시됩니다</h3>
            <ul id="postList">
                <li>캘린더에서 날짜를 클릭해주세요.</li>
            </ul>
        </div>

        <script src="./public/js/calendar.js"></script>
    <?php endif; ?>
</body>
</html>