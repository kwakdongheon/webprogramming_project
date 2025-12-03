# 배포 가이드

## 로컬 개발 환경 설정

1. **config.ini 파일 생성**
   ```bash
   cp config.ini.example config.ini
   ```

2. **config.ini 수정** (로컬 환경에 맞게)
   ```ini
   DB_HOST=localhost
   DB_NAME=lifelog_db
   DB_USER=root
   DB_PASS=
   ```

3. **데이터베이스 생성**
   - phpMyAdmin에서 `lifelog_db` 생성
   - README.md의 SQL 스키마 실행

4. **Apache 시작**
   - XAMPP 실행
   - `http://localhost/webprogramming_project/` 접속

---

## 서버 배포 방법

### 무료 호스팅 옵션

#### 1. InfinityFree (추천)
- **URL**: https://infinityfree.net
- **제공**: 무료 PHP + MySQL 호스팅
- **용량**: 5GB 저장공간, 무제한 트래픽

**배포 순서:**
1. InfinityFree 계정 생성
2. 새 웹사이트 생성 (무료 서브도메인 제공)
3. MySQL 데이터베이스 생성
4. FTP 정보 확인
5. FileZilla로 파일 업로드
6. `config.ini` 수정:
   ```ini
   DB_HOST=sql123.infinityfree.com
   DB_NAME=epiz_12345678_lifelog
   DB_USER=epiz_12345678
   DB_PASS=서버에서_받은_비밀번호
   ```
7. phpMyAdmin에서 테이블 생성

#### 2. 000webhost
- **URL**: https://www.000webhost.com
- **제공**: 무료 PHP + MySQL
- **용량**: 300MB, 3GB 대역폭

#### 3. Cloudflare Pages + Supabase (현대적 방식)
- **Cloudflare Pages**: 정적 파일 호스팅
- **Supabase**: 무료 PostgreSQL 데이터베이스
- PHP 대신 Serverless Functions 사용 필요

---

## GitHub Pages + Backend 분리 (권장)

**프론트엔드 (GitHub Pages)**
- HTML, CSS, JavaScript만 호스팅
- 무료, 빠름

**백엔드 (별도 호스팅)**
- PHP + MySQL은 InfinityFree 또는 Railway 사용
- API 방식으로 통신

---

## Railway 배포 (유료지만 간단)

1. **Railway 계정 생성**
   - https://railway.app
   - GitHub 연동

2. **프로젝트 생성**
   - "New Project" → "Deploy from GitHub repo"
   - MySQL 서비스 추가

3. **환경 변수 설정**
   - `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` 추가

4. **배포**
   - 자동 배포 완료
   - 제공된 URL로 접속

---

## 주의사항

1. **config.ini는 절대 Git에 커밋하지 마세요!**
   - `.gitignore`에 추가됨
   - `config.ini.example`만 공유

2. **서버 보안**
   - 배포 시 `APP_DEBUG=false`로 변경
   - 강력한 DB 비밀번호 사용

3. **파일 경로**
   - 서버마다 경로가 다를 수 있음
   - 상대 경로 사용 권장

4. **업로드 폴더 권한**
   - `public/uploads/` 폴더 쓰기 권한 확인
   - 일부 호스팅은 777 권한 필요

---

## 팀원과 협업 시

1. **Git Clone 후**
   ```bash
   git clone https://github.com/kwakdongheon/webprogramming_project.git
   cd webprogramming_project
   cp config.ini.example config.ini
   ```

2. **각자 config.ini 수정**
   - 자신의 로컬 DB 정보 입력
   - Git에 커밋되지 않음

3. **변경사항 Pull**
   ```bash
   git pull origin main
   ```
   - `config.ini`는 변경되지 않음 (개인 설정 유지)
