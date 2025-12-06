# LifeLog - 일상 기록 웹 애플리케이션

## 📁 프로젝트 파일 구조

```
project1205/
│
├── api/                          # REST API 엔드포인트
│   ├── fetch_month.php          # 월별 게시글 있는 날짜 조회 (JSON)
│   ├── fetch_day.php            # 특정 날짜의 게시글 목록 조회 (JSON)
│   └── fetch_all_posts.php      # 사용자 전체 게시글 조회 (JSON)
│
├── includes/                     # 공통 포함 파일
│   ├── db.php                   # PDO/MySQLi 데이터베이스 연결 (config.ini 기반)
│   └── auth_guard.php           # 로그인 세션 확인 미들웨어
│
├── public/                       # 공개 리소스
│   ├── css/
│   │   └── calendar.css         # 통합 스타일시트 (캘린더, 카드, 헤더, 폼)
│   ├── js/
│   │   └── calendar.js          # 캘린더 동적 렌더링 + AJAX 로직
│   ├── images/
│   │   └── logo.png             # 로고 이미지 (48x48px)
│   └── uploads/                 # 업로드된 사진 저장 폴더
│
├── views/                        # 뷰 템플릿 (사용자 UI 페이지)
│   ├── login.php                # 로그인 페이지
│   ├── register.php             # 회원가입 페이지
│   ├── write_screen.php         # 게시글 작성 폼 (사진 업로드 지원)
│   ├── post_view.php            # 게시글 상세 보기 (사진 갤러리)
│   └── post_edit.php            # 게시글 수정 폼
│
├── index.php                     # 메인 페이지 (캘린더 뷰 + 전체 기록 토글)
├── posts.php                     # 전체 기록 목록 페이지 (대체 진입점)
├── login_process.php             # 로그인 처리 (세션 생성, 비밀번호 해시 검증)
├── register_process.php          # 회원가입 처리 (비밀번호 해싱)
├── write_process.php             # 게시글 작성 처리 (사진 업로드, 트랜잭션)
├── post_edit_process.php         # 게시글 수정 처리
├── post_delete.php               # 게시글 삭제 처리
├── logout.php                    # 로그아웃 (세션 파괴)
├── config.ini                    # DB 설정 파일 (Git 제외)
├── .gitignore                    # Git 제외 파일 목록
├── README.md                     # 프로젝트 개요 및 설치 가이드
└── PROJECT_STRUCTURE.md          # 본 파일 (구조 및 기술 명세)
```

---

## 🎨 주요 기능

### 1. **사용자 인증 시스템**
- **회원가입/로그인** (`register_process.php`, `login_process.php`)
  - `password_hash()` / `password_verify()` 사용
  - 세션 기반 인증 관리
  - 과거 평문 비밀번호 자동 해시 업그레이드
- **로그인 가드** (`includes/auth_guard.php`)
  - 모든 보호된 페이지에서 세션 확인
  - 미인증 시 자동 리디렉션

### 2. **캘린더 뷰 (index.php)**
- **2단 레이아웃**
  - 왼쪽: 월별 캘린더 그리드 (7열 - 일~토)
  - 오른쪽: 선택된 날짜의 게시글 피드 (폴라로이드 카드)
- **동적 렌더링** (`calendar.js`)
  - 이전/다음 달 네비게이션
  - 게시글이 있는 날짜에 오렌지 인디케이터 표시
  - 날짜 클릭 시 AJAX로 `fetch_day.php` 호출
- **뷰 토글 버튼**
  - 📅 캘린더 / 📋 전체 기록 전환
  - `switchView()` 함수로 동적 UI 전환

### 3. **전체 기록 리스트 (posts.php, fetch_all_posts.php)**
- 사용자의 모든 게시글을 날짜 역순으로 표시
- 각 카드에 제목, 별점, 카테고리, 사진, 장소 정보 포함
- "보기/수정/삭제" 버튼 제공

