#Attendance API & UI (Laravel)

A Laravel-based attendance tracking system with session-based login (WEB) and token-based API (Sanctum).
Employees can clock IN / OUT, view daily attendance reports, and calculate total working hours.

## Features

- Employee login (email + password)
- Clock IN / OUT actions
- Daily attendance report
- Automatic total hours calculation
- Error detection (missing OUT, invalid sequences)
- UI built with Tailwind CSS
- API protected with Laravel Sanctum
- Session-based WEB auth + Bearer token for API calls

## Tech Stack
- PHP 8.2+
- Laravel 11
- MySQL
- Laravel Sanctum
- Tailwind CSS
- Composer

## Installation
- git clone https://github.com/dudilorenz/attendance-api.git
- cd attendance-api
- composer install
- cp .env.example .env
- php artisan key:generate

## Environment Configuration (.env)
APP_URL=http://127.0.0.1:8000

DB_DATABASE=attendance_db
DB_USERNAME=root
DB_PASSWORD=

## Database Setup
```bash
php artisan migrate
```

### This will create:
- users
- businesses
- employees
- attendance_events
- personal_access_tokens
- sessions

#Create Test Data (REQUIRED)
## 1. Create Business
`php artisan tinker`

$business = \App\Models\Business::create([
    'name' => 'Demo Business'
]);

## 2. Create User (Employee)
```bash
$user = \App\Models\User::create([
    'business_id' => $business->id,
    'name' => 'Test User',
    'email' => 'test@test.com',
    'password' => bcrypt('secret'),
    'role' => 'employee',
]);
```

## 3. Create Employee Profile (CRITICAL)
```bash
\App\Models\Employee::create([
    'business_id' => $business->id,
    'user_id' => $user->id,
    'worker_identifier' => '0215916',
    'job_title' => 'Developer',
    'status' => 'active',
]);
```

**⚠️ Without an Employee record, IN / OUT will fail.**

## Run the Server
```bash
php artisan serve
```

### Server URL:
http://127.0.0.1:8000

## Authentication Flow
1. Login (WEB)
2. GET  /login
3. POST /login


## Credentials:
*Email:*    test@test.com
*Password:* secret


## After login:

1. User is authenticated via session

2. Sanctum API token is generated

3. Token is stored in session

4. User is redirected to /attendance

## UI Pages
### Description
### /login - Login page
### /attendance - Attendance UI (protected)

## API Endpoints (Sanctum Protected)
### Clock IN
- POST /attendance/in
- Authorization: Bearer <token>

### Clock OUT
- POST /api/attendance/out
- Authorization: Bearer <token>

### Attendance Status
- GET /api/attendance/status
- Authorization: Bearer <token>


Response:
{
  "clocked_in": true
}

## Daily Report
GET /api/attendance/daily?date=YYYY-MM-DD
Authorization: Bearer <token>


Response:
```php
{
  "date": "2025-12-31",
  "total_hours": "04:45",
  "events": [
    { "type": "IN", "time": "09:00" },
    { "type": "OUT", "time": "13:45" }
  ],
  "errors": []
}
```

## Business Rules

- IN must be followed by OUT
- Multiple IN without OUT - return error - button disabled
- Missing OUT - reported in daily report
- Total hours calculated only from valid IN/OUT pairs
- Page refresh does not break state (status is loaded from API)
