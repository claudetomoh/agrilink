<?php include BASE_PATH . '/app/views/partials/head.php'; ?>
<div class="app-layout">
<?php include BASE_PATH . '/app/views/partials/sidebar.php'; ?>
<div class="main-wrap">
<?php include BASE_PATH . '/app/views/partials/topbar.php'; ?>
<main class="main-content">
<?php include BASE_PATH . '/app/views/partials/alerts.php'; ?>

<div class="mb-10">
  <h1 class="text-4xl font-extrabold tracking-tight text-primary">Admin Dashboard</h1>
  <p class="text-on-surface-variant mt-1">System overview — AgriLink Ghana</p>
</div>

<!-- KPIs -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-5 mb-10">
  <?php
  $kpis = [
    ['Total Users',       $stats['total_users'],      'people',         'bg-primary text-white'],
    ['Total Orders',      $stats['total_orders'],      'receipt_long',   'bg-secondary-container text-on-secondary-container'],
    ['Active Deliveries', $stats['active_deliveries'], 'local_shipping', 'bg-tertiary-container text-on-tertiary-container'],
    ['Total Revenue',     '₵'.number_format($stats['total_revenue'],0), 'payments', 'bg-surface-container-low text-primary'],
  ];
  foreach ($kpis as [$label,$val,$icon,$cls]):
  ?>
  <div class="<?= $cls ?> p-6 rounded-[1.5rem] shadow-sm">
    <div class="flex items-center justify-between mb-3">
      <span class="text-xs font-bold uppercase tracking-wider opacity-70"><?= $label ?></span>
      <span class="material-symbols-outlined opacity-60"><?= $icon ?></span>
    </div>
    <p class="text-3xl font-extrabold"><?= $val ?></p>
  </div>
  <?php endforeach; ?>
</div>

<!-- User Breakdown -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-10">
  <?php
  $roles = [
    ['Farmers',      $stats['farmers'],      'agriculture', 'Registered producers'],
    ['Buyers',       $stats['buyers'],        'shopping_bag','Active buyers'],
    ['Transporters', $stats['transporters'],  'local_shipping','Delivery partners'],
  ];
  foreach ($roles as [$label,$count,$icon,$sub]):
  ?>
  <div class="bg-surface-container-lowest rounded-[1.5rem] p-6 border border-outline-variant/10 shadow-sm flex items-center gap-5">
    <div class="w-14 h-14 rounded-2xl bg-primary-container/20 flex items-center justify-center flex-shrink-0">
      <span class="material-symbols-outlined text-primary" style="font-size:1.6rem"><?= $icon ?></span>
    </div>
    <div>
      <p class="text-3xl font-extrabold text-primary"><?= $count ?></p>
      <p class="font-bold text-on-surface"><?= $label ?></p>
      <p class="text-xs text-on-surface-variant"><?= $sub ?></p>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Ghana Market Spotlight -->
<div class="mb-10">
  <div class="flex items-center justify-between mb-5">
    <h3 class="text-xl font-bold text-primary">Market Spotlight — Key Commodities</h3>
    <span class="text-xs text-on-surface-variant font-medium">Ghana's top traded produce</span>
  </div>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
    <?php
    $spotlight = [
      ['Tomatoes',   'tomato',  'Brong-Ahafo',  'High demand, harvest season', '₵28–₵45/bag'],
      ['Cocoa Pods', 'cocoa',   'Ashanti',       'Grade A export ready',         '₵320–₵410/bag'],
      ['White Yam',  'yam',     'Brong-Ahafo',  'Surplus supply, stable price', '₵55–₵75/bag'],
    ];
    foreach ($spotlight as [$pname, $pcat, $region, $note, $price]):
    ?>
    <div class="relative rounded-[1.75rem] overflow-hidden shadow-sm group cursor-default" style="height:200px">
      <img src="<?= Helpers::produceImage($pname, $pcat, 600) ?>" alt="<?= $pname ?>"
           class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
      <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/20 to-transparent"></div>
      <div class="absolute bottom-0 left-0 right-0 p-5 text-white">
        <p class="font-extrabold text-base leading-tight"><?= $pname ?></p>
        <p class="text-xs opacity-70 mt-0.5"><?= $region ?> &bull; <?= $note ?></p>
        <p class="text-sm font-bold mt-2 text-emerald-300"><?= $price ?></p>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
  <!-- Recent Orders -->
  <div class="bg-surface-container-lowest rounded-[2rem] overflow-hidden border border-outline-variant/10 shadow-sm">
    <div class="flex items-center justify-between px-6 py-5 border-b border-outline-variant/10">
      <h3 class="font-bold text-primary">Recent Orders</h3>
      <a href="<?= APP_URL ?>/admin/orders" class="text-xs font-bold text-primary hover:underline">View All</a>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-left">
        <thead>
          <tr class="bg-surface-container-low/40">
            <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Order</th>
            <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Produce</th>
            <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Total</th>
            <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-surface-container">
          <?php foreach ($recentOrders as $o): ?>
          <tr class="hover:bg-surface-container-low/20">
            <td class="px-5 py-4 font-mono text-xs">#<?= str_pad($o['id'],4,'0',STR_PAD_LEFT) ?></td>
            <td class="px-5 py-4 text-sm font-semibold"><?= e($o['produce_name'] ?? '—') ?></td>
            <td class="px-5 py-4 font-bold text-primary text-sm">₵<?= number_format($o['total_price'],0) ?></td>
            <td class="px-5 py-4">
              <?php $cls=['pending'=>'badge-warning','confirmed'=>'badge-info','in_transit'=>'badge-warning','delivered'=>'badge-success','cancelled'=>'badge-error'][$o['status']]??'badge-info'; ?>
              <span class="badge <?= $cls ?> text-xs"><?= ucfirst(str_replace('_',' ',$o['status'])) ?></span>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Recent Users -->
  <div class="bg-surface-container-lowest rounded-[2rem] overflow-hidden border border-outline-variant/10 shadow-sm">
    <div class="flex items-center justify-between px-6 py-5 border-b border-outline-variant/10">
      <h3 class="font-bold text-primary">New Members</h3>
      <a href="<?= APP_URL ?>/admin/users" class="text-xs font-bold text-primary hover:underline">View All</a>
    </div>
    <div class="divide-y divide-surface-container">
      <?php foreach ($recentUsers as $u): ?>
      <div class="flex items-center gap-4 px-6 py-4">
        <div class="w-9 h-9 rounded-full bg-primary flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
          <?= strtoupper(substr($u['name'],0,1)) ?>
        </div>
        <div class="flex-1 min-w-0">
          <p class="font-bold text-sm truncate"><?= e($u['name']) ?></p>
          <p class="text-xs text-on-surface-variant truncate"><?= e($u['email']) ?></p>
        </div>
        <span class="badge badge-info text-xs capitalize"><?= e($u['role']) ?></span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

</main>
</div>
</div>
<?php include BASE_PATH . '/app/views/partials/foot.php'; ?>
