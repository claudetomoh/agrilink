<?php
require_once dirname(__DIR__, 3) . '/app/config/config.php';
$pageTitle = 'Reset Password';
include __DIR__ . '/../partials/head.php';
?>

<div class="auth-wrap">
  <div class="auth-hero">
    <img src="https://images.unsplash.com/photo-1665904285523-47c0a6fdfc0e?auto=format&fit=crop&w=1200&q=80" alt="Chilli vendor at a Ghanaian market" class="auth-hero-img">
    <div class="auth-hero-content">
      <div class="hero-tag" style="color:rgba(255,255,255,.9)">
        <span class="material-symbols-outlined" style="font-size:.9rem">verified_user</span>
        One-time secure reset
      </div>
      <h1>Create a new password</h1>
      <p>Choose a new password for <?= htmlspecialchars($email) ?>. This reset link expires after one hour.</p>
    </div>
  </div>

  <div class="auth-panel">
    <div class="auth-logo">
      <span class="material-symbols-outlined" style="color:var(--primary)">agriculture</span>
      AgriLink
    </div>

    <h2 class="auth-title">Set a new password</h2>
    <p class="auth-subtitle">Use at least 8 characters.</p>

    <?php include __DIR__ . '/../partials/alerts.php'; ?>

    <form method="POST" action="<?= APP_URL ?>/reset-password" novalidate>
      <input type="hidden" name="_token" value="<?= htmlspecialchars(Session::csrfToken()) ?>">
      <input type="hidden" name="action" value="reset_password">
      <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

      <div class="form-group">
        <label class="form-label" for="password">New Password</label>
        <div style="position:relative">
          <input type="password" id="password" name="password" class="form-input"
                 placeholder="Min. 8 characters" required autocomplete="new-password"
                 style="padding-right:3rem">
          <button type="button" onclick="togglePassword('password', this)"
                  style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--on-surface-variant);display:flex;align-items:center">
            <span class="material-symbols-outlined" style="font-size:1.125rem">visibility</span>
          </button>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="password_confirm">Confirm Password</label>
        <div style="position:relative">
          <input type="password" id="password_confirm" name="password_confirm" class="form-input"
                 placeholder="Repeat your new password" required autocomplete="new-password"
                 style="padding-right:3rem">
          <button type="button" onclick="togglePassword('password_confirm', this)"
                  style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--on-surface-variant);display:flex;align-items:center">
            <span class="material-symbols-outlined" style="font-size:1.125rem">visibility</span>
          </button>
        </div>
      </div>

      <button type="submit" class="btn btn-primary btn-full btn-lg">
        Reset Password
        <span class="material-symbols-outlined" style="font-size:1rem">lock</span>
      </button>
    </form>

    <div class="auth-footer">
      Need a fresh link?
      <a href="<?= APP_URL ?>/forgot-password" style="font-weight:700;color:var(--primary)">Request another</a>
    </div>
  </div>
</div>

<script>
function togglePassword(id, btn) {
  const inp = document.getElementById(id);
  const icon = btn.querySelector('.material-symbols-outlined');
  if (inp.type === 'password') {
    inp.type = 'text';
    icon.textContent = 'visibility_off';
  } else {
    inp.type = 'password';
    icon.textContent = 'visibility';
  }
}
</script>

<?php include __DIR__ . '/../partials/foot.php'; ?>