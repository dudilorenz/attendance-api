<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>Attendance</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex flex-col gap-4 items-center justify-center min-h-screen">

<div class="bg-white p-6 rounded shadow w-96 text-center">

    <h1 class="text-xl font-bold mb-4">דיווח נוכחות</h1>

    <button onclick="clock('in')"
        class="w-full bg-green-600 text-white py-2 rounded mb-3 disabled:bg-gray-300 disabled:text-gray-500 disabled:cursor-not-allowed disabled:opacity-60">
        כניסה (IN)
    </button>

    <button onclick="clock('out')"
        class="w-full bg-red-600 text-white py-2 rounded mb-3 disabled:bg-gray-300 disabled:text-gray-500 disabled:cursor-not-allowed disabled:opacity-60">
        יציאה (OUT)
    </button>

    <div id="result" class="mt-2 text-sm text-gray-700"></div>

    <!-- Toggle daily report -->
    <button onclick="toggleReport()"
        class="mt-4 w-full bg-blue-600 text-white py-2 rounded">
        דוח יומי
    </button>


    <form method="POST" action="/logout" class="mt-6">
        @csrf
        <button class="w-full bg-gray-700 text-white py-2 rounded">
            התנתקות
        </button>
    </form>

</div>

<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4 text-center w-auto max-w-full sm:min-w-[400px]" id="dailyReportWrapper">
        <!-- Daily report (collapsed by default) -->
    <div class="mt-4 hidden text-right">

        <label class="block text-sm mb-1">תאריך</label>
        <input type="date" id="reportDate"
               class="border rounded px-2 py-1 w-full mb-2"
               value="{{ now()->toDateString() }}">

    </div>
    <button onclick="loadDailyReport()"
        class="w-full bg-blue-500 text-white py-2 rounded mb-2">
        רפרש דוח
    </button>
    <div id="dailyReport" class="text-sm text-gray-800 p-5"></div>
</div>

<script>
    // Data injected from backend (must stay in Blade)
    window.AttendanceConfig = {
        token: @json($token),
        baseUrl: '/api',
        loginUrl: '{{ route('login') }}'
    };

    function toggleReport() {
        document
            .getElementById('dailyReportWrapper')
            .classList.toggle('hidden');
    }
</script>

<script src="/js/attendance.js"></script>

</body>
</html>
