<!DOCTYPE html>
<html lang="en">
<?php
$headExtra = '<style>body{font-family:\'Inter\',sans-serif;}h1,h2,h3{font-family:\'Manrope\',sans-serif;}</style>';
?>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Complete Your Profile – AgriLink</title>
<script src="https://cdn.tailwindcss.com?plugins=forms"></script>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
<script>
  tailwind.config = {
    theme: { extend: {
      colors: {
        primary: '#2c694e', 'primary-dim': '#1e5d43',
        'primary-container': '#b1f0ce', 'on-primary': '#e1ffec',
        'surface': '#f7f9ff', 'surface-container-low': '#eff4fc',
        'surface-container-lowest': '#ffffff',
        'surface-container': '#e7eff8', 'surface-container-high': '#dfe9f5',
        'on-surface': '#28343e', 'on-surface-variant': '#54606c',
        'outline': '#6f7c88', 'outline-variant': '#a6b3c1',
        'secondary-container': '#a1f4c8', 'on-secondary-container': '#005e3e',
        'error': '#9f403d', 'error-container': '#fe8983',
      },
      fontFamily: { headline: ['Manrope'], body: ['Inter'] }
    }}
  }
</script>
<style>
  .material-symbols-outlined { font-variation-settings: 'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24; }
  body { background: #f7f9ff; font-family: 'Inter', sans-serif; min-height: 100dvh; }
  h1,h2,h3 { font-family: 'Manrope', sans-serif; }
</style>
</head>
<body class="text-on-surface flex flex-col min-h-screen">

<!-- TopBar -->
<header class="bg-surface border-b border-outline-variant/15 sticky top-0 z-40">
  <div class="flex justify-between items-center px-6 py-4 max-w-5xl mx-auto">
    <div class="flex items-center gap-3">
      <span class="material-symbols-outlined text-primary">agriculture</span>
      <span class="font-headline font-bold text-lg text-[#1B4332]">AgriLink</span>
    </div>
    <a href="<?= APP_URL ?>/logout"
       class="text-outline hover:text-primary transition-colors flex items-center gap-1 text-sm font-medium">
      <span class="material-symbols-outlined text-[18px]">close</span>
      <span class="hidden sm:inline">Exit Setup</span>
    </a>
  </div>
</header>

<main class="flex-1 flex items-center justify-center p-4 md:p-8">
  <div class="w-full max-w-5xl grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">

    <!-- Left: Progress Tracker -->
    <div class="lg:col-span-5 flex flex-col justify-between py-6 space-y-10">
      <div class="space-y-4">
        <div class="inline-flex items-center px-3 py-1 bg-primary-container text-[#1B4332] rounded-full text-[11px] font-bold uppercase tracking-wider">
          Step 02 / 03
        </div>
        <h1 class="text-4xl md:text-5xl font-extrabold text-on-surface leading-tight tracking-tight">
          Cultivating your
          <span style="background-image:linear-gradient(to right,#2c694e,#1e5d43);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;color:#2c694e"> digital field.</span>
        </h1>
        <p class="text-outline text-base max-w-sm leading-relaxed">
          Setting up your AgriLink profile takes less than two minutes. We'll tailor your experience to your role and region.
        </p>
      </div>

      <!-- Progress Steps -->
      <div class="relative flex flex-col gap-6">

        <!-- Connector line -->
        <div class="absolute left-4 top-8 bottom-14 w-[2px] bg-outline-variant/30"></div>

        <!-- Step 1: Done -->
        <div class="flex items-center gap-4">
          <div class="z-10 w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white ring-4 ring-primary-container flex-shrink-0">
            <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 1;">check</span>
          </div>
          <div>
            <span class="text-sm font-semibold text-primary">Identity Verified</span>
            <p class="text-xs text-outline">Account credentials secured</p>
          </div>
        </div>

        <!-- Step 2: Current -->
        <div class="flex items-center gap-4">
          <div class="z-10 w-8 h-8 rounded-full bg-primary-container flex items-center justify-center text-primary ring-4 ring-surface flex-shrink-0">
            <span class="material-symbols-outlined text-[18px]">business_center</span>
          </div>
          <div>
            <span class="text-sm font-semibold text-on-surface">Profile Details</span>
            <p class="text-xs text-outline">Region, contact & business info</p>
          </div>
        </div>

        <!-- Step 3: Pending -->
        <div class="flex items-center gap-4 opacity-40">
          <div class="z-10 w-8 h-8 rounded-full bg-surface-container flex items-center justify-center text-outline flex-shrink-0">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
          </div>
          <div>
            <span class="text-sm font-medium">All Set!</span>
            <p class="text-xs">Start using your dashboard</p>
          </div>
        </div>
      </div>

      <!-- Trust note -->
      <div class="hidden lg:flex items-start gap-3 p-4 bg-surface-container-low rounded-xl border-l-4 border-primary">
        <span class="material-symbols-outlined text-primary mt-0.5" style="flex-shrink:0">verified_user</span>
        <p class="text-xs text-on-surface-variant font-medium">
          Your information is protected and only used to connect you with the right farmers, buyers, and logistics partners in Ghana.
        </p>
      </div>
    </div>

    <!-- Right: Form -->
    <div class="lg:col-span-7">
      <div class="bg-surface-container-lowest rounded-2xl shadow-[0px_20px_40px_rgba(40,52,62,0.06)] overflow-hidden">

        <!-- Hero image banner -->
        <div class="h-48 relative overflow-hidden">
          <img src="https://images.unsplash.com/photo-1594904351111-a072f80b1a71?auto=format&fit=crop&w=800&q=80"
               alt="Ghanaian farm at dawn"
               class="w-full h-full object-cover object-center">
          <div class="absolute inset-0 bg-gradient-to-r from-primary/85 to-primary-dim/60 flex items-center px-8">
            <div class="text-on-primary">
              <p class="text-[11px] font-bold uppercase tracking-widest opacity-70 mb-1">AgriLink Setup</p>
              <h2 class="text-2xl font-extrabold leading-tight">Complete Your Profile</h2>
              <p class="text-sm opacity-80 mt-1">As a <?= e(ucfirst($user['role'])) ?>, your location helps us match you faster.</p>
            </div>
          </div>
        </div>

        <div class="p-8 md:p-10 space-y-6">

          <?php if (Session::getFlash('error')): ?>
          <div class="bg-error-container text-[#752121] px-4 py-3 rounded-lg text-sm font-medium">
            <?= e(Session::getFlash('error')) ?>
          </div>
          <?php endif; ?>

          <form method="POST" action="<?= APP_URL ?>/auth/onboarding" class="space-y-5">
            <input type="hidden" name="action" value="onboarding">
            <input type="hidden" name="_token" value="<?= Session::csrfToken() ?>">

            <!-- Phone -->
            <div class="space-y-1.5">
              <label class="block text-[11px] font-bold uppercase tracking-wider text-outline">
                Phone Number
              </label>
              <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[20px]">phone</span>
                <input type="tel" name="phone" placeholder="e.g. 0244 123 456"
                       value="<?= e($user['phone'] ?? '') ?>"
                       class="w-full pl-10 pr-4 py-3 bg-surface-container border-0 border-b-2 border-outline/30 focus:border-primary focus:ring-0 transition-all text-on-surface text-sm">
              </div>
            </div>

            <!-- Region -->
            <div class="space-y-1.5">
              <label class="block text-[11px] font-bold uppercase tracking-wider text-outline">
                Ghana Region <span class="text-error">*</span>
              </label>
              <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[20px]">map</span>
                <select name="region" required
                        class="w-full pl-10 pr-4 py-3 bg-surface-container border-0 border-b-2 border-outline/30 focus:border-primary focus:ring-0 transition-all text-on-surface text-sm appearance-none">
                  <option value="">Select your region…</option>
                  <?php foreach ($regions as $r): ?>
                  <option value="<?= e($r) ?>" <?= ($user['region'] ?? '') === $r ? 'selected' : '' ?>><?= e($r) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <!-- Town/City -->
            <div class="space-y-1.5">
              <label class="block text-[11px] font-bold uppercase tracking-wider text-outline">
                Town / City
              </label>
              <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[20px]">location_on</span>
                <input type="text" name="town" placeholder="e.g. Kumasi, Techiman, Tamale"
                       value="<?= e($user['town'] ?? '') ?>"
                       class="w-full pl-10 pr-4 py-3 bg-surface-container border-0 border-b-2 border-outline/30 focus:border-primary focus:ring-0 transition-all text-on-surface text-sm">
              </div>
            </div>

            <?php if ($user['role'] === 'transport'): ?>
            <!-- Business Type — Transport only -->
            <div class="space-y-3 pt-2">
              <label class="block text-[11px] font-bold uppercase tracking-wider text-outline">Operations Type</label>
              <div class="grid grid-cols-2 gap-3">
                <?php $ops = ['Owner Operator','Fleet Manager','Agricultural Co-op','Industrial Logistics']; ?>
                <?php foreach ($ops as $op): ?>
                <label class="flex items-start gap-3 p-4 rounded-xl bg-surface-container cursor-pointer hover:bg-primary-container/30 transition-colors group">
                  <input type="radio" name="ops_type" value="<?= e($op) ?>"
                         class="mt-1 text-primary focus:ring-primary">
                  <span class="text-sm font-medium"><?= e($op) ?></span>
                </label>
                <?php endforeach; ?>
              </div>
            </div>
            <?php endif; ?>

            <!-- Submit -->
            <div class="pt-4 flex items-center justify-between">
              <a href="<?= APP_URL ?>/<?= $user['role'] ?>/dashboard"
                 class="text-primary font-semibold hover:underline text-sm px-2 py-2">
                Skip for later
              </a>
              <button type="submit"
                      class="px-10 py-3 rounded-lg font-bold hover:scale-[1.02] transition-transform active:scale-95 flex items-center gap-2 text-sm"
                      style="background:linear-gradient(135deg,#2c694e,#1e5d43);color:#ffffff;box-shadow:0 4px 15px rgba(44,105,78,0.3)">
                Complete Setup
                <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

  </div>
</main>

<footer class="bg-surface-container-low border-t border-outline-variant/10 mt-auto">
  <div class="flex flex-col sm:flex-row justify-between items-center px-6 py-6 max-w-5xl mx-auto gap-3">
    <span class="font-headline font-bold text-[#1B4332]">AgriLink</span>
    <div class="flex gap-6 text-sm text-outline">
      <a href="#" class="hover:text-primary underline transition-colors">Privacy Policy</a>
      <a href="#" class="hover:text-primary underline transition-colors">Terms of Service</a>
      <a href="#" class="hover:text-primary underline transition-colors">Support</a>
    </div>
    <p class="text-xs text-outline">© 2024 AgriLink Ghana</p>
  </div>
</footer>

</body>
</html>
