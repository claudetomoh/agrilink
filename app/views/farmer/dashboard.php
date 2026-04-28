<?php
/* Farmer Dashboard — matches farmer_dashboard/code.html design */
require_once BASE_PATH . '/app/core/Session.php';
$user = (new UserModel())->findById(Session::userId());
$pageTitle = 'Farmer Dashboard';
include BASE_PATH . '/app/views/partials/head.php';
?>
<div class="app-layout">
<?php include BASE_PATH . '/app/views/partials/sidebar.php'; ?>
<div class="main-wrap">
<?php include BASE_PATH . '/app/views/partials/topbar.php'; ?>
<main class="main-content">

<?php include BASE_PATH . '/app/views/partials/alerts.php'; ?>

<!-- Page Header -->
<div class="flex flex-col md:flex-row justify-between items-end gap-6 mb-10">
  <div>
    <h1 class="text-4xl font-extrabold tracking-tight text-primary mb-1">
      Welcome back, <?= e($user['name'] ?? 'Farmer') ?>
    </h1>
    <p class="text-on-surface-variant font-medium">
      <?= e($user['region'] ?? 'Ghana') ?> &bull; AgriLink Farmer
    </p>
  </div>
  <a href="<?= APP_URL ?>/farmer/listings/add"
     class="flex items-center gap-2 bg-primary text-white px-6 py-3 rounded-xl font-bold hover:opacity-90 transition-all shadow-lg shadow-primary/20">
    <span class="material-symbols-outlined">add_circle</span>Add New Produce
  </a>
</div>

<!-- Stats Row -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
  <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">

    <!-- Total Revenue -->
    <div class="bg-primary p-8 rounded-[2rem] text-white flex flex-col justify-between relative overflow-hidden">
      <div class="absolute top-0 right-0 p-8 opacity-10">
        <span class="material-symbols-outlined" style="font-size:5rem">payments</span>
      </div>
      <div>
        <p class="text-emerald-200/80 font-bold tracking-wider uppercase text-xs mb-1">Total Revenue</p>
        <h2 class="text-5xl font-extrabold tracking-tighter">
          <span class="text-3xl font-medium mr-1">₵</span><?= number_format($stats['total_revenue'], 0) ?>
        </h2>
      </div>
      <div class="mt-4 flex items-center gap-2 text-emerald-300">
        <span class="material-symbols-outlined text-sm">trending_up</span>
        <span class="text-sm font-semibold"><?= $stats['total_orders'] ?> total orders</span>
      </div>
    </div>

    <!-- Active Listings -->
    <div class="bg-surface-container-low p-8 rounded-[2rem] border-l-4 border-secondary-container flex flex-col justify-between">
      <div>
        <p class="text-on-surface-variant font-bold tracking-wider uppercase text-xs mb-1">Active Listings</p>
        <h2 class="text-4xl font-extrabold tracking-tight text-primary">
          <?= str_pad($stats['active_listings'], 2, '0', STR_PAD_LEFT) ?>
        </h2>
      </div>
      <div class="flex -space-x-3 mt-4">
        <?php foreach (['Tomato'=>'tomato','Yam'=>'yam','Cocoa'=>'cocoa'] as $pname=>$pcat): ?>
        <div class="w-10 h-10 rounded-full border-2 border-white overflow-hidden flex-shrink-0 shadow-sm" title="<?= $pname ?>">
          <img src="<?= Helpers::produceImage($pname, $pcat) ?>" alt="<?= $pname ?>" class="w-full h-full object-cover">
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- Pending Orders -->
  <div class="bg-surface-container-lowest p-8 rounded-[2rem] shadow-sm flex flex-col justify-between border border-outline-variant/10">
    <div class="flex justify-between items-start">
      <h3 class="text-xl font-bold text-primary">Pending Orders</h3>
      <span class="bg-tertiary-fixed text-on-tertiary-fixed-variant px-3 py-1 rounded-full text-xs font-bold">
        <?= $stats['pending_orders'] ?> NEW
      </span>
    </div>
    <div class="mt-6 space-y-4">
      <?php if (empty($recentOrders)): ?>
        <p class="text-sm text-on-surface-variant">No orders yet.</p>
      <?php else: ?>
        <?php foreach (array_slice($recentOrders, 0, 3) as $o): ?>
        <div class="flex items-center gap-4">
          <div class="w-10 h-10 rounded-xl bg-surface-container flex items-center justify-center">
            <span class="material-symbols-outlined text-primary">person</span>
          </div>
          <div class="flex-1">
            <p class="text-sm font-bold"><?= e($o['buyer_name'] ?? 'Buyer') ?></p>
            <p class="text-xs text-on-surface-variant"><?= e($o['produce_name'] ?? '') ?> &bull; <?= e($o['quantity'] ?? '') ?> <?= e($o['unit'] ?? '') ?></p>
          </div>
          <p class="font-bold text-primary">₵<?= number_format($o['total_price'], 0) ?></p>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
    <a href="<?= APP_URL ?>/farmer/orders"
       class="block w-full mt-6 py-3 rounded-xl bg-surface-container-high font-bold text-center text-on-surface hover:bg-surface-container-highest transition-colors">
      View All Orders
    </a>
  </div>
