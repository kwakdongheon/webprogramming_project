# [2025-2 웹응용프로그래밍] LifeLog 프로젝트 최종 보고서

<div align="center">
    <img src="./public/images/logo.png" alt="LifeLog Logo" width="50" style="vertical-align: middle; margin-right: 10px;"/>
  <h3>  
    LifeLog: 일상 기록 중심의 캘린더 기반 장소 로깅 서비스
  </h3>
</div>

---

## 1. 웹 서비스 주제

### 1.1 웹 서비스 주제

**서비스명:** LifeLog (라이프로그)  
**한줄 소개:** 일상 기록 중심의 캘린더 기반 장소 로깅 서비스

LifeLog는 사용자의 소중한 일상(맛집, 여행, 취미, 소소한 하루 등)을 캘린더 뷰를 중심으로 기록하고 시각적으로 회고할 수 있는 프라이빗 웹 다이어리입니다. 복잡한 SNS 기능 없이 오직 '나의 기록'에만 집중할 수 있는 환경을 제공합니다.

---

## 2. 주제 선정 배경 및 이유

### 2.1 시장 환경 분석

- **디지털 저널 시장의 성장:** 글로벌 디지털 저널 앱 시장은 연평균 11.4% 성장하고 있으며, 개인의 기록에 대한 니즈가 지속적으로 증가하고 있습니다.

- **SNS 피로증후군:** 타인의 시선을 의식해야 하는 '보여주기식' 기록, 불필요한 정보 과부하로 인해 많은 사용자가 SNS 피로증후군을 호소하고 있습니다.

- **기존 서비스의 한계:** 블로그는 검색 노출에 대한 부담이 있고, 일반적인 일기 앱은 텍스트 위주라 '장소'나 '사진' 중심의 직관적인 회고가 어렵습니다.

### 2.2 개발 목적

* 좋아요나 조회수에 구애받지 않고 솔직한 감정을 기록할 수 있는 개인 공간 제공합니다
* 캘린더 인디케이터와 카드 UI를 통해 한 달간의 활동을 직관적으로 확인 가능합니다
* 지도 기능을 통해 방문 장소를 함께 기록하여 시간·공간 정보를 동시에 보관 가능하게 합니다.

---

## 3. 개발 언어 및 사용 도구

### 3.1 기술 스택 (Tech Stack)

| 구분 | 기술 / 도구 | 비고 |
| :--- | :--- | :--- |
| **Frontend** | HTML5, CSS3, JavaScript (ES6) | 시맨틱 마크업, Flexbox/Grid 레이아웃 |
| **Library** | Leaflet.js | 오픈소스 지도 라이브러리 (OpenStreetMap 연동) |
| **Backend** | PHP 7.4+ | 서버 사이드 스크립트, 세션 관리 |
| **Database** | MariaDB (MySQL 호환) | 관계형 데이터베이스 (RDBMS) |
| **Server** | Apache Web Server | 웹 서버 (XAMPP 환경 / 42web 호스팅) |
| **API** | Photon API (Komoot) | 장소 검색 및 지오코딩 (Geocoding) |

### 3.2 개발 및 협업 도구

- **IDE:** Visual Studio Code
- **Database Tool:** phpMyAdmin
- **VCS:** Git, GitHub (브랜치 전략: main, develop, feature)
- **Deployment:** FileZilla (FTP 배포)

---

## 4. 전체 시스템 개요

### 4.1 시스템 아키텍처 다이어그램

<div align="center">
  <img src="./public/images/flow.png" alt="시스템 아키텍처" width="800"/>
</div>


### 4.2 시스템 동작 방식 설명

저희 시스템은 **MVC(Model-View-Controller)** 패턴을 기반으로 구성되어 있으며, 주요 동작 방식은 다음과 같습니다.

1.  **사용자 인증 (Auth Module)**
    * 모든 보호된 페이지에서 `auth_guard.php`가 세션(`$_SESSION`)을 확인하여 로그인 상태를 검증합니다.
    * 로그인 요청 시 `login_process.php`가 사용자의 비밀번호를 `password_verify()` 함수로 검증합니다.
    * 비밀번호는 회원가입 시 `password_hash()`로 암호화되어 DB에 저장됩니다..

