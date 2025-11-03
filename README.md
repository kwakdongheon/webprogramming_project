# 2025-2 웹응용 프로그래밍 프로젝트 개발 계획서
**일상 기록 중심의 캘린더 기반 장소 로깅 서비스**

***

## 1. 프로젝트 개요

### 1.1 서비스 주제
개인의 일상 경험(맛집, 카페, 여행, 취미 활동 등)을 **캘린더 중심**으로 기록하고 회고하는 프라이빗 라이프로그 웹 서비스

### 1.2 주제 선정 배경
- **기존 SNS의 한계**: 불특정 다수에게 공개되는 부담감, 타인의 반응에 의존적
- **개인 기록 니즈 증가**: Z세대를 중심으로 "나만의 아카이브" 구축 트렌드 확산 (Repov 앱 사례: 2024년 출시 후 빠르게 성장)[9]
- **시간 중심 회고의 가치**: 달력 뷰를 통해 과거의 활동 패턴을 한눈에 파악하고 의미 있는 인사이트 도출
- **통합 관리의 편의성**: 장소, 사진, 평점, 메모를 한 곳에서 체계적으로 관리

**벤치마킹 서비스**: Repov (모바일 앱), Polarsteps (여행 기록), Day One (일기)

***

## 2. 핵심 기능 정의 

### 2.1 필수 기능 (Phase 1 - MVP)

#### **A. 사용자 인증**
- 회원가입 / 로그인 (이메일 + 비밀번호)
- JWT 기반 세션 관리
- *간소화*: 소셜 로그인은 Phase 2로 연기

#### **B. 캘린더 뷰 (메인 화면)**
- 월별 캘린더 표시
- 기록이 있는 날짜에 시각적 인디케이터 표시 (점, 아이콘 등)
- 날짜 클릭 시 해당 날짜의 게시글 목록 모달/사이드바로 표시
- 이전/다음 달 탐색 기능

#### **C. 게시글 작성**
- **필수 항목**:
  - 날짜 선택 (기본값: 오늘)
  - 제목 (선택) / 내용 (필수)
  - 카테고리 선택 (맛집, 카페, 여행, 취미, 일상 등 5-7개 고정)
  - 평점 (1-5점 별점)
- **선택 항목**:
  - 장소 검색 및 등록 (Kakao Local API)
  - 사진 업로드 (최대 3장으로 제한)
- **간소화**: 시간은 제외하고 날짜만 관리 (복잡도 감소)

#### **D. 게시글 조회**
- 캘린더에서 날짜 선택 시 해당 날짜 게시글 목록
- 개별 게시글 상세 보기 (제목, 내용, 평점, 카테고리, 사진, 장소 정보)
- 등록된 장소가 있을 경우 지도에 마커 표시 (Kakao Map API)

#### **E. 게시글 수정/삭제**
- 본인이 작성한 게시글만 수정/삭제 가능

#### **F. 데이터베이스 설계**
```
Users (사용자)
├─ id (PK)
├─ email (unique)
├─ password (hashed)
├─ username
└─ created_at

Posts (게시글)
├─ id (PK)
├─ user_id (FK → Users)
├─ title
├─ content
├─ category
├─ rating (1-5)
├─ date (날짜만, 시간 제외)
├─ place_name (선택)
├─ place_address (선택)
├─ latitude (선택)
├─ longitude (선택)
├─ created_at
└─ updated_at

Photos (사진)
├─ id (PK)
├─ post_id (FK → Posts)
├─ file_url
└─ uploaded_at
```

### 2.2 Phase 2 기능 (향후 확장)
- 댓글 기능 (친구 초대 시스템 구현 후)
- 태그/해시태그 기능
- 카테고리별 필터링 및 통계 (월별 방문 횟수, 평균 평점 등)
- 지도 뷰 (모든 방문 장소를 지도에 한눈에 표시)
- 데이터 내보내기 (JSON/PDF)
- 프로필 커스터마이징

***

## 3. 기술 스택

### 3.1 프론트엔드
- **언어**: JavaScript (ES6+)
- **프레임워크**: **React.js** (컴포넌트 재사용성, 생태계 풍부)
- **상태관리**: Context API (Redux는 불필요, 과도한 보일러플레이트)
- **주요 라이브러리**:
  - `react-calendar` 또는 `@fullcalendar/react`: 캘린더 UI
  - `axios`: HTTP 통신
  - `react-router-dom`: 페이지 라우팅
  - `react-rating-stars-component`: 별점 입력/표시
- **스타일링**: **Tailwind CSS** (빠른 프로토타이핑, 일관된 디자인)

### 3.2 백엔드
- **언어**: **Node.js** (프론트엔드와 동일 언어로 러닝커브 감소)
- **프레임워크**: **Express.js** (경량, 직관적)
- **데이터베이스**: **PostgreSQL** (관계형 데이터 구조에 적합, 무료 호스팅 가능)
- **인증**: `jsonwebtoken` (JWT), `bcrypt` (비밀번호 해싱)
- **파일 업로드**: `multer` (이미지 업로드), **Cloudinary** (무료 이미지 호스팅)
- **API 검증**: `express-validator` (입력값 검증)

### 3.3 외부 API (무료 사용량 상세)

#### 🗺️ Kakao Local API (장소 검색)
**기능**: 키워드 기반 장소 검색 (식당, 카페 등)

#### 🗺️ Kakao Map API (지도 표시)
**기능**: 웹 페이지에 지도 표시, 마커 표시

#### 📷 Cloudinary (이미지 호스팅)
**기능**: 이미지 업로드, 저장, CDN 제공


