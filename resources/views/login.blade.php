<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

<form method="POST" action="/login" class="bg-white p-6 rounded shadow w-80">
    @csrf

    <h1 class="text-xl font-bold mb-4 text-center">התחברות</h1>

    <input name="email" type="email" placeholder="אימייל"
        class="w-full border p-2 mb-3 rounded" required>

    <input name="password" type="password" placeholder="סיסמה"
        class="w-full border p-2 mb-4 rounded" required>

    <button class="w-full bg-blue-600 text-white py-2 rounded">
        התחבר
    </button>

    @error('email')
        <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
    @enderror
</form>

</body>
</html>