2.  **캘린더 및 데이터 조회 (Calendar Module)**
    * 사용자가 메인 페이지(`index.php`)에 접속하면 JavaScript(`calendar.js`)가 실행되어 현재 달의 캘린더를 렌더링합니다.
    * `api/fetch_month.php`를 AJAX로 호출하여 기록이 있는 날짜 목록을 받아옵니다.
    * 응답받은 날짜에 대해 캘린더 셀에 인디케이터(점)를 표시합니다.
    * 사용자가 특정 날짜를 클릭하면 `api/fetch_day.php`를 호출하여 해당 날짜의 게시글과 사진 목록을 JSON으로 가져옵니다.
    * 가져온 데이터를 기반으로 우측(또는 모바일 환경에서는 하단) 피드 영역에 카드 형태의 게시글 리스트를 동적으로 렌더링합니다.

3.  **게시글 작성 및 지도 연동 (Post & Map Module)**
    * 사용자는 `write_screen.php`에서 날짜, 제목, 카테고리, 평점, 내용, 사진을 입력합니다.
    * Leaflet.js를 통해 지도를 표시하고, 검색창에서 장소명을 입력하면 Photon API로 장소 검색 요청을 보냅니다.
    * Photon API 응답에 포함된 위도·경도와 주소 정보를 활용해 지도에 마커를 표시하고, 장소명/주소 입력 필드를 자동으로 채웁니다.
    * 사용자가 폼을 제출하면 `write_process.php`가 실행되어 트랜잭션을 시작합니다.
    * `posts` 테이블에 게시글 기본 정보를 저장하고, 업로드된 이미지를 `public/uploads/`에 저장한 뒤 경로를 `photos` 테이블에 기록합니다.
    * 중간에 오류가 발생하면 롤백하여 게시글과 사진 정보가 일부만 저장되지 않도록 한다.

4.  **데이터 흐름 제어**
    * `auth_guard.php`를 통해 로그인 여부를 공통으로 확인하여 비인가 사용자의 접근을 차단합니다.
    * `db.php`에서 PDO 기반 DB 연결을 생성하고, 모든 쿼리는 Prepared Statement로 실행하여 SQL Injection을 방지합니다.
    * 사진 파일은 실제 파일은 `public/uploads/` 디렉터리에 저장하고, DB에는 파일 경로만 저장하여 파일 및 데이터 관리를 분리합니다.

5.  **외부 API 연동**
    * Leaflet.js가 OpenStreetMap 타일 서버에서 지도 타일 이미지를 로드하여 브라우저에 지도를 표시합니다.
    * 사용자가 장소를 검색하면 Photon API에 HTTP 요청을 보내고, 응답으로 받은 GeoJSON 데이터를 파싱하여 지도의 마커 및 장소 정보에 활용합니다.

---

## 5. 팀 멤버 별 담당 업무

팀원 모두 웹 개발 입문자로서, 기능 단위로 역할을 명확히 분담하여 프로젝트를 수행했습니다.
| 이름 (학번) | 역할 | 주요 담당 업무 상세 |
| :--- | :---: | :--- |
| **곽동헌**(202255508) | **DB & Auth** | **[인증/보안 및 DB 설계]**• 데이터베이스 ERD 설계 및 스키마 구축 (Users, Posts, Photos 테이블)• 회원가입/로그인/로그아웃 로직 구현• 비밀번호 해싱 및 세션 보안 관리 (Auth Guard 구현)• DB 연결 설정 및 예외 처리 담당 |
| **김소윤**(202355610) | **Post CRUD** | **[게시글 관리 로직]**• 게시글 작성(Create), 조회(Read), 수정(Update), 삭제(Delete) 핵심 기능 구현• PHP PDO를 이용한 Prepared Statement 쿼리 작성 (SQL Injection 방지)• 게시글 데이터 바인딩 및 유효성 검사 로직 구현 |
| **안나연**(202127525) | **UI/UX & Map** | **[프론트엔드 및 지도/배포]**• 캘린더 동적 생성 로직(JS) 및 AJAX 데이터 연동 구현• **Photon API & Leaflet.js를 활용한 지도 검색 및 위치 저장 기능 구현**• 서비스 로고 디자인 및 전체 UI/UX 스타일링 (CSS)• 무료 호스팅 서버(42web) 환경 구축 및 FTP 배포 담당 |
---

## 6. 서비스 시연 시나리오 (Demo Scenario)

본 프로젝트의 핵심 기능을 효과적으로 보여주기 위한 데모 진행 순서입니다.

### 6.1 시나리오 1: 접속 및 회원가입
1.  **메인 접속:** 로그아웃 상태에서 접속하여 랜딩 페이지 디자인과 "시작하기" 버튼을 확인합니다.
2.  **회원가입:** '시작하기'를 눌러 회원가입 페이지로 이동합니다.
    * 닉네임, 이메일, 비밀번호를 입력하고 가입을 완료합니다.
   

