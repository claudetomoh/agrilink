<?php include BASE_PATH . '/app/views/partials/head.php'; ?>
<div class="app-layout">
<?php include BASE_PATH . '/app/views/partials/sidebar.php'; ?>
<div class="main-wrap">
<?php include BASE_PATH . '/app/views/partials/topbar.php'; ?>
<main class="main-content">
<?php include BASE_PATH . '/app/views/partials/alerts.php'; ?>

<div class="mb-8">
  <h1 class="text-3xl font-extrabold tracking-tight text-primary">My Profile</h1>
  <p class="text-on-surface-variant text-sm mt-1">Manage your account information</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
  <!-- Profile Card -->
  <div class="lg:col-span-1">
    <div class="bg-surface-container-lowest rounded-[2rem] p-8 border border-outline-variant/10 shadow-sm text-center">
      <div class="w-24 h-24 rounded-full bg-primary-container flex items-center justify-center mx-auto mb-4">
        <span class="text-4xl font-extrabold text-white"><?= strtoupper(substr($user['name'], 0, 1)) ?></span>
      </div>
      <h2 class="text-xl font-extrabold text-primary"><?= e($user['name']) ?></h2>
      <p class="text-on-surface-variant text-sm mt-1"><?= ucfirst($user['role']) ?></p>
      <div class="mt-4 flex flex-col gap-2 text-sm text-on-surface-variant">
        <div class="flex items-center justify-center gap-2">
          <span class="material-symbols-outlined" style="font-size:1rem">location_on</span>
          <?= e($user['region'] ?? 'Ghana') ?>
        </div>
        <div class="flex items-center justify-center gap-2">
          <span class="material-symbols-outlined" style="font-size:1rem">calendar_today</span>
          Joined <?= date('M Y', strtotime($user['created_at'])) ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Form -->
  <div class="lg:col-span-2">
    <div class="bg-surface-container-lowest rounded-[2rem] p-8 border border-outline-variant/10 shadow-sm">
      <h3 class="text-lg font-bold text-primary mb-6">Account Information</h3>
      <form method="POST" action="<?= APP_URL ?>/farmer/profile" novalidate>
        <input type="hidden" name="_token" value="<?= e(Session::csrfToken()) ?>">
        <input type="hidden" name="action" value="update_profile">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <div class="form-group md:col-span-2">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-input" value="<?= e($user['name']) ?>" required>
          </div>
          <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-input" value="<?= e($user['email']) ?>" required>
          </div>
          <div class="form-group">
            <label class="form-label">Phone</label>
            <input type="tel" name="phone" class="form-input" value="<?= e($user['phone'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label class="form-label">Region</label>
            <select name="region" class="form-select">
              <?php foreach (GH_REGIONS as $r): ?>
              <option value="<?= e($r) ?>" <?= ($user['region']??'')===$r?'selected':'' ?>><?= e($r) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Town</label>
            <input type="text" name="town" class="form-input" value="<?= e($user['town'] ?? '') ?>">
          </div>
          <div class="form-group md:col-span-2">
            <label class="form-label">New Password <span class="text-on-surface-variant text-xs">(leave blank to keep current)</span></label>
            <input type="password" name="password" class="form-input" placeholder="New password" autocomplete="new-password">
          </div>
        </div>
        <div class="mt-6">
          <button type="submit" class="btn btn-primary flex items-center gap-2">
            <span class="material-symbols-outlined" style="font-size:1rem">save</span>Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

</main>
</div>
</div>
<?php include BASE_PATH . '/app/views/partials/foot.php'; ?>
