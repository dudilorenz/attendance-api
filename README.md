#Attendance API (Laravel)

A simple REST API for tracking employee attendance events (IN/OUT) and calculating daily working hours.


--

##Tech Stack
- PHP 8.2+
- Laravel 11
- MySQL
- Composer

--

##Installation
```bash
git clone https://github.com/dudilorenz/attendance-api.git
cd attendance-api
composer install
cp .env.example .env
php artisan key:generate


##Configure Database (.env)
DB_DATABASE=attendance_db
DB_USERNAME=root
DB_PASSWORD=


##Run migrations:
php artisan migrate


##Start the server:
php artisan serve


##Server will be available at:
http://127.0.0.1:8000


##API Endpoints
POST /api/entry

Request Body (JSON):
{
  "workerId": 12345,
  "event_time": "2025-02-01 09:00:00",
  "type": "IN"
}

Responses:

201 – Event stored successfully
409 – Duplicate event (same workerId + event_time)
422 – Validation error


##Daily Attendance Report
GET /api/report/{workerId}?date=YYYY-MM-DD

Example:
GET /api/report/12345?date=2025-02-01


Response:
{
  "workerId": 12345,
  "date": "2025-02-01",
  "totalHours": "05:00",
  "entries": [
    { "type": "IN", "time": "09:00" },
    { "type": "OUT", "time": "14:00" }
  ],
  "errors": []
}


##Project Structure
app/
 ├── Http/
 │   ├── Controllers/AttendanceController.php
 │   └── Requests/StoreAttendanceEventRequest.php
 ├── Models/AttendanceEvent.php
database/
 └── migrations/


