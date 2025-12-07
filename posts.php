<?php
require_once 'includes/auth_guard.php';
require_once 'includes/db.php';

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM posts WHERE user_id=? ORDER BY date DESC");
$stmt->execute([$user_id]);
$posts = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>내 기록 목록 - LifeLog</title>
    <link rel="stylesheet" href="./public/css/calendar.css">
</head>
<body>
    <!-- 헤더 -->
    <header>
        <div class="logo">
            <a href="index.php">
                <img src="./public/images/logo.png" alt="LifeLog" class="logo-img">
                <span class="logo-title">LifeLog</span>
            </a>
        </div>
        <div class="user-info" style="display:flex; gap:20px; align-items:center;">
            <div class="view-toggle">
                <button class="toggle-btn" id="calendarToggle" onclick="switchView('calendar')">📅 캘린더</button>
                <button class="toggle-btn active" id="listToggle" onclick="switchView('list')">📋 전체 기록</button>
            </div>
            <span class="user-badge">@<?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <button class="btn btn-secondary" onclick="location.href='views/write_screen.php'">✏️ 기록하기</button>
            <button class="btn logout-btn" onclick="location.href='logout.php'">로그아웃</button>
        </div>
    </header>

    <main>
        <!-- 캘린더 뷰 -->
        <div id="calendarView" class="main-layout" style="display:none;">
            
            <!-- 1. 왼쪽 사이드바 (캘린더) -->
            <aside class="sidebar">
                <div class="calendar-header">
                    <button id="prevMonth" class="nav-btn">◀</button>
                    <h2 id="currentYearMonth" class="date-display">2025년 12월</h2>
                    <button id="nextMonth" class="nav-btn">▶</button>
                </div>
                <div class="calendar-grid" id="calendarGrid">
                    <!-- JS가 채움 -->
                </div>
            </aside>

            <!-- 2. 오른쪽 피드 영역 (스크롤) -->
            <section class="feed-area">
                <div class="feed-header">
                    <h3 id="detailTitle">오늘의 기록 📝</h3>
                </div>
                
                <!-- 폴라로이드 카드들이 들어갈 컨테이너 -->
                <div id="feedContainer" class="post-list">
                    <div class="empty-state">
                        날짜를 클릭하면<br>이야기가 펼쳐집니다 ✨
                    </div>
                </div>
            </section>

        </div>

        <!-- 전체 기록 보기 -->
        <div id="listView" style="display:block; max-width: 1000px; margin: 40px auto; padding: 0 20px;">
            <h2 style="font-size: 2rem; color: var(--secondary); margin-bottom: 30px;">📋 나의 모든 기록</h2>

            <!-- 카테고리 필터 -->
            <div class="category-filter" style="margin-bottom: 20px;">
                <button class="filter-btn active" onclick="filterListView('all')"전체</button>
                <button class="filter-btn" onclick="filterListView('맛집')">🍴 맛집</button>
                <button class="filter-btn" onclick="filterListView('카페')">☕ 카페</button>
                <button class="filter-btn" onclick="filterListView('여행')">✈️ 여행</button>
                <button class="filter-btn" onclick="filterListView('취미')">🎨 취미</button>
                <button class="filter-btn" onclick="filterListView('일상')">📝 일상</button>
            </div>

            <?php if(count($posts)===0): ?>
                <div class="empty-state" style="margin-top: 60px;">
                    <div style="font-size: 4rem; margin-bottom: 20px;">📝</div>
                    <p style="font-size: 1.2rem;">작성된 기록이 없습니다.</p>
                    <button class="btn btn-secondary" style="margin-top: 20px;" onclick="location.href='views/write_screen.php'">첫 기록 시작하기</button>
                </div>
            <?php else: ?>
                <div class="post-list" id="postListContainer" style="flex-direction: column; gap: 20px;">
                    <?php foreach($posts as $p): ?>
                        <div class="polaroid-card post-item" data-category="<?= htmlspecialchars($p['category']) ?>" style="min-width: 100%; max-width: 100%;">
                            <div class="card-header">
                                <div class="card-title"><?= htmlspecialchars($p['title'] ?: "[제목 없음]") ?></div>
                                <div class="card-meta">
                                    <span class="rating-star"><?=str_repeat('★', intval($p['rating'])) ?></span> | 
                                    <span><?= htmlspecialchars($p['category'] ?? '기타') ?></span>
                                </div>
                            </div>
                            
                            <?php 
                            // 해당 게시글의 사진 조회
                            $img_stmt = $pdo->prepare("SELECT file_path FROM photos WHERE post_id=? ORDER BY uploaded_at ASC");
                            $img_stmt->execute([$p['id']]);
                            $images = $img_stmt->fetchAll(PDO::FETCH_COLUMN);
                            ?>
                            
                            <?php if (!empty($images)): ?>
                                <div class="photo-scroller">
                                    <?php foreach($images as $src): ?>
                                        <img src="<?= htmlspecialchars($src) ?>" alt="memory">
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="card-content"><?= htmlspecialchars($p['content']) ?></div>
                            
                            <?php if(!empty($p['place_name'])): ?>
                                <div style="margin-top:15px; font-size:0.9rem; color:#888;">📍 <?= htmlspecialchars($p['place_name']) ?></div>
                            <?php endif; ?>
                            
                            <div style="margin-top:20px; text-align:right;">
                                <a href="views/post_view.php?id=<?= $p['id'] ?>" class="btn btn-secondary" style="font-size:0.8rem; padding:6px 12px;">보기</a>
                                <a href="views/post_edit.php?id=<?= $p['id'] ?>" class="btn btn-secondary" style="font-size:0.8rem; padding:6px 12px;">수정</a>
                                <button class="btn btn-delete" style="font-size:0.8rem; padding:6px 12px;" onclick="if(confirm('정말 삭제할까요?')) location.href='post_delete.php?id=<?= $p['id'] ?>'">삭제</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
        // 뷰 전환 함수
        function switchView(view) {
            const calendarView = document.getElementById('calendarView');
            const listView = document.getElementById('listView');
            const calendarToggle = document.getElementById('calendarToggle');
            const listToggle = document.getElementById('listToggle');
            
            if (view === 'calendar') {
                calendarView.style.display = 'grid';
                listView.style.display = 'none';
                calendarToggle.classList.add('active');
                listToggle.classList.remove('active');
            } else {
                calendarView.style.display = 'none';
                listView.style.display = 'block';
                calendarToggle.classList.remove('active');
                listToggle.classList.add('active');
            }
        }
        
        // 카테고리 필터링 함수
        function filterListView(category) {
            // 버튼 활성화 상태 변경
            document.querySelectorAll('#listView .filter-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            
            // 모든 게시글 카드 가져오기
            const posts = document.querySelectorAll('.post-item');
            
            posts.forEach(post => {
                if (category === 'all') {
                    post.style.display = 'block';
                } else {
                    const postCategory = post.getAttribute('data-category');
                    post.style.display = postCategory === category ? 'block' : 'none';
                }
            });
        }
    </script>

    <script src="./public/js/calendar.js"></script>
</body>
</html>