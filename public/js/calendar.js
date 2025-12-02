// 캘린더 렌더링 및 제어 로직
(function(){
  const grid = document.getElementById('calendarGrid');
  const yearMonthLabel = document.getElementById('currentYearMonth');
  const prevBtn = document.getElementById('prevMonth');
  const nextBtn = document.getElementById('nextMonth');
  const postList = document.getElementById('postList');
  const detailTitle = document.getElementById('detailTitle');

  const dayNames = ['일', '월', '화', '수', '목', '금', '토'];
  let viewDate = new Date(); // 현재 날짜 기준

  // 숫자를 2자리 문자열로 변환 (예: 5 -> "05")
  function pad(n) {
    return n < 10 ? '0' + n : '' + n;
  }

  // 캘린더 기본 구조 생성 (요일 헤더)
  function buildSkeleton() {
    grid.innerHTML = '';
    // 요일 헤더 추가
    dayNames.forEach(d => {
      const hd = document.createElement('div');
      hd.className = 'day-name';
      hd.textContent = d;
      grid.appendChild(hd);
    });
  }

  // 캘린더 렌더링 (월별)
  async function render() {
    buildSkeleton();
    const year = viewDate.getFullYear();
    const month = viewDate.getMonth(); // 0-11
    yearMonthLabel.textContent = `${year}년 ${month + 1}월`;

    // 해당 월의 첫 날이 무슨 요일인지 (0: 일요일 ~ 6: 토요일)
    const firstDayIdx = new Date(year, month, 1).getDay();
    // 해당 월의 마지막 날짜
    const lastDate = new Date(year, month + 1, 0).getDate();

    // 서버에서 해당 월에 게시글이 있는 날짜 가져오기
    let activeDates = [];
    try {
      const res = await fetch(`./api/fetch_month.php?year=${year}&month=${month + 1}`);
      if (res.ok) {
        const data = await res.json();
        activeDates = data.dates || [];
      }
    } catch (e) {
      console.warn('월별 게시글 데이터 불러오기 실패:', e);
    }

    // 앞쪽 빈 칸 (이전 달 날짜)
    for (let i = 0; i < firstDayIdx; i++) {
      const empty = document.createElement('div');
      empty.className = 'day-cell empty';
      grid.appendChild(empty);
    }

    // 실제 날짜 칸 생성
    for (let d = 1; d <= lastDate; d++) {
      const cell = document.createElement('div');
      cell.className = 'day-cell';
      
      const span = document.createElement('span');
      span.className = 'date-number';
      span.textContent = d;
      cell.appendChild(span);

      // 게시글이 있는 날짜인지 확인
      const hasPosts = activeDates.includes(d);
      if (hasPosts) {
        cell.classList.add('has-posts');
        const indicator = document.createElement('button');
        indicator.className = 'indicator';
        indicator.type = 'button';
        indicator.title = '게시글 있음';
        indicator.setAttribute('aria-label', `${d}일에 게시글 있음`);
        cell.appendChild(indicator);
      }

      // 날짜 클릭 시 해당 날짜의 게시글 목록 표시
      cell.addEventListener('click', () => loadDay(year, month + 1, d));
      cell.setAttribute('tabindex', '0'); // 키보드 접근성
      cell.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          loadDay(year, month + 1, d);
        }
      });

      grid.appendChild(cell);
    }

    // 42칸 채우기 (6행 보장) - 달력을 일정한 크기로 유지
    const totalCells = firstDayIdx + lastDate;
    const target = totalCells <= 35 ? 35 : 42; // 5주 또는 6주
    for (let i = totalCells; i < target; i++) {
      const tail = document.createElement('div');
      tail.className = 'day-cell empty';
      grid.appendChild(tail);
    }
  }

  // 특정 날짜의 게시글 목록 불러오기
  async function loadDay(year, month, day) {
    const iso = `${year}-${pad(month)}-${pad(day)}`;
    detailTitle.textContent = `${year}년 ${month}월 ${day}일 게시글`;
    postList.innerHTML = '<li>로딩 중...</li>';

    try {
      const res = await fetch(`./api/fetch_day.php?date=${iso}`);
      if (!res.ok) {
        postList.innerHTML = '<li>불러오기 실패</li>';
        return;
      }
      
      const data = await res.json();
      const posts = data.posts || [];
      
      if (posts.length === 0) {
        postList.innerHTML = '<li>이 날짜에는 게시글이 없습니다.</li>';
        return;
      }

      postList.innerHTML = '';
      posts.forEach(p => {
        const li = document.createElement('li');
        
        // 제목
        const title = document.createElement('strong');
        title.textContent = p.title || '(제목 없음)';
        li.appendChild(title);
        
        // 카테고리 & 평점
        if (p.category || p.rating) {
          const meta = document.createElement('p');
          meta.style.fontSize = '0.9rem';
          meta.style.color = '#777';
          let metaText = '';
          if (p.category) metaText += `📁 ${p.category}`;
          if (p.rating) metaText += ` | ⭐ ${p.rating}점`;
          meta.textContent = metaText;
          li.appendChild(meta);
        }
        
        // 이미지 표시
        if (p.images && p.images.length) {
          p.images.forEach(src => {
            const img = document.createElement('img');
            img.src = src;
            img.alt = p.title || '게시글 이미지';
            li.appendChild(img);
          });
        }
        
        // 내용
        const content = document.createElement('p');
        content.textContent = p.content || '';
        li.appendChild(content);
        
        // 장소 정보
        if (p.place_name) {
          const place = document.createElement('p');
          place.style.fontSize = '0.9rem';
          place.style.color = '#555';
          place.textContent = `📍 ${p.place_name}`;
          if (p.place_address) {
            place.textContent += ` (${p.place_address})`;
          }
          li.appendChild(place);
        }
        
        // 수정/삭제 버튼 (선택사항)
        if (p.canEdit) {
          const actions = document.createElement('div');
          actions.style.marginTop = '10px';
          
          const editBtn = document.createElement('button');
          editBtn.textContent = '수정';
          editBtn.onclick = () => location.href = `./views/post_edit.php?id=${p.id}`;
          
          const deleteBtn = document.createElement('button');
          deleteBtn.textContent = '삭제';
          deleteBtn.style.background = '#ff5252';
          deleteBtn.style.color = 'white';
          deleteBtn.onclick = () => {
            if (confirm('정말 삭제하시겠습니까?')) {
              location.href = `./post_delete.php?id=${p.id}`;
            }
          };
          
          actions.appendChild(editBtn);
          actions.appendChild(deleteBtn);
          li.appendChild(actions);
        }
        
        postList.appendChild(li);
      });
    } catch (e) {
      console.error('게시글 불러오기 오류:', e);
      postList.innerHTML = '<li>오류가 발생했습니다.</li>';
    }
  }

  // 이전/다음 달 버튼 이벤트
  prevBtn.addEventListener('click', () => {
    viewDate.setMonth(viewDate.getMonth() - 1);
    render();
  });

  nextBtn.addEventListener('click', () => {
    viewDate.setMonth(viewDate.getMonth() + 1);
    render();
  });

  // 초기 렌더링
  render();
})();
