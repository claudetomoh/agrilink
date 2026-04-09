<?php
// Load recent notifications & unread count directly in the partial
$_notifModel   = new NotificationModel();
$_userId       = Session::userId();
$unread        = $_userId ? $_notifModel->countUnread($_userId) : 0;
$_recentNotifs = $_userId ? $_notifModel->getForUser($_userId, 6) : [];
$role          = Session::userRole();
?>
<header class="topbar topbar-dark" style="position:sticky;top:0;z-index:50;padding:0 1.5rem;">
  <!-- Hamburger (mobile) -->
  <button class="btn btn-icon" id="menuToggle" onclick="toggleSidebar()"
          style="display:none;background:rgba(255,255,255,.08);color:#fff"
          aria-label="Toggle sidebar">
    <span class="material-symbols-outlined">menu</span>
  </button>

  <!-- Brand -->
  <a href="<?= APP_URL ?>" class="topbar-brand" style="color:#fff">
    <span class="material-symbols-outlined" style="color:#a7f3d0">agriculture</span>
    AgriLink
  </a>

  <!-- Right actions -->
  <div style="display:flex;align-items:center;gap:1rem">

    <!-- Notifications dropdown -->
    <div style="position:relative" id="notifWrap">
      <button onclick="toggleNotifMenu(event)"
              class="btn btn-icon"
              style="background:rgba(255,255,255,.08);color:#fff"
              aria-label="Notifications">
        <span class="material-symbols-outlined">notifications</span>
      </button>
      <?php if ($unread > 0): ?>
        <span style="position:absolute;top:2px;right:2px;min-width:17px;height:17px;
                     background:#ef4444;color:#fff;border-radius:999px;font-size:.58rem;
                     font-weight:800;display:flex;align-items:center;justify-content:center;
                     border:2px solid #1e5d43;pointer-events:none;padding:0 2px">
          <?= $unread > 9 ? '9+' : $unread ?>
        </span>
      <?php endif; ?>

      <!-- Dropdown panel -->
      <div id="notifMenu"
           style="display:none;position:absolute;right:0;top:calc(100%+.5rem);
                  width:330px;max-height:420px;overflow-y:auto;z-index:300;
                  background:#fff;border-radius:1rem;
                  box-shadow:0 12px 40px rgba(0,0,0,.18);
                  border:1px solid rgba(0,0,0,.07)">
        <div style="padding:.8rem 1rem .6rem;border-bottom:1px solid #eee;
                    display:flex;align-items:center;justify-content:space-between;
                    position:sticky;top:0;background:#fff;z-index:1">
          <span style="font-weight:800;font-size:.85rem;color:#2c694e">Notifications</span>
          <?php if ($unread > 0): ?>
          <button onclick="markAllRead()"
                  style="font-size:.72rem;font-weight:700;color:#2c694e;background:none;
                         border:none;cursor:pointer;padding:0;text-decoration:underline">
            Mark all read
          </button>
          <?php endif; ?>
        </div>

        <?php if (empty($_recentNotifs)): ?>
        <div style="padding:2rem;text-align:center;color:#6b7280">
          <span class="material-symbols-outlined" style="font-size:2.2rem;opacity:.35">notifications_none</span>
          <p style="margin:.5rem 0 0;font-size:.78rem">No notifications yet</p>
        </div>
        <?php else: ?>
          <?php
          $notifIcons = [
            'order_placed'     => ['receipt_long',    '#2c694e'],
            'order_status'     => ['package_2',       '#0284c7'],
            'delivery_update'  => ['local_shipping',  '#7c3aed'],
            'review_received'  => ['star',            '#d97706'],
            'low_stock'        => ['warning',         '#dc2626'],
            'account_verified' => ['verified',        '#0891b2'],
            'bid_received'     => ['gavel',           '#6d28d9'],
            'new_account'      => ['person_add',      '#059669'],
            'job_available'    => ['work_outline',    '#b45309'],
          ];
          foreach ($_recentNotifs as $n):
            [$nIcon, $nColor] = $notifIcons[$n['type']] ?? ['notifications', '#2c694e'];
          ?>
          <a href="<?= $n['link'] ? APP_URL . htmlspecialchars($n['link']) : '#' ?>"
             style="display:flex;align-items:flex-start;gap:.65rem;padding:.65rem .9rem;
                    border-bottom:1px solid #f3f4f6;text-decoration:none;color:inherit;
                    background:<?= $n['is_read'] ? 'transparent' : '#f0fdf4' ?>">
            <div style="flex-shrink:0;width:1.8rem;height:1.8rem;border-radius:50%;
                        background:<?= $n['is_read'] ? '#f3f4f6' : '#dcfce7' ?>;
                        display:flex;align-items:center;justify-content:center;margin-top:.1rem">
              <span class="material-symbols-outlined"
                    style="font-size:.85rem;color:<?= $nColor ?>">
                <?= $nIcon ?>
              </span>
            </div>
            <div style="flex:1;min-width:0">
              <p style="margin:0 0 .15rem;font-size:.78rem;
                        font-weight:<?= $n['is_read'] ? '500' : '700' ?>;color:#111827;
                        white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                <?= htmlspecialchars($n['title']) ?>
              </p>
              <p style="margin:0;font-size:.7rem;color:#6b7280;
                        display:-webkit-box;-webkit-line-clamp:2;
                        -webkit-box-orient:vertical;overflow:hidden;line-height:1.4">
                <?= htmlspecialchars($n['message']) ?>
              </p>
              <p style="margin:.25rem 0 0;font-size:.62rem;color:#9ca3af">
                <?= date('d M, g:ia', strtotime($n['created_at'])) ?>
              </p>
            </div>
            <?php if (!$n['is_read']): ?>
            <div style="width:7px;height:7px;border-radius:50%;background:#2c694e;
                        flex-shrink:0;margin-top:.35rem"></div>
            <?php endif; ?>
          </a>
          <?php endforeach; ?>
        <?php endif; ?>

        <a href="<?= APP_URL ?>/notifications"
           style="display:block;padding:.65rem;text-align:center;font-size:.78rem;font-weight:700;
                  color:#2c694e;text-decoration:none;background:#f9fafb;
                  border-top:1px solid #e5e7eb;border-radius:0 0 1rem 1rem">
          View all notifications<?php if ($unread > 0): ?>
          <span style="background:#2c694e;color:#fff;font-size:.62rem;
                       padding:.1rem .4rem;border-radius:999px;margin-left:.3rem">
            <?= $unread ?>
          </span><?php endif; ?>
        </a>
      </div>
    </div>

    <!-- User menu -->
    <div style="position:relative" id="userMenuWrap">
      <button onclick="toggleUserMenu()" class="btn btn-icon"
              style="background:rgba(255,255,255,.08);color:#fff;border-radius:50%;
                     width:2.25rem;height:2.25rem;font-weight:700;
                     font-family:var(--font-headline)">
        <?= strtoupper(substr(Session::userName(), 0, 1)) ?>
      </button>
      <div id="userMenu" class="card"
           style="display:none;position:absolute;right:0;top:calc(100%+.5rem);
                  min-width:190px;z-index:200;border-radius:.75rem;padding:.5rem 0;
                  box-shadow:0 8px 24px rgba(0,0,0,.15)">
        <div style="padding:.625rem 1rem;font-size:.8rem;color:var(--on-surface-variant)">
          <strong style="display:block;color:var(--on-surface)">
            <?= htmlspecialchars(Session::userName()) ?>
          </strong>
          <?= ucfirst(htmlspecialchars(Session::userRole() ?? '')) ?>
        </div>
        <div class="divider" style="margin:.35rem 0"></div>
        <a href="<?= APP_URL ?>/notifications"
           style="display:flex;align-items:center;gap:.5rem;padding:.5rem 1rem;font-size:.875rem;
                  color:var(--on-surface);text-decoration:none;transition:background .1s"
           onmouseover="this.style.background='var(--surface-container-low)'"
           onmouseout="this.style.background=''">
          <span class="material-symbols-outlined" style="font-size:1rem">notifications</span>
          Notifications
          <?php if ($unread > 0): ?>
          <span style="background:var(--primary);color:#fff;font-size:.6rem;
                       padding:.05rem .35rem;border-radius:999px;margin-left:auto">
            <?= $unread ?>
          </span>
          <?php endif; ?>
        </a>
        <a href="<?= APP_URL ?>/logout" onclick="return confirm('Log out?')"
           style="display:flex;align-items:center;gap:.5rem;padding:.5rem 1rem;font-size:.875rem;
                  color:#991b1b;text-decoration:none;transition:background .1s"
           onmouseover="this.style.background='#fef2f2'"
           onmouseout="this.style.background=''">
          <span class="material-symbols-outlined" style="font-size:1rem">logout</span> Logout
        </a>
      </div>
    </div>
  </div>
