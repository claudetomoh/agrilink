<?php include BASE_PATH . '/app/views/partials/head.php'; ?>
<div class="app-layout">
<?php include BASE_PATH . '/app/views/partials/sidebar.php'; ?>
<div class="main-wrap">
<?php include BASE_PATH . '/app/views/partials/topbar.php'; ?>
<main class="main-content">
<?php include BASE_PATH . '/app/views/partials/alerts.php'; ?>

<div class="flex items-center justify-between mb-8">
  <div>
    <h1 class="text-3xl font-extrabold tracking-tight text-primary">Notifications</h1>
    <p class="text-on-surface-variant text-sm mt-1"><?= count($notifications) ?> notification<?= count($notifications) !== 1 ? 's' : '' ?></p>
  </div>
  <?php if (!empty($notifications)): ?>
  <form method="POST" action="<?= APP_URL ?>/notifications">
    <input type="hidden" name="_token" value="<?= e(Session::csrfToken()) ?>">
    <input type="hidden" name="action" value="mark_all_read">
    <button type="submit" class="btn btn-secondary flex items-center gap-2">
      <span class="material-symbols-outlined" style="font-size:1rem">done_all</span>Mark All Read
    </button>
  </form>
  <?php endif; ?>
</div>

<?php
$notifIcons = [
  'order_placed'     => ['receipt_long',    '#2c694e', '#f0fdf4'],
  'order_status'     => ['package_2',       '#0284c7', '#eff6ff'],
  'delivery_update'  => ['local_shipping',  '#7c3aed', '#f5f3ff'],
  'review_received'  => ['star',            '#d97706', '#fffbeb'],
  'low_stock'        => ['warning',         '#dc2626', '#fef2f2'],
  'account_verified' => ['verified',        '#0891b2', '#ecfeff'],
  'bid_received'     => ['gavel',           '#6d28d9', '#faf5ff'],
  'new_account'      => ['person_add',      '#059669', '#ecfdf5'],
  'job_available'    => ['work_outline',    '#b45309', '#fefce8'],
];

// Separate unread and read
$unreadNotifs = array_filter($notifications, fn($n) => !$n['is_read']);
$readNotifs   = array_filter($notifications, fn($n)  => $n['is_read']);
?>

<?php if (empty($notifications)): ?>
<div class="bg-surface-container-lowest rounded-[2rem] p-20 text-center border border-outline-variant/10">
  <span class="material-symbols-outlined" style="font-size:5rem;color:var(--outline)">notifications_none</span>
  <p class="mt-4 text-xl font-bold text-primary">You're all caught up!</p>
  <p class="text-on-surface-variant text-sm mt-1 max-w-xs mx-auto">No notifications yet. They'll appear here when you receive orders, updates, or alerts.</p>
</div>
<?php else: ?>

<?php if (!empty($unreadNotifs)): ?>
<div class="mb-8">
  <h2 class="text-sm font-bold uppercase tracking-widest text-on-surface-variant mb-4 flex items-center gap-2">
    <span class="w-2 h-2 bg-primary rounded-full inline-block"></span>
    Unread (<?= count($unreadNotifs) ?>)
  </h2>
  <div class="space-y-3">
    <?php foreach ($unreadNotifs as $n): ?>
    <?php
      [$nIcon, $nColor, $nBg] = $notifIcons[$n['type']] ?? ['notifications', '#2c694e', '#f0fdf4'];
    ?>
    <a href="<?= $n['link'] ? APP_URL . htmlspecialchars($n['link']) : '#' ?>"
       class="flex items-start gap-4 bg-surface-container-lowest rounded-[1.25rem] p-5
              border-l-4 border-primary shadow-sm hover:shadow-md transition-shadow
              block text-inherit no-underline"
       style="text-decoration:none;color:inherit">
      <div style="width:2.75rem;height:2.75rem;border-radius:50%;background:<?= $nBg ?>;
                  flex-shrink:0;display:flex;align-items:center;justify-content:center">
        <span class="material-symbols-outlined" style="color:<?= $nColor ?>;font-size:1.2rem"><?= $nIcon ?></span>
      </div>
      <div style="flex:1;min-width:0">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:.5rem">
          <h3 style="font-size:.9rem;font-weight:800;color:#111827;margin:0 0 .2rem">
            <?= htmlspecialchars($n['title']) ?>
          </h3>
          <span style="font-size:.68rem;color:#9ca3af;flex-shrink:0;margin-top:.1rem">
            <?= date('d M, g:ia', strtotime($n['created_at'])) ?>
          </span>
        </div>
        <p style="margin:0;font-size:.8rem;color:#6b7280;line-height:1.5">
          <?= htmlspecialchars($n['message']) ?>
        </p>
      </div>
      <div style="width:9px;height:9px;border-radius:50%;background:#2c694e;flex-shrink:0;margin-top:.4rem"></div>
    </a>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<?php if (!empty($readNotifs)): ?>
<div>
  <h2 class="text-sm font-bold uppercase tracking-widest text-on-surface-variant mb-4 flex items-center gap-2">
    <span class="w-2 h-2 bg-surface-container-high rounded-full inline-block"></span>
    Earlier (<?= count($readNotifs) ?>)
  </h2>
  <div class="space-y-3">
    <?php foreach ($readNotifs as $n): ?>
    <?php
      [$nIcon, $nColor, $nBg] = $notifIcons[$n['type']] ?? ['notifications', '#2c694e', '#f0fdf4'];
    ?>
    <a href="<?= $n['link'] ? APP_URL . htmlspecialchars($n['link']) : '#' ?>"
       class="flex items-start gap-4 bg-surface-container-lowest rounded-[1.25rem] p-5
              border border-outline-variant/10 shadow-sm hover:shadow-md transition-shadow
              block text-inherit no-underline opacity-80"
       style="text-decoration:none;color:inherit">
      <div style="width:2.75rem;height:2.75rem;border-radius:50%;background:#f3f4f6;
                  flex-shrink:0;display:flex;align-items:center;justify-content:center">
        <span class="material-symbols-outlined" style="color:#9ca3af;font-size:1.2rem"><?= $nIcon ?></span>
      </div>
      <div style="flex:1;min-width:0">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:.5rem">
          <h3 style="font-size:.9rem;font-weight:600;color:#374151;margin:0 0 .2rem">
            <?= htmlspecialchars($n['title']) ?>
          </h3>
          <span style="font-size:.68rem;color:#9ca3af;flex-shrink:0;margin-top:.1rem">
            <?= date('d M, g:ia', strtotime($n['created_at'])) ?>
          </span>
        </div>
        <p style="margin:0;font-size:.8rem;color:#9ca3af;line-height:1.5">
          <?= htmlspecialchars($n['message']) ?>
        </p>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<?php endif; ?>

</main>
</div>
</div>
<?php include BASE_PATH . '/app/views/partials/foot.php'; ?>
