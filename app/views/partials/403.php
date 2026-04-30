<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 – Access Denied | AgriLink</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@600;800&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="min-h-screen bg-[#f4f9f6] flex items-center justify-center p-6">
    <div class="text-center max-w-md">
        <div class="w-20 h-20 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>
        </div>
        <p class="text-red-500 font-semibold text-sm uppercase tracking-widest mb-3">Access Denied</p>
        <h1 class="font-['Manrope'] font-extrabold text-4xl text-gray-900 mb-4">403</h1>
        <p class="text-gray-600 leading-relaxed mb-8">
            You don't have permission to access this page.<br>
            Please log in with a different account or go back.
        </p>
        <div class="flex gap-3 justify-center flex-wrap">
            <?php if (class_exists('Session') && Session::isLoggedIn()): ?>
            <a href="<?= APP_URL ?>/<?= htmlspecialchars(Session::userRole()) ?>/dashboard"
               class="px-5 py-2.5 rounded-xl bg-[#2c694e] text-white text-sm font-semibold hover:bg-[#1e5d43] transition-colors">
                Go to My Dashboard
            </a>
            <?php else: ?>
            <a href="<?= APP_URL ?>/login"
               class="px-5 py-2.5 rounded-xl bg-[#2c694e] text-white text-sm font-semibold hover:bg-[#1e5d43] transition-colors">
                Log In
            </a>
            <?php endif; ?>
            <button onclick="history.back()"
                    class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition-colors">
                Go Back
            </button>
        </div>
    </div>
</body>
</html>