### 4. **게시글 작성 (views/write_screen.php, write_process.php)**
- **입력 필드**
  - 날짜 (기본값: 오늘)
  - 제목 (선택)
  - 카테고리 (맛집/카페/여행/취미/일상)
  - 평점 (⭐ 1~5)
  - 장소명/주소 (수동 입력)
  - 내용 (필수)
  - 사진 업로드 (최대 2장)
- **보안 처리**
  - Prepared Statement (SQL Injection 방어)
  - MIME 타입 검증 (`image/jpeg`, `image/png`, `image/gif`)
  - 파일 크기 제한 (5MB)
  - 파일명 난수화 (`uniqid()` + 원본 확장자)
- **트랜잭션**
  - `posts` 테이블에 게시글 저장 → `photos` 테이블에 사진 경로 저장
  - 실패 시 롤백

### 5. **게시글 보기/수정/삭제**
- **post_view.php**: 게시글 상세 정보 + 사진 갤러리
- **post_edit.php**: 제목/평점/내용 수정 (현재 사진 업로드 미지원)
- **post_delete.php**: 게시글 및 연결된 사진 파일 삭제

### 6. **반응형 디자인 (calendar.css)**
- **CSS 변수 시스템**
  ```css
  --primary: #FF8E72;     /* 코랄 핑크 */
  --secondary: #6C5CE7;   /* 퍼플 */
  --accent: #FDCB6E;      /* 옐로우 */
  --bg-color: #FFFDF5;    /* 크림색 배경 */
  ```
- **로고 디자인**
  - `logo.png` (48x48px, 라운드 처리)
  - 헤더 좌측에 이미지+텍스트 조합
- **폴라로이드 카드**
  - 흰색 배경, 그림자 효과
  - 가로 스크롤 지원 (2개 이상 게시글 시)
  - 호버 시 살짝 들어올리는 애니메이션
- **모바일 최적화**
  - 768px 이하: 캘린더 좌우 패딩 축소
  - 520px 이하: 폴라로이드 카드 폭 축소

---

## 🗂️ 데이터베이스 구조

### users 테이블
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### posts 테이블
```sql
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255),
    content TEXT NOT NULL,
    category VARCHAR(50) NOT NULL,
    rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    date DATE NOT NULL,
    place_name VARCHAR(255),
    place_address VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### photos 테이블
```sql
CREATE TABLE photos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);
```

---

## 🔧 핵심 코드 구조

### 1. 데이터베이스 연결 (includes/db.php)
- `config.ini` 파일에서 DB 설정 로드
- PDO 객체 생성 (UTF-8, 예외 모드)
- MariaDB 인증 플러그인 문제 대응 (상세 에러 메시지)

### 2. 인증 가드 (includes/auth_guard.php)
```php
if (!isset($_SESSION['user_id'])) {
    // 로그인 페이지로 리디렉션
    echo "<script>alert('로그인이 필요합니다.'); 
          window.location.href='login.php';</script>";
    exit;
}
```

### 3. 캘린더 렌더링 (calendar.js)
```javascript
async function render() {
    // 1. API로 월별 게시글 있는 날짜 조회
    const res = await fetch(`./api/fetch_month.php?year=${year}&month=${month}`);
    const data = await res.json();
    const activeDates = data.dates || [];
    
    // 2. 캘린더 그리드 생성
    for (let d = 1; d <= lastDate; d++) {
        const cell = document.createElement('div');
        cell.textContent = d;
        
        // 3. 게시글 있는 날짜에 인디케이터 추가
        if (activeDates.includes(d)) {
            const dot = document.createElement('div');
            dot.className = 'indicator';
            cell.appendChild(dot);
        }
        
        // 4. 클릭 시 해당 날짜 게시글 로드
        cell.addEventListener('click', () => loadDay(year, month, d));
    }
}
```

### 4. 게시글 작성 처리 (write_process.php)
```php
// 트랜잭션 시작
$conn->begin_transaction();

