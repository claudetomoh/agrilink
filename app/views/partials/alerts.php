<?php
// Flash alert display partial
$error   = $error   ?? Session::getFlash('error');
$success = $success ?? Session::getFlash('success');
$info    = $info    ?? Session::getFlash('info');
?>
<?php if ($error): ?>
  <div class="alert alert-error" role="alert">
    <span class="material-symbols-outlined" style="font-size:1.1rem;flex-shrink:0">error</span>
    <span><?= htmlspecialchars($error) ?></span>
  </div>
<?php endif; ?>
<?php if ($success): ?>
  <div class="alert alert-success" role="alert">
    <span class="material-symbols-outlined" style="font-size:1.1rem;flex-shrink:0">check_circle</span>
    <span><?= htmlspecialchars($success) ?></span>
  </div>
<?php endif; ?>
<?php if ($info): ?>
  <div class="alert alert-info" role="alert">
    <span class="material-symbols-outlined" style="font-size:1.1rem;flex-shrink:0">info</span>
    <span><?= htmlspecialchars($info) ?></span>
  </div>
<?php endif; ?>
