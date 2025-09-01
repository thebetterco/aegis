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

- PHP 8.2+
- Composer
- MySQL
- MongoDB (PHP 확장 2.1+)
- Nginx
- PHP-FPM (PHP 8.2)

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

## Nginx + PHP-FPM Setup

For production or serving through a web server, configure Nginx to use PHP-FPM.

### Prerequisites

- Nginx
- PHP-FPM (matching the PHP version, e.g., `php8.2-fpm`)

### Front controller

The application entry point is `public/index.php`:

```php
<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
```

### Example Nginx configuration

```
server {
    listen 80;
    server_name example.com;
    root /path/to/aegis/public;

    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

After saving the configuration, restart the services:

```bash
sudo systemctl restart nginx php8.2-fpm
```

Ensure the `storage` and `bootstrap/cache` directories are writable by the web server user.
