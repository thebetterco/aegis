# Aegis

## 기능

- 이메일/비밀번호 회원가입 및 로그인: `/register`, `/login`
- 네이버 치지직 OAuth 로그인: `/oauth/chzzk`
- 유튜브 OAuth 로그인: `/oauth/youtube`
- 치지직 다운로드 스트림 목록 및 재생: `/chzzk/streams`
- 라이브 스트림 관리자 및 채팅 관리(치지직/유튜브 채팅 뮤트/밴): `/live/streams`
- 라이브 스트림 상태 모니터링 커맨드: `php artisan streams:check-live`
- 네이버 커머스 대시보드 (슈퍼관리자): `/naver-commerce`
- 완료된 치지직/유튜브 라이브를 큐에 쌓는 커맨드: `php artisan chzzk:queue`, `php artisan youtube:queue` (4시간마다 크론으로 실행)
- 치지직 스트림 다운로드 커맨드: `php artisan chzzk:download`
- 유튜브 스트림 다운로드 커맨드: `php artisan youtube:download`

## Installation

### Requirements

- PHP 8.1+
- Composer
- MySQL
- MongoDB

### Steps

1. Clone the repository and install PHP dependencies:
   ```bash
   git clone <repository-url>
   cd aegis
   composer install
   ```
2. Copy the example environment file and generate the application key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
3. Fill in `.env` with your MySQL, MongoDB, and OAuth credentials for Naver Chzzk, YouTube, and Naver Commerce.
4. Run database migrations and link the storage directory:
   ```bash
   php artisan migrate
   php artisan storage:link
   ```
5. Start the development server:
   ```bash
   php artisan serve
   ```
