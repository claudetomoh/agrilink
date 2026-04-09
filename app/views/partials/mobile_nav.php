<?php
$role        = Session::userRole();
$currentPage = $currentPage ?? '';
$mobileLinks = [];

if ($role === 'farmer') {
    $mobileLinks = [
        ['href'=>APP_URL.'/farmer/dashboard','icon'=>'dashboard','label'=>'Home','key'=>'dashboard'],
        ['href'=>APP_URL.'/farmer/produce',  'icon'=>'inventory_2','label'=>'Listings','key'=>'produce'],
        ['href'=>APP_URL.'/farmer/orders',   'icon'=>'receipt_long','label'=>'Orders','key'=>'orders'],
        ['href'=>APP_URL.'/farmer/deliveries','icon'=>'local_shipping','label'=>'Deliver','key'=>'deliveries'],
        ['href'=>APP_URL.'/account/profile', 'icon'=>'person','label'=>'Profile','key'=>'profile'],
    ];
} elseif ($role === 'buyer') {
    $mobileLinks = [
        ['href'=>APP_URL.'/buyer/marketplace','icon'=>'storefront','label'=>'Market','key'=>'marketplace'],
        ['href'=>APP_URL.'/buyer/orders',     'icon'=>'receipt_long','label'=>'Orders','key'=>'orders'],
        ['href'=>APP_URL.'/buyer/track',      'icon'=>'local_shipping','label'=>'Track','key'=>'track'],
        ['href'=>APP_URL.'/account/profile',  'icon'=>'person','label'=>'Profile','key'=>'profile'],
    ];
} elseif ($role === 'transport') {
    $mobileLinks = [
        ['href'=>APP_URL.'/transport/dashboard',  'icon'=>'dashboard','label'=>'Home','key'=>'dashboard'],
        ['href'=>APP_URL.'/transport/jobs',       'icon'=>'work_outline','label'=>'Jobs','key'=>'jobs'],
        ['href'=>APP_URL.'/transport/deliveries', 'icon'=>'local_shipping','label'=>'Deliver','key'=>'deliveries'],
        ['href'=>APP_URL.'/account/profile',      'icon'=>'person','label'=>'Profile','key'=>'profile'],
    ];
} elseif ($role === 'admin') {
    $mobileLinks = [
        ['href'=>APP_URL.'/admin/dashboard', 'icon'=>'dashboard', 'label'=>'Home','key'=>'dashboard'],
        ['href'=>APP_URL.'/admin/users',     'icon'=>'group',     'label'=>'Users','key'=>'users'],
        ['href'=>APP_URL.'/admin/orders',    'icon'=>'receipt_long','label'=>'Orders','key'=>'orders'],
        ['href'=>APP_URL.'/admin/analytics', 'icon'=>'analytics', 'label'=>'Stats','key'=>'analytics'],
    ];
}
?>
<nav class="mobile-nav" role="navigation" aria-label="Bottom navigation">
  <div class="mobile-nav-inner">
    <?php foreach ($mobileLinks as $link): ?>
      <a href="<?= htmlspecialchars($link['href']) ?>"
         class="mobile-nav-item <?= $currentPage === $link['key'] ? 'active' : '' ?>">
        <span class="material-symbols-outlined mn-icon"><?= htmlspecialchars($link['icon']) ?></span>
        <?= htmlspecialchars($link['label']) ?>
      </a>
    <?php endforeach; ?>
  </div>
</nav>
