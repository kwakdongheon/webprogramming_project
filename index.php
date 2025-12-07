<?php
session_start();
require_once 'includes/db.php';
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeLog - 나의 일상 기록</title>
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
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="user-info" style="display:flex; gap:20px; align-items:center;">
                <div class="view-toggle">
                    <button class="toggle-btn active" id="calendarToggle" onclick="switchView('calendar')">📅 캘린더</button>
                    <button class="toggle-btn" id="listToggle" onclick="switchView('list')">📋 전체 기록</button>
                </div>
                <span class="user-badge">@<?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <button class="btn btn-secondary" onclick="location.href='views/write_screen.php'">✏️ 기록하기</button>
                <button class="btn logout-btn" onclick="location.href='logout.php'">로그아웃</button>
            </div>
        <?php else: ?>
            <div class="user-info">
                <button class="btn btn-secondary" onclick="location.href='views/login.php'">로그인</button>
            </div>
        <?php endif; ?>
    </header>

    <main>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <!-- 비로그인 시: 랜딩 페이지 -->
            <div class="landing-container" style="display:flex; flex-direction:column; align-items:center; justify-content:center; height:80vh; text-align:center;">
                <div style="font-size: 5rem; margin-bottom: 20px;">📅✨</div>
                <h2 style="font-size: 3rem; color:var(--secondary); margin-bottom:10px;">나의 하루를<br>예쁘게 기록하세요</h2>
                <p style="color:#666; margin-bottom:30px;">
                    맛집, 여행, 취미, 그리고 소소한 일상까지.<br>
                    LifeLog 캘린더에 당신의 이야기를 채워보세요.
                </p>
                <div>
                    <button class="btn btn-secondary" style="padding:15px 40px; font-size:1.2rem;" onclick="location.href='views/register.php'">시작하기</button>
                    <button class="btn" style="padding:15px 40px; font-size:1.2rem; background:white; color:var(--primary); border:2px solid var(--primary);" onclick="location.href='views/login.php'">로그인</button>
                </div>
            </div>

        <?php else: ?>
            <!-- 로그인 시: 2단 레이아웃 (좌:캘린더 / 우:피드) -->
            <div id="calendarView" class="main-layout" style="display:grid;">
                
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
                    
                    <!-- 카테고리 필터 -->
                    <div class="category-filter">
                        <button class="filter-btn active" data-category="all">전체</button>
                        <button class="filter-btn" data-category="맛집">🍴 맛집</button>
                        <button class="filter-btn" data-category="카페">☕ 카페</button>
                        <button class="filter-btn" data-category="여행">✈️ 여행</button>
                        <button class="filter-btn" data-category="취미">🎨 취미</button>
                        <button class="filter-btn" data-category="일상">📝 일상</button>
                        <button class="filter-btn" data-category="기타">📦 기타</button>
                    </div>
                </div>
                
                <!-- 폴라로이드 카드들이 들어갈 컨테이너 -->
                <div id="feedContainer" class="post-list">
                    <div class="empty-state">
                        날짜를 클릭하면<br>이야기가 펼쳐집니다 ✨
                    </div>
                </div>
            </section>            </div>

            <!-- 전체 기록 보기 -->
            <div id="listView" style="display:none; max-width: 1000px; margin: 40px auto; padding: 0 20px;">
                <h2 style="font-size: 2rem; color: var(--secondary); margin-bottom: 20px;">📋 나의 모든 기록</h2>
                
                <!-- 카테고리 필터 (전체 기록용) -->
                <div class="category-filter" style="margin-bottom: 30px;">
                    <button class="filter-btn active" data-list-category="all">전체</button>
                    <button class="filter-btn" data-list-category="맛집">🍴 맛집</button>
                    <button class="filter-btn" data-list-category="카페">☕ 카페</button>
                    <button class="filter-btn" data-list-category="여행">✈️ 여행</button>
                    <button class="filter-btn" data-list-category="취미">🎨 취미</button>
                    <button class="filter-btn" data-list-category="일상">📝 일상</button>
                    <button class="filter-btn" data-list-category="기타">📦 기타</button>
                </div>
                
                <div id="allPostsContainer"></div>
            </div>
            
            <script>
                // 뷰 전환 함수
                function switchView(view) {
                    console.log('switchView 호출됨:', view);
                    
                    const calendarView = document.getElementById('calendarView');
                    const listView = document.getElementById('listView');
                    const calendarToggle = document.getElementById('calendarToggle');
                    const listToggle = document.getElementById('listToggle');
                    
                    console.log('Elements:', { calendarView, listView, calendarToggle, listToggle });
                    
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
                        loadAllPosts();
                    }
                }
                
                // 전역 변수로 전체 게시글 저장
                let allPosts = [];
                let activeListCategory = 'all';
                
                // 모든 기록 로드 함수
                async function loadAllPosts() {
                    const container = document.getElementById('allPostsContainer');
                    
                    try {
                        const response = await fetch('./api/fetch_all_posts.php');
                        if (!response.ok) throw new Error('Failed to load posts');
                        
                        const data = await response.json();
                        allPosts = data.posts || [];
                        
                        // 필터링된 게시글 렌더링
                        renderAllPosts(filterAllPosts(allPosts, activeListCategory));
                        
                        // 필터 버튼 이벤트 리스너 추가 (한 번만)
                        setupListFilters();
                        
                    } catch (error) {
                        console.error('Error loading posts:', error);
                        container.innerHTML = '<div class="empty-state">오류가 발생했습니다 😭</div>';
                    }
                }
                
                // 전체 기록 필터링 함수
                function filterAllPosts(posts, category) {
                    if (category === 'all') return posts;
                    return posts.filter(post => (post.category || '기타') === category);
                }
                
                // 전체 기록 렌더링 함수
                function renderAllPosts(posts) {
                    const container = document.getElementById('allPostsContainer');
                    
                    if (posts.length === 0) {
                        container.innerHTML = `
                            <div class="empty-state" style="margin-top: 60px;">
                                <div style="font-size: 4rem; margin-bottom: 20px;">📝</div>
                                <p style="font-size: 1.2rem;">해당 카테고리의 기록이 없습니다.</p>
                            </div>`;
                        return;
                    }
                    
                    let html = '<div class="post-list" style="flex-direction: column; gap: 20px;">';
                    
                    posts.forEach(p => {
                        html += `
                            <div class="polaroid-card" style="min-width: 100%; max-width: 100%;">
                                <div class="card-header">
                                    <div class="card-title">${p.title || '[제목 없음]'}</div>
                                    <div class="card-meta">
                                        <span class="rating-star">${'★'.repeat(p.rating)}</span> | 
                                        <span>${p.category || '기타'}</span>
                                    </div>
                                </div>`;
                        
                        if (p.images && p.images.length > 0) {
                            html += '<div class="photo-scroller">';
                            p.images.forEach(src => {
                                const finalSrc = src.startsWith('public/') ? src : 'public/' + src;
                                html += `<img src="${finalSrc}" alt="memory">`;
                            });
                            html += '</div>';
                        }
                        
                        html += `<div class="card-content">${p.content}</div>`;
                        
                        if (p.place_name) {
                            html += `<div style="margin-top:15px; font-size:0.9rem; color:#888;">📍 ${p.place_name}</div>`;
                        }
                        
                        html += `
                            <div style="margin-top:20px; text-align:right;">
                                <a href="views/post_view.php?id=${p.id}" class="btn btn-secondary" style="font-size:0.8rem; padding:6px 12px;">보기</a>
                                <a href="views/post_edit.php?id=${p.id}" class="btn btn-secondary" style="font-size:0.8rem; padding:6px 12px;">수정</a>
                                <button class="btn btn-delete" style="font-size:0.8rem; padding:6px 12px;" onclick="if(confirm('정말 삭제할까요?')) location.href='post_delete.php?id=${p.id}'">삭제</button>
                            </div>
                        </div>`;
                    });
                    
                    html += '</div>';
                    container.innerHTML = html;
                }
                
                // 전체 기록 필터 버튼 설정
                let listFiltersSetup = false;
                function setupListFilters() {
                    if (listFiltersSetup) return;
                    listFiltersSetup = true;
                    
                    const listFilterBtns = document.querySelectorAll('[data-list-category]');
                    listFilterBtns.forEach(btn => {
                        btn.addEventListener('click', function() {
                            // 활성화 상태 변경
                            listFilterBtns.forEach(b => b.classList.remove('active'));
                            this.classList.add('active');
                            
                            // 카테고리 저장 및 재렌더링
                            activeListCategory = this.dataset.listCategory;
                            renderAllPosts(filterAllPosts(allPosts, activeListCategory));
                        });
                    });
                }
            </script>
            
            <script src="./public/js/calendar.js"></script>
        <?php endif; ?>
    </main>
</body>
</html>