try {
    // 1. 게시글 저장
    $stmt = $conn->prepare("INSERT INTO posts (...) VALUES (?, ?, ...)");
    $stmt->execute([...]);
    $post_id = $conn->insert_id;
    
    // 2. 사진 업로드 처리
    foreach ($_FILES['photos']['tmp_name'] as $key => $tmp_name) {
        // MIME 타입 검증
        if (!in_array($file_type, ['image/jpeg', 'image/png', 'image/gif'])) {
            throw new Exception("지원하지 않는 이미지 형식");
        }
        
        // 파일 저장
        $new_name = uniqid('img_', true) . '.' . $ext;
        move_uploaded_file($tmp_name, $upload_dir . $new_name);
        
        // DB에 경로 저장
        $photo_stmt->execute([$post_id, $file_path]);
    }
    
    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    die("오류: " . $e->getMessage());
}
```

---

## 🛡️ 보안 고려사항

✅ **SQL Injection 방어**: Prepared Statement 사용  
✅ **XSS 방어**: `htmlspecialchars()` 출력 이스케이프  
✅ **비밀번호 보안**: `password_hash()` (bcrypt)  
✅ **파일 업로드 보안**:
  - MIME 타입 검증
  - 파일 크기 제한 (5MB)
  - 파일명 난수화 (경로 traversal 방지)
✅ **세션 관리**: `session_start()` + `auth_guard.php`  
✅ **카테고리 화이트리스트**: `in_array()` 검증

---

## 📦 설치 및 실행

### 1. 환경 요구사항
- PHP 7.4 이상
- MariaDB 10.4 이상 또는 MySQL 5.7 이상
- Apache (또는 PHP 내장 서버)
- GD 라이브러리 (이미지 처리)

### 2. 설치 순서
```bash
# 1. 프로젝트 클론
git clone <repository-url>
cd project1205

# 2. 데이터베이스 설정
# - phpMyAdmin 또는 MySQL 클라이언트에서 README.md의 SQL 스키마 실행

# 3. config.ini 파일 생성
cp config.ini.example config.ini
# DB_HOST, DB_USER, DB_PASS, DB_NAME 설정

# 4. 업로드 폴더 권한 설정
chmod 755 public/uploads

# 5. Apache 실행 (XAMPP의 경우)
# - XAMPP Control Panel에서 Apache, MySQL 시작

# 6. 브라우저 접속
# http://localhost/project1205/
```

### 3. 테스트 플로우
1. 회원가입 (`views/register.php`)
2. 로그인 (`views/login.php`)
3. 메인 캘린더에서 "✏️ 기록하기" 클릭
4. 게시글 작성 (날짜, 카테고리, 평점, 내용 입력 + 사진 업로드)
5. 저장 후 캘린더에서 해당 날짜 클릭하여 확인
6. 📋 전체 기록 버튼으로 리스트 뷰 전환

---

## 🎯 주요 개선 이력

### v1.0 (초기 버전)
- 기본 CRUD 기능 구현
- 캘린더 UI 구현

### v1.2 (현재)
- ✅ 사진 업로드 기능 추가 (최대 2장, 5MB 제한)
- ✅ 로고 이미지 적용 (logo.png)
- ✅ 전체 기록 리스트 뷰 추가 (fetch_all_posts.php)
- ✅ 캘린더/리스트 토글 버튼 구현
- ✅ 장소 정보 필드 추가 (place_name, place_address)
- ✅ 폴라로이드 카드 디자인 개선
- ✅ 반응형 디자인 최적화
- ✅ 과거 평문 비밀번호 자동 해시 업그레이드
- ✅ 카테고리별 필터링 UI
- ✅ 게시글 수정 시 사진 추가/삭제 기능
- ❌ Kakao Maps API 통합 제거 -> Openstreetapi 

### 향후 개선 계획
- [ ] 이미지 썸네일 자동 생성 (GD 라이브러리)
- [ ] 월별 통계 대시보드 (게시글 수, 평균 평점)
- [ ] 검색 기능 (제목/내용/장소)



## 📄 라이선스

이 프로젝트는 교육 목적으로 개발되었습니다.
