<?php
require_once dirname(__DIR__, 3) . '/app/config/config.php';
$pageTitle = 'Forgot Password';
$old = $old ?? [];
include __DIR__ . '/../partials/head.php';
?>

<div class="auth-wrap">
  <div class="auth-hero">
    <img src="https://images.unsplash.com/photo-1625191824758-e00b2c51f388?auto=format&fit=crop&w=1200&q=80" alt="Fruit baskets at Kumasi market, Ghana" class="auth-hero-img">
    <div class="auth-hero-content">
      <div class="hero-tag" style="color:rgba(255,255,255,.9)">
        <span class="material-symbols-outlined" style="font-size:.9rem">lock_reset</span>
        Secure account recovery
      </div>
      <h1>Reset your password</h1>
      <p>Enter your AgriLink email address and we will email you a secure password reset link.</p>
    </div>
  </div>

  <div class="auth-panel">
    <div class="auth-logo">
      <span class="material-symbols-outlined" style="color:var(--primary)">agriculture</span>
      AgriLink
    </div>

    <h2 class="auth-title">Forgot password?</h2>
    <p class="auth-subtitle">Use your account email to request a secure reset link.</p>

    <?php include __DIR__ . '/../partials/alerts.php'; ?>

    <form method="POST" action="<?= APP_URL ?>/forgot-password" novalidate>
      <input type="hidden" name="_token" value="<?= htmlspecialchars(Session::csrfToken()) ?>">
      <input type="hidden" name="action" value="forgot_password">

      <div class="form-group">
        <label class="form-label" for="email">Email Address</label>
        <input type="email" id="email" name="email" class="form-input"
               placeholder="e.g. kofi@agrilink.gh" required autocomplete="email"
               value="<?= htmlspecialchars($old['email'] ?? '') ?>">
      </div>

      <button type="submit" class="btn btn-primary btn-full btn-lg">
        Generate Reset Link
        <span class="material-symbols-outlined" style="font-size:1rem">arrow_forward</span>
      </button>
    </form>

    <?php if (!empty($resetLink)): ?>
      <div style="margin-top:1.25rem;padding:1rem;border:1px solid var(--outline-variant);background:var(--surface-container-low);border-radius:var(--radius-lg)">
        <div style="font-weight:700;margin-bottom:.4rem;color:var(--on-surface)">One-time reset link</div>
        <p style="font-size:.85rem;color:var(--on-surface-variant);margin-bottom:.75rem">This build does not send email yet. Use this generated link before it expires in 60 minutes.</p>
        <a href="<?= htmlspecialchars($resetLink) ?>" style="display:block;word-break:break-all;color:var(--primary);font-weight:600"><?= htmlspecialchars($resetLink) ?></a>
      </div>
    <?php endif; ?>

    <div class="auth-footer">
      Remembered your password?
      <a href="<?= APP_URL ?>/login" style="font-weight:700;color:var(--primary)">Sign in</a>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../partials/foot.php'; ?>