### 6.2 시나리오 2: 일상 기록 작성
1.  **로그인 및 메인 진입:** 가입한 계정으로 로그인하여, 아직 기록이 없는 깨끗한 캘린더 화면을 보여줍니다.
2.  **작성 페이지 이동:** 상단의 '기록하기' 버튼을 클릭합니다.
3.  **내용 입력 및 지도 검색:**
    * 날짜는 오늘 날짜로 선택합니다.
    * **[지도 검색]:** 지도 검색창에 학교 이름(예: '부산대학교')이나 핫플레이스(예: '광안리')를 검색하고, 결과 마커를 클릭하여 주소가 자동 입력되는 기능을 시연합니다.
    * **[사진 업로드]:** 준비된 사진 파일을 선택하여 업로드합니다.
    * 카테고리(예: '여행')와 별점(5점)을 입력하고 저장합니다.

### 6.3 시나리오 3: 캘린더 뷰 및 상세 조회
1.  **인디케이터 확인:** 캘린더의 오늘 날짜에 '기록 있음'을 알리는 **주황색 점(Indicator)**이 생긴 것을 확인합니다.
2.  **피드 조회:** 해당 날짜를 클릭하여 우측(모바일은 하단)에 나타나는 **폴라로이드 카드 UI**를 보여줍니다.
    * 업로드한 사진, 별점, 위치 정보가 카드에 예쁘게 표시되는지 확인합니다.
3.  **상세 보기:** 카드 내 '보기' 버튼을 눌러 상세 페이지로 이동해 사진 갤러리 형식을 확인합니다.

### 6.4 시나리오 4: 리스트 뷰 및 필터링
1.  **뷰 전환:** 헤더의 '📋 전체 기록' 토글 버튼을 클릭하여 리스트 뷰로 전환합니다.
2.  **필터링:** 상단의 카테고리 버튼(맛집, 여행 등)을 눌러 게시글이 실시간으로 필터링되는 것을 시연합니다.

### 6.5 시나리오 5: 수정/삭제 및 로그아웃
1.  **수정:** 게시글 '수정' 버튼을 눌러 내용을 변경하고 저장하여 반영 여부를 확인합니다.
2.  **삭제:** '삭제' 버튼을 눌러 게시글이 캘린더와 리스트에서 사라지는 것을 확인합니다.
3.  **로그아웃:** 상단 '로그아웃' 버튼을 눌러 시연을 종료합니다.

---

## 7. 개발 과정 중 주요 이슈 및 해결 방법

### 7.1 지도 API 선정 및 변경

**문제 상황**
- 초기에 Kakao Maps API를 사용하려 했으나, 무료 호스팅 환경(42web.io)에서 CORS 정책 위반 및 API 키 로딩 실패 문제 지속 발생하였습니다
- 콘솔 오류: `TypeError: Cannot read properties of undefined (reading 'keywordSearch')`
- 상용 서비스가 아닌 교육용 프로젝트 특성상 API 키 승인 지연되었습니다.

**해결 방법**
- **Leaflet.js + Photon API**로 전면 교체하였습니다.
- Leaflet은 오픈소스 라이브러리로 API 키 불필요하고, OpenStreetMap 기반으로 안정적이으로 이를 통해 해결하였습니다.
- Photon API는 무료 지오코딩 서비스로 한국어 장소 검색 지원합니다.
- 코드 수정: `https://photon.komoot.io/api/?q={검색어}&limit=5` 형식으로 AJAX 요청하였습니다.

---

### 7.2 Photon API 400 Bad Request 오류

**문제 상황**
- Photon API 호출 시 모든 검색어에 대해 `400 Bad Request` 에러 발생하였습니다.
- 브라우저 콘솔: `GET https://photon.komoot.io/api/?q=부산대학교&lang=ko&limit=5 400`

**원인 분석**
1. **URL 경로 오류**: `/api/` (슬래시 포함) → `/api` (슬래시 제거)로 수정하였습니다.
2. **지원하지 않는 파라미터**: Photon API는 `lang=ko` 파라미터를 공식 지원하지 않았습니다. (en, de, fr, it만 지원)

**해결 방법**
```javascript
// 수정 전 (오류)
fetch(`https://photon.komoot.io/api/?q=${query}&lang=ko&limit=5`)