</header>

<style>
  @media(max-width:768px){ #menuToggle{display:flex!important} }
</style>
<script>
function toggleNotifMenu(e) {
  e && e.stopPropagation();
  var m = document.getElementById('notifMenu');
  var u = document.getElementById('userMenu');
  if (u) u.style.display = 'none';
  m.style.display = m.style.display === 'none' ? 'block' : 'none';
}
function toggleUserMenu() {
  var m = document.getElementById('userMenu');
  var n = document.getElementById('notifMenu');
  if (n) n.style.display = 'none';
  m.style.display = m.style.display === 'none' ? 'block' : 'none';
}
function markAllRead() {
  fetch('<?= APP_URL ?>/notifications', {
    method: 'POST',
    headers: { 'X-Requested-With': 'XMLHttpRequest',
               'Content-Type': 'application/x-www-form-urlencoded' },
    body: '_token=<?= Session::csrfToken() ?>&action=mark_all_read'
  }).then(function() {
    var badge = document.querySelector('#notifWrap > span');
    if (badge) badge.remove();
  });
}
document.addEventListener('click', function(e) {
  if (!document.getElementById('notifWrap')?.contains(e.target)) {
    var nm = document.getElementById('notifMenu');
    if (nm) nm.style.display = 'none';
  }
  if (!document.getElementById('userMenuWrap')?.contains(e.target)) {
    var um = document.getElementById('userMenu');
    if (um) um.style.display = 'none';
  }
});
</script>