</div>

<!-- Inventory Table -->
<section>
  <div class="flex items-center justify-between mb-6">
    <h3 class="text-2xl font-bold text-primary">Current Inventory &amp; Listings</h3>
    <a href="<?= APP_URL ?>/farmer/listings"
       class="flex items-center gap-1 text-sm font-bold text-primary hover:underline">
      <span class="material-symbols-outlined" style="font-size:1rem">open_in_new</span> Manage All
    </a>
  </div>

  <div class="bg-surface-container-lowest rounded-[2rem] overflow-hidden shadow-sm border border-outline-variant/10">
    <?php if (empty($listings)): ?>
      <div class="p-12 text-center text-on-surface-variant">
        <span class="material-symbols-outlined" style="font-size:3rem">inventory_2</span>
        <p class="mt-2 font-bold">No listings yet.</p>
        <a href="<?= APP_URL ?>/farmer/listings/add" class="btn btn-primary mt-4 inline-flex">Add First Listing</a>
      </div>
    <?php else: ?>
    <div class="overflow-x-auto">
      <table class="w-full text-left">
        <thead>
          <tr class="bg-surface-container-low/50">
            <th class="px-8 py-5 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Produce Item</th>
            <th class="px-8 py-5 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Quantity</th>
            <th class="px-8 py-5 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Unit Price</th>
            <th class="px-8 py-5 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Status</th>
            <th class="px-8 py-5 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-surface-container">
          <?php foreach (array_slice($listings, 0, 6) as $item): ?>
          <tr class="hover:bg-surface-container-low/30 transition-colors">
            <td class="px-8 py-6">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl overflow-hidden flex-shrink-0 shadow-sm">
                  <img src="<?= Helpers::produceImage($item['name'], $item['category'] ?? '') ?>"
                       alt="<?= e($item['name']) ?>"
                       class="w-full h-full object-cover">
                </div>
                <div>
                  <p class="font-extrabold text-primary"><?= e($item['name']) ?></p>
                  <p class="text-xs text-on-surface-variant">
                    <?= e($item['category']) ?> &bull; <?= e($item['region']) ?>
                  </p>
                </div>
              </div>
            </td>
            <td class="px-8 py-6">
              <span class="font-bold text-on-surface"><?= e($item['quantity']) ?> <?= e($item['unit']) ?></span>
              <div class="w-24 h-1.5 bg-surface-container rounded-full mt-2">
                <div class="h-full bg-primary-container rounded-full" style="width:<?= min(100, ($item['quantity'] / max(1, 200)) * 100) ?>%"></div>
              </div>
            </td>
            <td class="px-8 py-6">
              <span class="text-xl font-bold text-primary">₵<?= number_format($item['price_per_unit'], 0) ?></span>
              <span class="text-xs text-on-surface-variant font-medium">/<?= e($item['unit']) ?></span>
            </td>
            <td class="px-8 py-6">
              <?php
                $badges = ['available'=>'bg-primary-fixed text-on-primary-fixed-variant','sold'=>'bg-surface-container-high text-on-surface-variant','in_transit'=>'bg-tertiary-fixed text-on-tertiary-fixed-variant'];
                $cls = $badges[$item['status']] ?? 'bg-surface-container text-on-surface';
              ?>
              <span class="<?= $cls ?> px-4 py-1.5 rounded-full text-xs font-extrabold tracking-wide uppercase">
                <?= ucfirst(str_replace('_', ' ', $item['status'])) ?>
              </span>
            </td>
            <td class="px-8 py-6">
              <a href="<?= APP_URL ?>/farmer/listings/edit?id=<?= $item['id'] ?>"
                 class="p-2 text-on-surface-variant hover:text-primary transition-colors inline-flex">
                <span class="material-symbols-outlined">edit</span>
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- Low Stock Alerts -->
<?php if (!empty($lowStock)): ?>
<section class="mt-10">
  <div class="flex items-center gap-3 mb-5">
    <div class="w-9 h-9 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0">
      <span class="material-symbols-outlined text-red-600" style="font-size:1.1rem">warning</span>
    </div>
    <div>
      <h3 class="text-xl font-bold text-red-700">Low Stock Alerts</h3>
      <p class="text-xs text-on-surface-variant">These listings are running low — restock soon to avoid missed orders</p>
    </div>
  </div>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php foreach ($lowStock as $ls): ?>
    <div class="bg-red-50 border border-red-200 rounded-[1.25rem] p-5 flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0">
        <span class="material-symbols-outlined text-red-500">inventory_2</span>
      </div>
      <div class="flex-1 min-w-0">
        <p class="font-bold text-red-800 truncate"><?= e($ls['name']) ?></p>
        <p class="text-xs text-red-600 mt-0.5">
          <strong><?= e($ls['quantity']) ?> <?= e($ls['unit']) ?></strong> remaining
          &bull; threshold: <?= e($ls['low_stock_threshold']) ?>
        </p>
        <div class="mt-2 h-1.5 bg-red-200 rounded-full overflow-hidden">
          <?php $pct = min(100, ($ls['quantity'] / max(1, $ls['low_stock_threshold'] * 2)) * 100); ?>
          <div class="h-full bg-red-500 rounded-full" style="width:<?= $pct ?>%"></div>
        </div>
      </div>
      <a href="<?= APP_URL ?>/farmer/listings/edit?id=<?= $ls['id'] ?>"
         class="text-red-600 hover:text-red-800 flex-shrink-0 p-1 rounded-lg hover:bg-red-100 transition-colors">
        <span class="material-symbols-outlined">edit</span>
      </a>
    </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- Farmer Rating & Regional Demand -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-10">

  <!-- Your Farmer Rating -->
  <?php if (!empty($farmerRating['total']) && $farmerRating['total'] > 0): ?>
  <div class="bg-amber-50 border border-amber-200 rounded-[1.5rem] p-6">
    <h3 class="font-bold text-amber-900 mb-4 flex items-center gap-2">
      <span class="material-symbols-outlined text-amber-500">star</span>Your Farmer Rating
    </h3>
    <div class="flex items-center gap-6">
      <div class="text-center">
        <p class="text-5xl font-extrabold text-amber-600"><?= number_format($farmerRating['avg_rating'] ?? 0, 1) ?></p>
        <div class="flex gap-0.5 justify-center mt-1">
          <?php for ($i = 1; $i <= 5; $i++): ?>
          <span class="material-symbols-outlined"
                style="font-size:1rem;color:<?= $i <= round($farmerRating['avg_rating'] ?? 0) ? '#f59e0b' : '#d1d5db' ?>">
            star
          </span>
          <?php endfor; ?>
        </div>
        <p class="text-xs text-amber-700 mt-1"><?= $farmerRating['total'] ?> review<?= $farmerRating['total'] != 1 ? 's' : '' ?></p>
      </div>
      <div class="flex-1">
        <p class="text-sm text-amber-800 font-medium leading-relaxed">
          <?php if ($farmerRating['avg_rating'] >= 4.5): ?>
            Excellent! Buyers love your produce. Keep up the great quality!
          <?php elseif ($farmerRating['avg_rating'] >= 3.5): ?>
            Good rating. Focus on packaging and timely updates to improve further.
          <?php else: ?>
            There's room to improve. Consider better quality control and buyer communication.
          <?php endif; ?>
        </p>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- Regional Demand Insights -->
  <?php if (!empty($regionalDemand)): ?>
  <div class="bg-blue-50 border border-blue-200 rounded-[1.5rem] p-6">
    <h3 class="font-bold text-blue-900 mb-4 flex items-center gap-2">
      <span class="material-symbols-outlined text-blue-500">insights</span>
      Top Demand in Your Region
    </h3>
    <p class="text-xs text-blue-700 mb-4">Most ordered categories from buyers in your region (last 90 days)</p>
    <div class="space-y-3">
      <?php
        $maxQty = max(array_column($regionalDemand, 'total_qty'));
        $barColors = ['#2c694e','#0284c7','#7c3aed','#d97706','#dc2626'];
      ?>
      <?php foreach ($regionalDemand as $i => $rd): ?>
      <div>
        <div class="flex items-center justify-between text-xs font-semibold mb-1">
          <span class="text-blue-800"><?= e($rd['category']) ?></span>
          <span class="text-blue-600"><?= number_format($rd['total_qty'], 0) ?> units ordered</span>
        </div>
        <div class="h-2 bg-blue-100 rounded-full overflow-hidden">
          <div class="h-full rounded-full"
               style="width:<?= $maxQty > 0 ? round(($rd['total_qty'] / $maxQty) * 100) : 0 ?>%;
                      background:<?= $barColors[$i % 5] ?>">
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

</div>

</main>
</div>
</div>
<?php include BASE_PATH . '/app/views/partials/foot.php'; ?>