// 수정 후 (정상)
fetch(`https://photon.komoot.io/api?q=${query}&limit=5`)
```
- `lang` 파라미터 제거해도 OpenStreetMap 데이터 자체에 한국어 명칭 포함되어 검색 가능하였습니다.


---

### 7.3 게시글 수정 시 사진 추가/삭제 기능 구현

**문제 상황**
- 초기 버전에서는 게시글 수정 시 텍스트만 수정 가능, 사진 관리 불가하였습니다.
- 사용자 요구사항: 기존 사진 삭제 + 새 사진 추가 동시 처리를 요구하였습니다.

**해결 방법**
1. **UI 개선**: 기존 사진에 체크박스 추가(삭제 표시) 하였습니다.
2. **Backend 트랜잭션 처리**:
```php
$conn->begin_transaction();
try {
    // 1. 체크된 사진 삭제
    foreach ($_POST['delete_photos'] as $photo_id) {
        // 파일 경로 조회 → 물리 파일 삭제 → DB 레코드 삭제
        unlink($file_path);
    }
    
    // 2. 새 사진 업로드 (최대 2장 제한 유지)
    $existing_count = count($remaining_photos);
    $new_count = count($_FILES['new_photos']);
    if ($existing_count + $new_count > 2) {
        throw new Exception("사진은 최대 2장까지만 가능합니다.");
    }
    
    // 3. 게시글 텍스트 UPDATE
    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
}
```

---

### 7.4 JavaScript 버튼 HTML 렌더링 오류

**문제 상황**
- 캘린더 피드의 수정/삭제 버튼이 화면에 표시되지 않았습니다.
- 브라우저 개발자 도구에서 HTML 구조가 깨진 것울 확인하였습니다.

**원인 분석**
```javascript
// calendar.js 잘못된 코드
<button onclick="location.href='...'"수정</button>  // 닫는 괄호 '>' 누락
```

**해결 방법**
```javascript
// 수정 후
<button class="btn btn-secondary" onclick="location.href='views/post_edit.php?id=${p.id}'>✏️ 수정</button>
```
- 템플릿 리터럴 내 HTML 문자열 작성 시 따옴표 누락 주의하였습니다.
- 이모지 추가로 UI 가독성 향상 시켰습니다.

---

### 7.5 CSS 버튼 크기 불일치 문제

**문제 상황**
- 헤더의 "기록하기", "로그아웃" 버튼 크기가 서로 달라 UI 일관성 저하되었습니다.
- 게시글 카드의 수정/삭제 버튼도 인라인 스타일로 중복 관리되었습니다.

**해결 방법**
```css
/* calendar.css 통일된 버튼 스타일 */
.btn {
    padding: 10px 20px;
    font-size: 0.95rem;
    white-space: nowrap;  /* 텍스트 줄바꿈 방지 */
}

.logout-btn {
    background: #FF7675;
    padding: 10px 20px;  /* .btn과 동일 */
}
```
- 인라인 스타일 제거하고 클래스 기반 스타일링으로 통일하였습니다.

---

### 7.6 MariaDB 비밀번호 인증 플러그인 오류

**문제 상황**
- XAMPP 환경에서 DB 연결 시 `Authentication plugin 'caching_sha2_password' failed` 오류가 발생하였습니다.
- PDO 연결은 성공하지만 MySQLi 연결은 실패하였습니다.

**해결 방법**
1. **단기 해결**: `mysql_native_password` 플러그인으로 변경하였습니다.
```sql
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'password';
FLUSH PRIVILEGES;
```

2. **장기 해결**: PDO 우선 사용하고, MySQLi는 레거시 코드에서만 제한적 사용합니다.

---

### 7.7 파일 업로드 MIME 타입 검증

**문제 상황**
- 악의적인 사용자가 PHP 파일을 이미지로 위장하여 업로드 시도가 가능하였습니다.
- 파일 확장자만 체크하는 것은 보안상 취약하였습니다.

**해결 방법**
```php
$file_type = mime_content_type($_FILES['photos']['tmp_name'][$key]);
$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];

if (!in_array($file_type, $allowed_types)) {
    throw new Exception("지원하지 않는 이미지 형식입니다.");
}

// 파일 크기 제한 (5MB)
if ($_FILES['photos']['size'][$key] > 5 * 1024 * 1024) {
    throw new Exception("파일 크기는 5MB를 초과할 수 없습니다.");
}

// 파일명 난수화 (경로 순회 공격 방지)
$new_name = uniqid('img_', true) . '.' . $ext;
```


