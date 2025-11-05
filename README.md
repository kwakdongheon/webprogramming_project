# 2025-2 웹응용 프로그래밍 프로젝트 개발 계획서
**일상 기록 중심의 캘린더 기반 장소 로깅 서비스**

---

## 1. 프로젝트 개요

### 1.1 서비스 주제
개인의 일상 경험(맛집, 카페, 여행, 전시회, 영화, 스포츠 관람 같은 취미 활동 등)을 **캘린더 중심**으로 기록하고 회고하는 프라이빗 라이프로그 웹 서비스

### 1.2 주제 선정 배경

**시장 환경**
- 글로벌 디지털 저널 앱 시장은 2025년 65억 달러에서 2035년 194억 달러로 연평균 11.4% 성장 전망 [1]
- 레포브(Repov) 앱은 출시 5개월 만에 누적 다운로드 10만 건, 누적 기록 100만 건 달성 [2]
- 가트너는 2025년까지 소셜미디어 사용자의 50%가 플랫폼 이탈 또는 사용량 대폭 감소 예측 [3]

**국내 사용자 현황**
- 소셜미디어 이용자의 33.8%가 피로증후군 경험 (20-30대 여성 39%) [4]
- 응답자의 62.6%는 소셜미디어 관리 시간 대비 실질적 효용 낮다고 응답 [4]
- 한국 20대 평균 4.25개 소셜미디어 사용, 인스타그램 이용률 80.9% [5]
- Z·M세대의 51.1~59.6%가 개인정보 유출 불안 [6]

**서비스 필요성**
- 소셜미디어: 타인의 반응 의존성과 이미지 연출 부담
- 블로그: 검색 노출 우려와 시간 기반 회고의 어려움
- **본 서비스**: 완전 프라이빗 환경에서 형식 강요 없이 한 줄 메모부터 긴 회고까지 자유롭게 작성

| 구분 | SNS | 블로그 | 본 프로젝트 |
|---|---|---|---|
| 프라이버시 | ❌ 공개 압박 | △ 검색 노출 | ✅ 원하는 포스트만 공개 |
| 시간 회고 | ❌ 피드 흐름 | △ 최신순만 | ✅ 캘린더 뷰 |
| 사용 부담 | ❌ 꾸미기 강요 | △ 긴 글 압박 | ✅ 한 줄도 OK |
| 장소 관리 | △ 태그만 가능 | ❌ 수동 입력 | ✅ 지도 자동 연동 |

