<?php
// Sidebar nav helper — $navItems and $currentPage passed from view controller
$role        = Session::userRole();
$currentPage = $currentPage ?? '';
$unread      = $unreadCount ?? 0;

// Build nav items per role
$navGroups = [];

if ($role === 'farmer') {
    $navGroups = [
        '' => [
            ['label'=>'Dashboard',    'icon'=>'dashboard',        'href'=>APP_URL.'/farmer/dashboard',  'key'=>'dashboard'],
            ['label'=>'My Listings',  'icon'=>'inventory_2',      'href'=>APP_URL.'/farmer/listings',   'key'=>'listings'],
            ['label'=>'Add Produce',  'icon'=>'add_circle',       'href'=>APP_URL.'/farmer/listings/add', 'key'=>'listing_add'],
        ],
        'Sales' => [
            ['label'=>'Orders',       'icon'=>'receipt_long',     'href'=>APP_URL.'/farmer/orders',     'key'=>'orders'],
        ],
        'Insights' => [
            ['label'=>'Analytics',    'icon'=>'analytics',        'href'=>APP_URL.'/analytics',         'key'=>'analytics'],
            ['label'=>'Market Data',  'icon'=>'bar_chart',        'href'=>APP_URL.'/analytics/market',  'key'=>'market'],
        ],
        'Account' => [
            ['label'=>'Profile',      'icon'=>'manage_accounts',  'href'=>APP_URL.'/farmer/profile',    'key'=>'profile'],
            ['label'=>'Logout',       'icon'=>'logout',           'href'=>APP_URL.'/logout',            'key'=>'logout'],
        ],
    ];
} elseif ($role === 'buyer') {
    $navGroups = [
        '' => [
            ['label'=>'Marketplace',  'icon'=>'storefront',       'href'=>APP_URL.'/buyer/marketplace', 'key'=>'marketplace'],
            ['label'=>'Smart Matching','icon'=>'auto_awesome',    'href'=>APP_URL.'/buyer/matching',    'key'=>'matching'],
            ['label'=>'My Orders',    'icon'=>'receipt_long',     'href'=>APP_URL.'/buyer/orders',      'key'=>'orders'],
        ],
        'Insights' => [
            ['label'=>'Market Analytics','icon'=>'bar_chart',     'href'=>APP_URL.'/analytics/market',  'key'=>'market'],
        ],
        'Account' => [
            ['label'=>'Logout',       'icon'=>'logout',           'href'=>APP_URL.'/logout',            'key'=>'logout'],
        ],
    ];
} elseif ($role === 'transport') {
    $navGroups = [
        '' => [
            ['label'=>'Dashboard',    'icon'=>'dashboard',        'href'=>APP_URL.'/transport/dashboard',  'key'=>'dashboard'],
            ['label'=>'Available Jobs','icon'=>'work_outline',    'href'=>APP_URL.'/transport/jobs',       'key'=>'jobs'],
        ],
        'Account' => [
            ['label'=>'Logout',       'icon'=>'logout',           'href'=>APP_URL.'/logout',               'key'=>'logout'],
        ],
    ];
} elseif ($role === 'admin') {
    $navGroups = [
        '' => [
            ['label'=>'Dashboard',    'icon'=>'dashboard',        'href'=>APP_URL.'/admin/dashboard',   'key'=>'dashboard'],
            ['label'=>'Users',        'icon'=>'group',            'href'=>APP_URL.'/admin/users',       'key'=>'users'],
        ],
        'Operations' => [
            ['label'=>'Orders',       'icon'=>'receipt_long',     'href'=>APP_URL.'/admin/orders',      'key'=>'orders'],
            ['label'=>'Deliveries',   'icon'=>'local_shipping',   'href'=>APP_URL.'/admin/deliveries',  'key'=>'deliveries'],
        ],
        'Insights' => [
            ['label'=>'Analytics',    'icon'=>'analytics',        'href'=>APP_URL.'/analytics',         'key'=>'analytics'],
            ['label'=>'Market Data',  'icon'=>'bar_chart',        'href'=>APP_URL.'/analytics/market',  'key'=>'market'],
        ],
        'Account' => [
            ['label'=>'Logout',       'icon'=>'logout',           'href'=>APP_URL.'/logout',            'key'=>'logout'],
        ],
    ];
}
?>
<!-- Overlay for mobile -->
<div class="overlay-sidebar" id="sidebarOverlay" onclick="closeSidebar()"></div>

<aside class="sidebar" id="sidebar">
  <div class="sidebar-header">
    <a href="<?= APP_URL ?>" class="sidebar-brand">
      <span class="material-symbols-outlined" style="font-size:1.375rem">agriculture</span>
      AgriLink
    </a>
  </div>

  <nav class="sidebar-nav" role="navigation" aria-label="Sidebar">
    <?php foreach ($navGroups as $section => $items): ?>
      <?php if ($section): ?><div class="sidebar-section"><?= htmlspecialchars($section) ?></div><?php endif; ?>
      <?php foreach ($items as $item): ?>
        <a href="<?= htmlspecialchars($item['href']) ?>"
           class="<?= $currentPage === $item['key'] ? 'active' : '' ?>"
           <?= $item['key']==='logout' ? 'onclick="return confirm(\'Log out of AgriLink?\')"' : '' ?>>
          <span class="material-symbols-outlined nav-icon"><?= htmlspecialchars($item['icon']) ?></span>
          <?= htmlspecialchars($item['label']) ?>
          <?php if ($item['key']==='orders' && ($pending ?? 0) > 0): ?>
            <span style="margin-left:auto;background:#dc2626;color:#fff;border-radius:100px;font-size:.65rem;font-weight:700;padding:0 .45rem;min-width:1.25rem;text-align:center">
              <?= (int)$pending ?>
            </span>
          <?php endif; ?>
        </a>
      <?php endforeach; ?>
    <?php endforeach; ?>
  </nav>

  <div class="sidebar-footer">
    <div style="display:flex;align-items:center;gap:.75rem;">
      <div class="avatar" style="width:2.25rem;height:2.25rem;background:var(--primary-container);color:var(--on-primary-container);font-size:.9rem">
        <?= strtoupper(substr(Session::userName(), 0, 1)) ?>
      </div>
      <div style="min-width:0">
        <div style="font-size:.825rem;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:#fff">
          <?= htmlspecialchars(Session::userName()) ?>
        </div>
        <div style="font-size:.7rem;color:rgba(255,255,255,.45);text-transform:capitalize">
          <?= htmlspecialchars(Session::userRole()) ?>
        </div>
      </div>
    </div>
  </div>
</aside>
