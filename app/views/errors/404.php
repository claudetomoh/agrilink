<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>404 – Page Not Found | AgriLink</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@600;800&family=Inter:wght@400;500&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<style>
  body { font-family: 'Inter', sans-serif; }
  h1,h2 { font-family: 'Manrope', sans-serif; }
  .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
</style>
</head>
<body class="bg-[#f7f9ff] text-[#28343e] min-h-screen flex flex-col items-center justify-center p-6">

  <div class="text-center max-w-md">
    <!-- Big icon -->
    <div class="mx-auto w-24 h-24 bg-[#b1f0ce] rounded-full flex items-center justify-center mb-6">
      <span class="material-symbols-outlined text-[#2c694e]" style="font-size:48px;font-variation-settings:'FILL' 1">search_off</span>
    </div>

    <p class="text-[#2c694e] font-bold text-sm uppercase tracking-widest mb-2">404 Error</p>
    <h1 class="text-4xl font-extrabold mb-4">Page Not Found</h1>
    <p class="text-[#54606c] mb-8 leading-relaxed">
      This crop didn't make it to market. The page you're looking for doesn't exist or has been moved.
    </p>

    <div class="flex flex-col sm:flex-row gap-3 justify-center">
      <a href="<?= defined('APP_URL') ? APP_URL : '/' ?>" class="px-6 py-3 rounded-full bg-[#2c694e] text-white font-bold text-sm hover:opacity-90 transition-opacity">
        Go Home
      </a>
      <a href="javascript:history.back()" class="px-6 py-3 rounded-full border border-[#a6b3c1] text-[#28343e] font-bold text-sm hover:bg-[#eff4fc] transition-colors">
        Go Back
      </a>
    </div>
  </div>

</body>
</html>