**벤치마킹 서비스**
- [Repov](https://apps.apple.com/us/app/repov-a-mini-blog-for-moments/id6502975294) (모바일 앱)
- Polarsteps (여행 기록)
- Day One (일기)

***

## 2. 핵심 기능 정의

### 2.1 필수 기능 (4주 MVP)

#### A. 사용자 인증
- 이메일/비밀번호 기반 회원가입·로그인
- PHP 세션 관리, `password_hash()` 함수로 비밀번호 해싱
- *간소화*: 소셜 로그인은 Phase 2로 연기

#### B. 캘린더 뷰 (메인 화면)
- 월별 캘린더 표시
- 기록이 있는 날짜에 시각적 인디케이터 배치 (점, 아이콘 등)
- 날짜 클릭 시 해당 날짜의 게시글 목록 표시
- 이전/다음 달 탐색 기능

#### C. 게시글 작성
**필수 항목**
- 날짜 선택 (기본값: 오늘)
- 내용
- 카테고리 선택 (맛집, 카페, 여행, 취미, 일상 등 5개 고정)
- 평점 (1-5점 별점)

**선택 항목**
- 제목
- 장소 정보 (텍스트 입력)
- 사진 업로드 (최대 2장)


#### D. 게시글 조회
- 캘린더에서 날짜 선택 시 해당 날짜 게시글 목록 표시
- 개별 게시글 상세 보기 (제목, 내용, 평점, 카테고리, 사진, 장소 정보)

#### E. 게시글 수정/삭제
- 작성자 본인만 수정/삭제 가능
- 세션 기반 권한 검증

#### F. 데이터베이스 설계

```sql
-- Users 테이블
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  username VARCHAR(50) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Posts 테이블
CREATE TABLE posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(100),
  content TEXT NOT NULL,
  category ENUM('맛집','카페','여행','취미','일상') NOT NULL,
  rating TINYINT CHECK (rating BETWEEN 1 AND 5),
  date DATE NOT NULL,
  place_name VARCHAR(100),
  place_address VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Photos 테이블
CREATE TABLE photos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  post_id INT NOT NULL,
  file_path VARCHAR(255) NOT NULL,
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);
```

### 2.2 Phase 2 기능 (추후 확장)
- Kakao Local API 연동 (장소 검색)
- Kakao Map API 연동 (지도 마커 표시)
- 카테고리별 필터링 및 통계 (월별 방문 횟수, 평균 평점)
- 태그/해시태그 기능
- 데이터 내보내기 (JSON/PDF)
- 프로필 커스터마이징

***

## 3. 기술 스택

### 3.1 프론트엔드
- **언어**: HTML5, CSS3, JavaScript (ES6)
- **캘린더 UI**: JavaScript와 CSS로 직접 구현 또는 FullCalendar.js 같은 경량 라이브러리 활용
- **스타일링**: CSS Flexbox, Media Query (반응형 디자인)

### 3.2 백엔드
- **언어/환경**: PHP (Apache 웹 서버)
- **데이터베이스**: MariaDB
- **DB 연결**: mysqli 또는 PDO, Prepared Statement로 SQL Injection 방어
- **파일 업로드**: `$_FILES` 슈퍼글로벌, `move_uploaded_file()` 함수 사용
- **보안**: `password_hash()`, `password_verify()`, 세션 관리

### 3.3 개발 도구
- **로컬 환경**: XAMPP (Apache + MariaDB + PHP)
- **DB 관리**: phpMyAdmin
- **버전 관리**: Git + GitHub
- **협업 관리**: GitHub Projects
- **테스트**: 브라우저 개발자 도구

### 3.4 시스템 구성

```
Browser (HTML/CSS/JS)
   └─ PHP (Apache, Session)
        ├─ Controllers (auth.php, posts.php, upload.php)
        ├─ Views (templates/*.php)
        ├─ Includes (db.php, auth_guard.php, helpers.php)
        └─ Public (uploads/, assets/)
   └─ MariaDB (users, posts, photos)
```

### 3.5 외부 API (Phase 2)
- **Kakao Local API**: 키워드 기반 장소 검색 ](https://developers.kakao.com/docs/latest/ko/local/dev-guide)[7]
- **Kakao Map API**: 웹 페이지에 지도 표시, 마커 배치 ](https://developers.kakao.com/docs/latest/ko/local/dev-guide)[7]

### 3.6 보안/품질 기준
- **인증**: `password_hash()` / `password_verify()` 적용, 세션 재생성으로 하이재킹 방지
- **입력 검증**: 서버 측 필수값 검증, 카테고리 화이트리스트, 평점 범위 체크 (1-5)
- **SQL 보안**: Prepared Statement 의무화, LIKE 검색 시 escape 처리
- **파일 보안**: 확장자/용량 제한, MIME 타입 확인, 파일명 난수화, 디렉터리 traversal 차단

***

## 4. 개발 일정 (4주)

### Week 1: 기획 및 환경 설정
- 기능 명세서 최종 확정
- ERD 작성 및 데이터베이스 스키마 설계
- XAMPP 설치, phpMyAdmin에서 DB 생성
- Git 저장소 생성, 협업 규칙 정의
- 기본 HTML 템플릿 작성 (로그인 페이지, 메인 페이지 구조)
- **주요 산출물**: ERD, DB 스키마, 기본 HTML 템플릿, Git 저장소

### Week 2: 백엔드 개발 및 기본 기능 구현
- 사용자 인증 (회원가입, 로그인, 로그아웃) 구현
- 세션 관리 및 비밀번호 해싱 적용
- 게시글 작성/조회 기능 구현 (HTML form + PHP 연동)
- mysqli/PDO로 DB CRUD 구현, Prepared Statement 적용
- **주요 산출물**: 회원가입/로그인 페이지, 게시글 작성/조회 기능

### Week 3: 캘린더 구현 및 고급 기능
- JavaScript로 월별 캘린더 UI 구현
- AJAX 또는 Fetch API로 날짜별 게시글 데이터 가져오기
- 게시글 수정/삭제 기능 추가
- 사진 업로드 기능 구현 (파일 저장 및 DB 경로 저장)
- 카테고리 선택, 별점 입력 UI 완성
- **주요 산출물**: 캘린더 뷰, 사진 업로드 기능, 게시글 수정/삭제 기능

### Week 4: 통합 테스트 및 최종 마무리
- 프론트엔드-백엔드 통합 테스트
- 버그 수정, 사용자 입력 검증 강화
- CSS로 UI 개선, 모바일 반응형 디자인 적용
- Chrome, Firefox 크로스 브라우저 테스트
- README 작성, 발표 자료 및 시연 영상 준비
- 무료 호스팅 서비스 배포 시도 (선택)
- **주요 산출물**: 완성된 웹 애플리케이션, README, 발표 자료

***

## 5. 팀 역할 분담

모든 팀원이 웹 프로젝트 초보임을 고려하여, **기능 단위로 역할을 분담**하고 서로의 코드를 리뷰하며 학습합니다.

### 👤 팀원 A: 인증 및 데이터베이스 담당
**담당 기능**
- 데이터베이스 설계 (ERD 작성, 스키마 생성)
- 회원가입 기능 (PHP + HTML form, 비밀번호 해싱)
- 로그인 기능 (세션 관리, 권한 검증)
- 로그아웃 기능

**주차별 업무**
- Week 1: ERD 작성, phpMyAdmin에서 DB 및 테이블 생성
- Week 2: 회원가입/로그인/로그아웃 구현
- Week 3: 세션 기반 권한 검증 강화
- Week 4: 보안 점검 (SQL Injection, XSS 방어)

**필요 역량**: PHP 기본 문법, HTML form 처리, MariaDB 기본 쿼리, 세션 관리

---

### 👤 팀원 B: 게시글 CRUD 담당
**담당 기능**
- 게시글 작성 (날짜, 제목, 내용, 카테고리, 평점 입력)
- 게시글 조회 (목록, 상세)
- 게시글 수정
- 게시글 삭제

**주차별 업무**
- Week 1: 게시글 작성 페이지 HTML 구조 작성
- Week 2: 게시글 작성/조회 기능 구현 (Prepared Statement 적용)
- Week 3: 게시글 수정/삭제 기능 구현
- Week 4: 입력 검증 강화, UI 개선

**필요 역량**: PHP 기본 문법, mysqli/PDO 사용법, HTML/CSS 기본

***

### 👤 팀원 C: 캘린더 및 사진 업로드 담당
**담당 기능**
- 캘린더 UI 구현 (월별 캘린더, 날짜별 인디케이터)
- 날짜 클릭 시 게시글 목록 표시
- 사진 업로드 기능 (파일 저장, DB 경로 저장)
- 게시글 상세 페이지에서 사진 표시

**주차별 업무**
- Week 1: 메인 페이지 HTML 구조 작성, 협업 규칙 문서화
- Week 2: 기본 캘린더 HTML/CSS 작성
- Week 3: JavaScript로 캘린더 동적 생성, AJAX로 게시글 데이터 가져오기, 사진 업로드 구현
- Week 4: 반응형 디자인 적용, README 작성, 발표 자료 준비

**필요 역량**: JavaScript DOM 조작, AJAX/Fetch API, PHP 파일 업로드, HTML/CSS

***

### 📊 협업 규칙
- **정기 회의**: 주 2회 (월요일: 주간 계획, 목요일: 진행 상황 공유)
- **GitHub 브랜치 전략**:
  - `main`: 최종 작동 버전 (배포용)
  - `develop`: 개발 통합 브랜치
  - `feature/기능명`: 개인 작업 브랜치
- **코드 리뷰**: 모든 Pull Request는 1명 이상 리뷰 후 병합
- **커밋 컨벤션**:
  - `feat: 기능 추가` (예: feat: 로그인 기능 구현)
  - `fix: 버그 수정` (예: fix: 세션 만료 오류 해결)
  - `style: 코드 포맷팅` (예: style: 들여쓰기 통일)
  - `docs: 문서 수정` (예: docs: README 업데이트)

***

## 6. 참고 자료

### 시장 조사 및 통계
1. [Digital Journal Apps Market 2025-2035 - Future Market Insights](https://www.futuremarketinsights.com/reports/digital-journal-apps-market)
2. [레포브(Repov) 10만 다운로드 달성 - BeSuccess](https://www.besuccess.com/%EB%B0%80%EB%A6%AC%EA%B7%B8%EB%9E%A8-%EA%B0%9C%EB%B0%9C%EC%82%AC%EC%9D%98-%EA%B4%80%EC%A0%90-%EA%B8%B0%EB%A1%9D-%EC%95%B1-%EB%A0%88%ED%8F%AC%EB%B8%8Crepov-%EB%88%84%EC%A0%81/)
3. [Gartner SNS 이탈 예측 - NDTV Profit](https://www.ndtvprofit.com/technology/50-consumers-will-significantly-limit-interactions-with-social-media-by-2025-gartner)
4. [엠브레인 SNS 피로증후군 조사 - TrendMonitor](https://trendmonitor.co.kr/tmweb/trend/allTrend/detail.do?bIdx=1302&code=0101&trendType=CKOREA)
5. [한국언론진흥재단 2024 소셜미디어 이용자 조사](https://www.kpf.or.kr/front/board/boardContentsView.do?board_id=246&contents_id=940a3bc4be914ac2a065b8922021728e)
6. [통계청 세대별 개인정보 불안 조사 - 한국경제](https://www.hankyung.com/article/202403110938i)
7. [Kakao Developers 지도 API](https://developers.kakao.com/docs/latest/ko/local/dev-guide)

### 기술 문서
- [W3Schools PHP Tutorial](https://www.w3schools.com/php/)
- [MDN Web Docs - JavaScript](https://developer.mozilla.org/ko/docs/Web/JavaScript)
- [MDN Web Docs - HTML](https://developer.mozilla.org/ko/docs/Web/HTML)
- [MDN Web Docs - CSS](https://developer.mozilla.org/ko/docs/Web/CSS)
- [PHP Manual - mysqli](https://www.php.net/manual/en/book.mysqli.php)
- [MariaDB Developer Quickstart](https://mariadb.com/resources/blog/developer-quickstart-php-mysqli-and-mariadb/)

### 참고 앱
- [Repov 앱 소개 - App Store](https://apps.apple.com/us/app/repov-a-mini-blog-for-moments/id6502975294)

***
