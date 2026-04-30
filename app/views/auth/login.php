<?php
require_once dirname(__DIR__, 3) . '/app/config/config.php';
$pageTitle = 'Login';
include __DIR__ . '/../partials/head.php';
?>

<div class="auth-wrap">
  <!-- Hero Side -->
  <div class="auth-hero">
    <img src="https://images.pexels.com/photos/2252584/pexels-photo-2252584.jpeg?auto=format&fit=crop&w=1200&q=80" alt="Fresh produce at a Ghanaian market" class="auth-hero-img">
    <div class="auth-hero-content">
      <div class="hero-tag" style="color:rgba(255,255,255,.9)">
        <span class="material-symbols-outlined" style="font-size:.9rem">verified</span>
        Trusted by Ghanaian Farmers
      </div>
      <h1>Ghana's Agricultural Marketplace</h1>
      <p>Connect with farmers, buyers, and transporters across all 16 regions of Ghana. Trade smarter with AgriLink.</p>
      <div style="display:flex;gap:2rem;margin-top:2rem;flex-wrap:wrap">
        <div>
          <div style="font-size:1.75rem;font-weight:800">2,400+</div>
          <div style="font-size:.8rem;opacity:.65">Active Farmers</div>
        </div>
        <div>
          <div style="font-size:1.75rem;font-weight:800">₵1.2M+</div>
          <div style="font-size:.8rem;opacity:.65">Monthly Trade Volume</div>
        </div>
        <div>
          <div style="font-size:1.75rem;font-weight:800">16</div>
          <div style="font-size:.8rem;opacity:.65">Regions Covered</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Form Side -->
  <div class="auth-panel">
    <div class="auth-logo">
      <span class="material-symbols-outlined" style="color:var(--primary)">agriculture</span>
      AgriLink
    </div>

    <h2 class="auth-title">Welcome back</h2>
    <p class="auth-subtitle">Sign in to your AgriLink account</p>

    <?php include __DIR__ . '/../partials/alerts.php'; ?>

    <form method="POST" action="<?= APP_URL ?>/login" novalidate>
      <input type="hidden" name="_token" value="<?= htmlspecialchars(Session::csrfToken()) ?>">
      <input type="hidden" name="action" value="login">

      <div class="form-group">
        <label class="form-label" for="email">Email Address</label>
        <input type="email" id="email" name="email" class="form-input" 
               placeholder="e.g. kofi@agrilink.gh" required autocomplete="email"
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <div style="position:relative">
          <input type="password" id="password" name="password" class="form-input"
                 placeholder="Enter your password" required autocomplete="current-password"
                 style="padding-right:3rem">
          <button type="button" onclick="togglePassword('password', this)"
                  style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--on-surface-variant);display:flex;align-items:center">
            <span class="material-symbols-outlined" style="font-size:1.125rem">visibility</span>
          </button>
        </div>
      </div>

      <div style="display:flex;justify-content:flex-end;margin-bottom:1.25rem">
        <a href="<?= APP_URL ?>/forgot-password" style="font-size:.8rem;color:var(--primary)">Forgot password?</a>
      </div>

      <button type="submit" class="btn btn-primary btn-full btn-lg">
        Sign In
        <span class="material-symbols-outlined" style="font-size:1rem">arrow_forward</span>
      </button>
    </form>

    <div class="auth-divider">or</div>

    <div class="auth-footer">
      Don't have an account?
      <a href="<?= APP_URL ?>/register" style="font-weight:700;color:var(--primary)">Create one free</a>
    </div>

    <!-- Quick login helper for demo -->
    <div style="margin-top:2rem;padding:1rem;background:var(--surface-container-low);border-radius:var(--radius);font-size:.8rem">
      <div style="font-weight:700;margin-bottom:.5rem;color:var(--on-surface-variant)">Demo Accounts</div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:.4rem">
        <button onclick="fillLogin('admin@agrilink.gh','Admin@1234')" class="btn btn-secondary btn-sm">Admin</button>
        <button onclick="fillLogin('kofi.boateng@agrilink.gh','Pass@1234')" class="btn btn-secondary btn-sm">Farmer</button>
        <button onclick="fillLogin('kwame.mensah@agrilink.gh','Pass@1234')" class="btn btn-secondary btn-sm">Buyer</button>
        <button onclick="fillLogin('kojo.logistics@agrilink.gh','Pass@1234')" class="btn btn-secondary btn-sm">Transport</button>
      </div>
    </div>
  </div>
</div>

<script>
function fillLogin(email, pass) {
  document.getElementById('email').value    = email;
  document.getElementById('password').value = pass;
}
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