### 3.4 개발 도구
- **버전 관리**: Git + GitHub
- **협업**: GitHub Projects (간트차트, 이슈 트래킹)
- **API 테스트**: Postman
- **디자인 프로토타이핑**: Figma 

***

## 4. 개발 일정 (8-10주 권장)

### 📅 Week 1: 기획 및 설계
- 기능 명세서 최종 확정
- DB 스키마 설계 및 ERD 작성
- API 엔드포인트 설계 (RESTful)
- **Kakao Developers 앱 생성 및 API 키 발급**
- **Cloudinary 계정 생성**
- Figma로 와이어프레임 작성 (선택)
- Git 저장소 생성 및 협업 규칙 정의

### 📅 Week 2-3: 백엔드 개발 (기초)
- 프로젝트 환경 설정 (Express.js, PostgreSQL 연결)
- 사용자 인증 API (회원가입, 로그인, JWT 발급)
- 게시글 CRUD API 구현
- 사진 업로드 API (Cloudinary 연동)
- **마일스톤**: Postman으로 모든 API 테스트 완료

### 📅 Week 4: 백엔드 개발 (고급)
- 장소 검색 API (Kakao Local API 연동)
- 에러 핸들링 및 입력값 검증 강화
- 백엔드 배포 (Render/Railway)
- **환경변수 설정** (.env 파일에 API 키 저장)

### 📅 Week 5-6: 프론트엔드 개발 (기초)
- React 프로젝트 초기 설정
- 로그인/회원가입 페이지
- 캘린더 뷰 구현 (react-calendar)
- 게시글 목록 컴포넌트

### 📅 Week 7-8: 프론트엔드 개발 (고급)
- 게시글 작성 폼 (평점, 카테고리, 사진 업로드)
- 장소 검색 UI (Kakao Local API 연동)
- 게시글 상세 페이지 (Kakao Map으로 지도 표시)
- 백엔드 API 연동

### 📅 Week 9: 통합 테스트 및 버그 수정
- 프론트-백엔드 통합 테스트
- **API 사용량 모니터링** (Kakao Developers 콘솔)
- 크로스 브라우저 테스트 (Chrome, Safari, Firefox)
- 모바일 반응형 테스트
- 주요 버그 수정

### 📅 Week 10: 배포 및 최적화
- 프론트엔드 배포 (Vercel)
- 백엔드 배포 확인 (환경변수 설정)
- 성능 최적화 (이미지 압축, API 캐싱)
- README 작성 및 발표 자료 준비

***

## 5. 팀 역할 분담 (3인 기준)

### 👤 팀원 A (팀장 / 백엔드 리드)
**주요 업무**:
- 프로젝트 일정 관리 및 GitHub 이슈 트래킹
- DB 스키마 설계 및 PostgreSQL 설정
- 사용자 인증 시스템 (회원가입, 로그인, JWT)
- 게시글 CRUD API 개발
- **Kakao Local API 연동** (장소 검색)
- **Cloudinary 연동** (이미지 업로드)
- 백엔드 배포 및 환경 설정

**필요 역량**: Node.js, Express, PostgreSQL, REST API, JWT

***

### 👤 팀원 B (프론트엔드 리드)
**주요 업무**:
- React 프로젝트 초기 설정 및 구조 설계
- 캘린더 뷰 구현 (라이브러리 선정 및 커스터마이징)
- 게시글 작성/수정 폼 UI 개발
- 평점/카테고리 선택 컴포넌트
- 반응형 디자인 (모바일 최적화)
- 프론트엔드 배포 (Vercel)

**필요 역량**: React.js, JavaScript, Tailwind CSS, 컴포넌트 설계

***

### 👤 팀원 C (풀스택 / API 연동 담당)
**주요 업무**:
- 프론트-백엔드 API 연동 작업 (Axios)
- 장소 검색 UI 및 **Kakao Map API 통합**
- 게시글 상세 페이지 (지도 마커 표시)
- 이미지 업로드 기능 (프론트+백엔드 협업)
- 통합 테스트 및 버그 픽스
- **API 사용량 모니터링 및 보고**
- (여유 있을 시) 카테고리별 필터링 기능

**필요 역량**: React + Node.js 기본, API 통합, 문제 해결 능력

***

### 📊 협업 규칙
- **주 2회 정기 회의** (월요일: 주간 계획, 금요일: 진행상황 공유)
- **GitHub 브랜치 전략**: 
  - `main`: 배포 브랜치
  - `develop`: 개발 통합 브랜치
  - `feature/기능명`: 기능별 개발 브랜치
- **코드 리뷰**: 모든 PR은 1명 이상 리뷰 후 병합
- **커밋 컨벤션**: 
  ```
  feat: 새 기능 추가
  fix: 버그 수정
  style: 코드 포맷팅
  refactor: 코드 리팩토링
  docs: 문서 수정
  ```

***


## 6. 참고 자료

### 공식 문서
- [Kakao Developers - 지도 API](https://developers.kakao.com/docs/latest/ko/local/dev-guide)
- [Kakao Developers - 쿼터 정책](https://developers.kakao.com/docs/latest/ko/getting-started/quota)[10]
- [Cloudinary 문서](https://cloudinary.com/documentation)
- [Cloudinary 무료 플랜 FAQ](https://support.cloudinary.com/hc/en-us/articles/202521662)[13]

### 참고 사례
- [Repov 앱 소개](https://apps.apple.com/us/app/repov-a-mini-blog-for-moments/id6502975294)[9]
- [웹 개발 프로젝트 타임라인 가이드](https://www.ramotion.com/blog/how-long-to-develop-website/)[17]

***
