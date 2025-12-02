# 프로젝트 파일 구조

```
webprogramming_project/
│
├── api/                          # API 엔드포인트
│   ├── fetch_month.php          # 월별 게시글 있는 날짜 조회
│   └── fetch_day.php            # 특정 날짜의 게시글 목록 조회
│
├── includes/                     # 공통 포함 파일
│   ├── db.php                   # 데이터베이스 연결
│   └── auth_guard.php           # 로그인 세션 확인
│
├── public/                       # 공개 리소스
│   ├── css/
│   │   └── calendar.css         # 캘린더 스타일
│   ├── js/
│   │   └── calendar.js          # 캘린더 동적 렌더링 로직
│   └── uploads/                 # 업로드된 사진 저장 폴더
│
├── views/                        # 뷰 템플릿
│   ├── login.php                # 로그인 페이지
│   ├── register.php             # 회원가입 페이지
│   ├── write_screen.php         # 게시글 작성 폼
│   ├── post_view.php            # 게시글 상세 보기
│   └── post_edit.php            # 게시글 수정 폼
│
├── index.php                     # 메인 페이지 (캘린더)
├── login_process.php            # 로그인 처리
├── register_process.php         # 회원가입 처리
├── write_process.php            # 게시글 작성 처리 (사진 업로드 포함)
├── post_edit_process.php        # 게시글 수정 처리
├── post_delete.php              # 게시글 삭제 처리
├── logout.php                   # 로그아웃
├── posts.php                    # 게시글 목록 (선택적)
└── README.md                     # 프로젝트 개요 및 계획서
```

## 주요 기능 구현 현황

### ✅ 완료된 기능

#### 1. 프로젝트 구조 정리
- 기능별 폴더 분류 (api, includes, views, public)
- 파일 경로 수정 및 정리

#### 2. 캘린더 메인 페이지 (index.php)
- 월별 캘린더 UI 구현
- 로그인 상태에 따른 화면 분기
- 반응형 디자인 적용

#### 3. 캘린더 CSS (public/css/calendar.css)
- 그라디언트 헤더 디자인
- 7열 그리드 레이아웃 (일~토)
- 게시글 있는 날짜 강조 (노란 배경, 주황 인디케이터)
- 모바일 반응형 (768px, 520px 브레이크포인트)

#### 4. 캘린더 JavaScript (public/js/calendar.js)
- 월별 캘린더 동적 생성
- 이전/다음 달 네비게이션
- 날짜 클릭 시 게시글 목록 표시
- AJAX로 서버 데이터 비동기 로드
- 키보드 접근성 지원 (Enter/Space)

#### 5. API 엔드포인트
- **fetch_month.php**: 특정 월에 게시글이 있는 날짜(일) 배열 반환
- **fetch_day.php**: 특정 날짜의 게시글 상세 정보 + 사진 경로 반환

#### 6. 사진 업로드 기능
- 게시글 작성 시 최대 2장 업로드
- 파일 크기 제한 (5MB)
- MIME 타입 검증 (JPG, PNG, GIF)
- 파일명 난수화 (보안)
- 미리보기 기능
- photos 테이블에 경로 저장

#### 7. 게시글 작성 페이지 개선 (views/write_screen.php)
- 현대적인 UI/UX
- 장소 정보 입력 필드 추가
- 사진 업로드 인터페이스
- 실시간 미리보기

### 📋 안나연 담당 업무 완료 내역

| 주차 | 업무 | 상태 |
|------|------|------|
| Week 1 | 메인 페이지 HTML 구조 작성 | ✅ 완료 |
| Week 2 | 캘린더 HTML/CSS 작성 | ✅ 완료 |
| Week 3 | JavaScript 캘린더 동적 생성 및 AJAX | ✅ 완료 |
| Week 3 | 사진 업로드 기능 구현 | ✅ 완료 |

### 🔄 다음 단계 (선택적)

- [ ] 게시글 수정 시 사진 업로드/삭제 기능
- [ ] 이미지 썸네일 최적화
- [ ] 카테고리별 필터링
- [ ] 통계 대시보드
- [ ] Kakao Map API 연동

## 사용 방법

### 1. 데이터베이스 설정
README.md의 SQL 스키마를 참고하여 MariaDB에 테이블 생성

### 2. 서버 실행
XAMPP에서 Apache와 MariaDB 시작

### 3. 접속
브라우저에서 `http://localhost/webprogramming_project/` 접속

### 4. 테스트 플로우
1. 회원가입 (`views/register.php`)
2. 로그인 (`views/login.php`)
3. 메인 캘린더에서 "새 기록 작성" 클릭
4. 게시글 작성 (사진 최대 2장 업로드 가능)
5. 저장 후 캘린더에서 해당 날짜 클릭하여 확인

## 보안 고려사항

- ✅ Prepared Statement로 SQL Injection 방어
- ✅ `password_hash()` / `password_verify()` 사용
- ✅ 세션 기반 인증
- ✅ 파일 업로드 시 MIME 타입 검증
- ✅ 파일명 난수화로 경로 traversal 방지
- ✅ 파일 크기 제한
- ✅ 카테고리 화이트리스트 검증
