// 캘린더 렌더링 및 제어 로직
(function(){
    const grid = document.getElementById('calendarGrid');
    const yearMonthLabel = document.getElementById('currentYearMonth');
    const prevBtn = document.getElementById('prevMonth');
    const nextBtn = document.getElementById('nextMonth');
    const feedContainer = document.getElementById('feedContainer'); 
    const detailTitle = document.getElementById('detailTitle');
  
    const dayNames = ['일', '월', '화', '수', '목', '금', '토'];
    let viewDate = new Date(); // 현재 날짜 기준
    let selectedDateElem = null; // 현재 선택된 날짜 요소
    
    // 카테고리 필터링을 위한 전역 변수
    let currentPosts = []; // 현재 로드된 게시글 저장
    let activeCategory = 'all'; // 현재 선택된 카테고리
  
    function pad(n) { return n < 10 ? '0' + n : '' + n; }
  
    // 캘린더 초기화
    function buildSkeleton() {
      grid.innerHTML = '';
      dayNames.forEach(d => {
        const hd = document.createElement('div');
        hd.className = 'day-name';
        hd.textContent = d;
        grid.appendChild(hd);
      });
    }
  
    // 캘린더 렌더링
    async function render() {
      buildSkeleton();
      const year = viewDate.getFullYear();
      const month = viewDate.getMonth();
      yearMonthLabel.textContent = `${year}년 ${month + 1}월`;
  
      const firstDayIdx = new Date(year, month, 1).getDay();
      const lastDate = new Date(year, month + 1, 0).getDate();
  
      // 데이터 가져오기
      let activeDates = [];
      try {
        const res = await fetch(`./api/fetch_month.php?year=${year}&month=${month + 1}`);
        if (res.ok) {
          const data = await res.json();
          activeDates = data.dates || [];
        }
      } catch (e) { console.warn('Fetch error:', e); }
  
      // 빈 칸 채우기
      for (let i = 0; i < firstDayIdx; i++) {
        const empty = document.createElement('div');
        empty.className = 'day-cell empty';
        grid.appendChild(empty);
      }
  
      // 날짜 채우기
      for (let d = 1; d <= lastDate; d++) {
        const cell = document.createElement('div');
        cell.className = 'day-cell';
        cell.textContent = d;
  
        // 게시글 있으면 점 표시
        if (activeDates.includes(d)) {
          const dot = document.createElement('div');
          dot.className = 'indicator';
          cell.appendChild(dot);
        }
  
        // 클릭 이벤트
        cell.addEventListener('click', () => {
            // 선택 효과
            if(selectedDateElem) selectedDateElem.classList.remove('selected');
            cell.classList.add('selected');
            selectedDateElem = cell;

            loadDay(year, month + 1, d);
        });

        grid.appendChild(cell);
      }
    }
  
    // ★ 폴라로이드 카드 생성 로직 ★
    async function loadDay(year, month, day) {
      const iso = `${year}-${pad(month)}-${pad(day)}`;
      
      // 제목 업데이트
      if(detailTitle) detailTitle.textContent = `${month}월 ${day}일의 기록 📝`;
      
      // 로딩 표시
      if(feedContainer) feedContainer.innerHTML = '<div class="empty-state">추억을 불러오는 중... ⏳</div>';
  
      try {
        const res = await fetch(`./api/fetch_day.php?date=${iso}`);
        console.log('fetch_day.php 응답:', res.status, res.statusText);
        
        if (!res.ok) throw new Error(`HTTP ${res.status}: ${res.statusText}`);
        
        const data = await res.json();
        console.log('받은 데이터:', data);
        currentPosts = data.posts || []; // 전역 변수에 저장
        
        if (!feedContainer) return; // 요소가 없으면 종료

        // 필터 적용하여 렌더링
        renderPosts(filterPosts(currentPosts, activeCategory));
  
      } catch (e) {
        console.error('fetch_day 오류:', e);
        if(feedContainer) feedContainer.innerHTML = `<div class="empty-state">오류가 발생했어요 😭<br><small>${e.message}</small></div>`;
      }
    }
    
    // 카테고리 필터링 함수
    function filterPosts(posts, category) {
        if (category === 'all') return posts;
        return posts.filter(post => post.category === category);
    }
    
    // 게시글 렌더링 함수
    function renderPosts(posts) {
        if (!feedContainer) return;
        
        if (posts.length === 0) {
            feedContainer.innerHTML = `
                <div class="empty-state">
                    <div style="font-size:3rem;">🍃</div>
                    <p>해당 카테고리의 기록이 없습니다 📝</p>
                </div>`;
            return;
        }
        
        feedContainer.innerHTML = '';
        
        posts.forEach(p => {
            const card = document.createElement('div');
            card.className = 'polaroid-card';
            
            let headerHtml = `
                <div class="card-header">
                    <div class="card-title">${p.title || '무제'}</div>
                    <div class="card-meta">
                        <span class="rating-star">${'★'.repeat(p.rating)}</span> | 
                        <span>${p.category}</span>
                    </div>
                </div>`;
            
            let imgHtml = '';
            if (p.images && p.images.length > 0) {
                imgHtml = `<div class="photo-scroller">`;
                p.images.forEach(src => {
                    const finalSrc = src.startsWith('public/') ? src : `public/${src}`;
                    imgHtml += `<img src="${finalSrc}" alt="memory">`;
                });
                imgHtml += `</div>`;
            }
            
            let contentHtml = `<div class="card-content">${p.content}</div>`;
            
            let placeHtml = '';
            if(p.place_name) {
                placeHtml = `<div style="margin-top:15px; font-size:0.9rem; color:#888;">📍 ${p.place_name}</div>`;
            }
            
            let actionHtml = '';
            if(p.canEdit) {
                actionHtml = `
                    <div style="margin-top:15px; display:flex; gap:8px; justify-content:flex-end;">
                        <button class="btn btn-secondary" onclick="location.href='views/post_edit.php?id=${p.id}'">✏️ 수정</button>
                        <button class="btn btn-delete" onclick="if(confirm('정말 삭제할까요?')) location.href='post_delete.php?id=${p.id}'">🗑️ 삭제</button>
                    </div>
                `;
            }
            
            card.innerHTML = headerHtml + imgHtml + contentHtml + placeHtml + actionHtml;
            feedContainer.appendChild(card);
        });
    }
  
    prevBtn.addEventListener('click', () => {
      viewDate.setMonth(viewDate.getMonth() - 1);
      render();
    });
  
    nextBtn.addEventListener('click', () => {
      viewDate.setMonth(viewDate.getMonth() + 1);
      render();
    });
  
    // 초기 실행
    render();
    
    // 필터 버튼 이벤트 리스너
    const filterBtns = document.querySelectorAll('.filter-btn');
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // 활성화 상태 변경
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // 카테고리 저장 및 재렌더링
            activeCategory = this.dataset.category;
            renderPosts(filterPosts(currentPosts, activeCategory));
        });
    });
    
    // 페이지 로드 시 오늘 날짜 자동 선택
    setTimeout(() => {
        const today = new Date();
        const todayDay = today.getDate();
        const dayCells = document.querySelectorAll('.day-cell:not(.empty)');
        
        let found = false;
        dayCells.forEach(cell => {
            const cellText = cell.childNodes[0]?.textContent || cell.textContent;
            if (cellText.trim() === todayDay.toString()) {
                if(selectedDateElem) selectedDateElem.classList.remove('selected');
                cell.classList.add('selected');
                selectedDateElem = cell;
                loadDay(today.getFullYear(), today.getMonth() + 1, todayDay);
                found = true;
            }
        });
        
        if (!found) {
            console.log('오늘 날짜 셀을 찾지 못했습니다. 첫 번째 날짜를 선택합니다.');
            if (dayCells.length > 0) {
                const firstCell = dayCells[0];
                const firstDay = parseInt(firstCell.childNodes[0]?.textContent || firstCell.textContent);
                firstCell.classList.add('selected');
                selectedDateElem = firstCell;
                loadDay(today.getFullYear(), today.getMonth() + 1, firstDay);
            }
        }
    }, 100);
})();