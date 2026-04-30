<?php
require_once dirname(__DIR__, 3) . '/app/config/config.php';
$pageTitle = 'Create Account';
$old = $old ?? [];
include __DIR__ . '/../partials/head.php';
?>

<div class="auth-wrap">
  <!-- Hero Side -->
  <div class="auth-hero">
    <img src="https://images.unsplash.com/photo-1625191824758-e00b2c51f388?auto=format&fit=crop&w=1200&q=80" alt="Fruit baskets at Kumasi market, Ghana" class="auth-hero-img">
    <div class="auth-hero-content">
      <div class="hero-tag" style="color:rgba(255,255,255,.9)">
        <span class="material-symbols-outlined" style="font-size:.9rem">groups</span>
        Join the Community
      </div>
      <h1>Start Selling or Buying Produce Today</h1>
      <p>Create your free account and be part of Ghana's fastest-growing agricultural network.</p>
    </div>
  </div>

  <!-- Form Side -->
  <div class="auth-panel" style="padding-top:2rem;padding-bottom:2rem">
    <div class="auth-logo">
      <span class="material-symbols-outlined" style="color:var(--primary)">agriculture</span>
      AgriLink
    </div>

    <h2 class="auth-title">Create your account</h2>
    <p class="auth-subtitle">Free to join. No hidden charges.</p>

    <?php include __DIR__ . '/../partials/alerts.php'; ?>

    <form method="POST" action="<?= APP_URL ?>/register" novalidate>
      <input type="hidden" name="_token" value="<?= htmlspecialchars(Session::csrfToken()) ?>">
      <input type="hidden" name="action" value="register">

      <!-- Role Selection -->
      <div class="form-group">
        <label class="form-label">I am a</label>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.5rem">
          <?php foreach(['farmer'=>['Farmer','grass'],'buyer'=>['Buyer','shopping_cart'],'transport'=>['Transporter','local_shipping']] as $val=>[$lbl,$icon]): ?>
          <label style="display:flex;flex-direction:column;align-items:center;gap:.35rem;padding:.75rem;border:2px solid var(--outline-variant);border-radius:var(--radius-lg);cursor:pointer;transition:all .15s;font-size:.8rem;font-weight:600"
                 id="role-card-<?=$val?>"
                 onclick="selectRole('<?=$val?>')">
            <input type="radio" name="role" value="<?=$val?>" style="display:none"
                   <?= ($old['role']??'buyer')===$val?'checked':'' ?>>
            <span class="material-symbols-outlined" style="font-size:1.5rem;color:var(--primary)"><?=$icon?></span>
            <?=$lbl?>
          </label>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Name & Phone -->
      <div class="form-row">
        <div class="form-group" style="margin-bottom:0">
          <label class="form-label" for="name">Full Name</label>
          <input type="text" id="name" name="name" class="form-input" required
                 placeholder="e.g. Kofi Boateng"
                 value="<?= htmlspecialchars($old['name'] ?? '') ?>">
        </div>
        <div class="form-group" style="margin-bottom:0">
          <label class="form-label" for="phone">Phone Number</label>
          <input type="tel" id="phone" name="phone" class="form-input"
                 placeholder="e.g. 024 400 0000"
                 value="<?= htmlspecialchars($old['phone'] ?? '') ?>">
        </div>
      </div>

      <!-- Email -->
      <div class="form-group">
        <label class="form-label" for="reg_email">Email Address</label>
        <input type="email" id="reg_email" name="email" class="form-input" required
               placeholder="your@email.com"
               value="<?= htmlspecialchars($old['email'] ?? '') ?>">
      </div>

      <!-- Region & Town -->
      <div class="form-row">
        <div class="form-group" style="margin-bottom:0">
          <label class="form-label" for="region">Region</label>
          <select id="region" name="region" class="form-select">
            <option value="">Select Region</option>
            <?php foreach (GH_REGIONS as $r): ?>
              <option value="<?= htmlspecialchars($r) ?>" <?= ($old['region']??'')===$r?'selected':'' ?>><?= htmlspecialchars($r) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group" style="margin-bottom:0">
          <label class="form-label" for="town">Town / City</label>
          <input type="text" id="town" name="town" class="form-input"
                 placeholder="e.g. Kumasi"
                 value="<?= htmlspecialchars($old['town'] ?? '') ?>">
        </div>
      </div>

      <!-- Password -->
      <div class="form-row">
        <div class="form-group" style="margin-bottom:0">
          <label class="form-label" for="reg_password">Password</label>
          <input type="password" id="reg_password" name="password" class="form-input" required
                 placeholder="Min. 8 characters" autocomplete="new-password">
        </div>
        <div class="form-group" style="margin-bottom:0">
          <label class="form-label" for="password_confirm">Confirm Password</label>
          <input type="password" id="password_confirm" name="password_confirm" class="form-input" required
                 placeholder="Repeat password" autocomplete="new-password">
        </div>
      </div>

      <div style="margin:1.25rem 0;font-size:.8rem;color:var(--on-surface-variant)">
        By creating an account you agree to AgriLink's Terms of Service and Privacy Policy.
      </div>

      <button type="submit" class="btn btn-primary btn-full btn-lg">
        Create Account
        <span class="material-symbols-outlined" style="font-size:1rem">person_add</span>
      </button>
    </form>

    <div class="auth-footer">
      Already have an account?
      <a href="<?= APP_URL ?>/login" style="font-weight:700;color:var(--primary)">Sign in</a>
    </div>
  </div>
</div>

<script>
function selectRole(val) {
  document.querySelectorAll('[id^="role-card-"]').forEach(el => {
    el.style.borderColor = 'var(--outline-variant)';
    el.style.background  = '';
    el.querySelector('input').checked = false;
  });
  const card = document.getElementById('role-card-' + val);
  card.style.borderColor = 'var(--primary)';
  card.style.background  = 'var(--primary-container)';
  card.querySelector('input').checked = true;
}
// Init with default
selectRole('<?= htmlspecialchars($old['role'] ?? 'buyer') ?>');
</script>
<?php include __DIR__ . '/../partials/foot.php'; ?>
