<?php include BASE_PATH . '/app/views/partials/head.php'; ?>
<div class="app-layout">
<?php include BASE_PATH . '/app/views/partials/sidebar.php'; ?>
<div class="main-wrap">
<?php include BASE_PATH . '/app/views/partials/topbar.php'; ?>
<main class="main-content">
<?php include BASE_PATH . '/app/views/partials/alerts.php'; ?>

<!-- Header -->
<div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 mb-10">
  <div>
    <span class="text-primary font-semibold text-[11px] uppercase tracking-widest mb-1 block">Performance Overview</span>
    <h1 class="text-4xl font-extrabold tracking-tight text-on-surface leading-tight">
      Precision Supply Chain<br><span class="text-primary">Intelligence.</span>
    </h1>
  </div>
  <!-- Quick stats pills -->
  <div class="flex flex-wrap gap-3">
    <span class="inline-flex items-center gap-2 bg-primary-container text-on-primary-container px-4 py-2 rounded-full text-sm font-semibold">
      <span class="material-symbols-outlined text-base">receipt_long</span>
      <?= number_format($stats['total_orders']) ?> Orders
    </span>
    <span class="inline-flex items-center gap-2 bg-tertiary-container text-on-tertiary-container px-4 py-2 rounded-full text-sm font-semibold">
      <span class="material-symbols-outlined text-base">local_shipping</span>
      <?= $stats['logistics_efficiency'] ?>% Logistics
    </span>
    <span class="inline-flex items-center gap-2 bg-surface-container-high text-on-surface px-4 py-2 rounded-full text-sm font-semibold">
      <span class="material-symbols-outlined text-base">group</span>
      <?= number_format(($stats['total_farmers'] + $stats['total_buyers'] + $stats['total_transporters'])) ?> Users
    </span>
  </div>
</div>

<!-- Bento Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">

  <!-- KPI: Total Revenue -->
  <div class="bg-surface-container-lowest p-6 rounded-[2rem] shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
    <div class="flex justify-between items-start mb-4">
      <div class="p-2 bg-primary-container rounded-xl text-primary">
        <span class="material-symbols-outlined">payments</span>
      </div>
      <span class="text-secondary font-bold text-xs">Gross Revenue</span>
    </div>
    <h3 class="text-outline text-sm font-medium">Total Revenue</h3>
    <p class="text-3xl font-bold mt-1">₵<?= number_format($stats['gross_revenue'] ?? 0, 0) ?></p>
    <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:opacity-20 transition-opacity">
      <span class="material-symbols-outlined text-9xl" style="font-variation-settings:'FILL' 1">payments</span>
    </div>
  </div>

  <!-- KPI: Delivery Rate -->
  <div class="bg-surface-container-lowest p-6 rounded-[2rem] shadow-sm hover:shadow-md transition-shadow">
    <div class="flex justify-between items-start mb-4">
      <div class="p-2 bg-tertiary-container rounded-xl text-tertiary">
        <span class="material-symbols-outlined">local_shipping</span>
      </div>
      <span class="text-secondary font-bold text-xs">On-time delivery</span>
    </div>
    <h3 class="text-outline text-sm font-medium">Delivery Rate</h3>
    <p class="text-3xl font-bold mt-1"><?= $stats['delivery_rate'] ?>%</p>
  </div>

  <!-- Main Chart: Order Volume (CSS bars) -->
  <div class="md:col-span-1 lg:col-span-2 bg-surface-container-lowest p-8 rounded-[2rem] shadow-sm relative overflow-hidden">
    <div class="flex justify-between items-center mb-6">
      <h3 class="font-bold text-lg">Order Status Breakdown</h3>
      <div class="flex gap-4 text-xs text-outline">
        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-primary inline-block"></span> Delivered</span>
        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-warning inline-block" style="background:#f59e0b"></span> Pending</span>
        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-secondary inline-block"></span> In Transit</span>
      </div>
    </div>
    <?php
      $total   = max(1, $stats['total_orders']);
      $delPct  = round(($stats['delivered_orders'] / $total) * 100);
      $pendPct = round(($stats['pending_orders'] / $total) * 100);
      $tranPct = round(($stats['in_transit'] / $total) * 100);
    ?>
    <div class="space-y-5">
      <div>
        <div class="flex justify-between text-sm mb-1"><span>Delivered</span><span class="font-bold text-primary"><?= $stats['delivered_orders'] ?></span></div>
        <div class="h-4 bg-surface-container rounded-full overflow-hidden">
          <div class="h-full bg-primary rounded-full transition-all" style="width:<?= $delPct ?>%"></div>
        </div>
      </div>
      <div>
        <div class="flex justify-between text-sm mb-1"><span>Pending</span><span class="font-bold" style="color:#f59e0b"><?= $stats['pending_orders'] ?></span></div>
        <div class="h-4 bg-surface-container rounded-full overflow-hidden">
          <div class="h-full rounded-full transition-all" style="width:<?= $pendPct ?>%;background:#f59e0b"></div>
        </div>
      </div>
      <div>
        <div class="flex justify-between text-sm mb-1"><span>In Transit</span><span class="font-bold text-secondary"><?= $stats['in_transit'] ?></span></div>
        <div class="h-4 bg-surface-container rounded-full overflow-hidden">
          <div class="h-full bg-secondary rounded-full transition-all" style="width:<?= $tranPct ?>%"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Network Health (full-width card) -->
  <div class="lg:col-span-2 bg-gradient-to-br from-primary to-primary-dim p-8 rounded-[2rem] text-on-primary flex flex-col justify-between relative overflow-hidden">
    <div class="z-10 relative">
      <h3 class="text-xl font-bold mb-2">Network Health Index</h3>
      <p class="text-primary-container text-sm opacity-90 max-w-xs">Supply chain operating at high efficiency. <?= $stats['in_transit'] ?> active deliveries moving across Ghana.</p>
    </div>
    <div class="mt-8 flex items-center gap-6 z-10 relative">
      <div class="flex flex-col">
        <span class="text-3xl font-bold"><?= $stats['logistics_efficiency'] ?>/100</span>
        <span class="text-xs uppercase tracking-widest font-bold opacity-75">Operational</span>
      </div>
      <div class="h-12 w-px bg-white/20"></div>
      <div class="grid grid-cols-2 gap-3 text-sm">
        <div><span class="opacity-70 text-xs">Farmers</span><br><span class="font-bold text-lg"><?= $stats['total_farmers'] ?></span></div>
        <div><span class="opacity-70 text-xs">Buyers</span><br><span class="font-bold text-lg"><?= $stats['total_buyers'] ?></span></div>
      </div>
    </div>
    <div class="absolute -right-6 -bottom-6 opacity-10 pointer-events-none">
      <span class="material-symbols-outlined" style="font-size:180px;font-variation-settings:'FILL' 1">hub</span>
    </div>
  </div>

  <!-- Top Produce Table -->
  <div class="md:col-span-3 lg:col-span-2 bg-surface-container-lowest rounded-[2rem] shadow-sm overflow-hidden">
    <div class="p-6 flex justify-between items-center border-b border-outline-variant/10">
      <h3 class="font-bold text-lg">Top Produce by Revenue</h3>
      <span class="text-outline text-xs font-medium">Delivered orders only</span>
    </div>
    <div class="px-6 pb-6 pt-2 space-y-1">
      <?php if (empty($topProduce)): ?>
        <p class="text-outline text-sm py-6 text-center">No data yet.</p>
      <?php else: ?>
        <?php foreach ($topProduce as $i => $p): ?>
        <div class="flex items-center justify-between py-3 hover:bg-surface-container-low px-3 rounded-xl transition-colors group">
          <div class="flex items-center gap-4">
            <div class="w-9 h-9 bg-primary-container rounded-full flex items-center justify-center text-primary font-bold text-sm group-hover:bg-primary group-hover:text-on-primary transition-colors">
              <?= $i + 1 ?>
            </div>
            <div>
              <p class="font-bold text-sm"><?= e($p['name']) ?></p>
              <p class="text-xs text-outline"><?= number_format($p['total_qty']) ?> units sold</p>
            </div>
          </div>
          <div class="text-right">
            <p class="font-bold text-sm text-primary">₵<?= number_format($p['revenue'] ?? 0, 0) ?></p>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- Regional Analysis -->
  <div class="md:col-span-3 lg:col-span-2 bg-surface-container-lowest rounded-[2rem] shadow-sm overflow-hidden">
    <div class="p-6 flex justify-between items-center border-b border-outline-variant/10">
      <h3 class="font-bold text-lg">Regional Demand</h3>
      <span class="text-outline text-xs font-medium">Orders by produce origin</span>
    </div>
    <div class="px-6 pb-6 pt-2 space-y-1">
      <?php if (empty($regionalData)): ?>
        <p class="text-outline text-sm py-6 text-center">No regional data yet.</p>
      <?php else: ?>
        <?php $maxRev = max(array_column($regionalData,'revenue') ?: [1]); ?>
        <?php foreach ($regionalData as $row): ?>
        <div class="flex items-center justify-between py-3 hover:bg-surface-container-low px-3 rounded-xl transition-colors group">
          <div class="flex items-center gap-3 w-1/3">
            <div class="p-1.5 bg-surface-container rounded-lg text-primary group-hover:bg-primary group-hover:text-on-primary transition-colors">
              <span class="material-symbols-outlined text-sm">map</span>
            </div>
            <p class="font-bold text-sm truncate"><?= e($row['region'] ?? 'Unknown') ?></p>
          </div>
          <div class="flex-1 px-4">
            <div class="h-2.5 bg-surface-container rounded-full overflow-hidden">
              <div class="h-full bg-primary rounded-full" style="width:<?= round(($row['revenue']/$maxRev)*100) ?>%"></div>
            </div>
          </div>
          <div class="text-right w-28">
            <p class="font-bold text-xs text-primary">₵<?= number_format($row['revenue']??0,0) ?></p>
            <p class="text-[10px] text-outline"><?= $row['order_count'] ?> orders</p>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- User Breakdown -->
  <div class="md:col-span-3 lg:col-span-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="bg-surface-container-lowest p-6 rounded-[2rem] shadow-sm flex items-center gap-4">
      <div class="p-3 bg-primary-container rounded-2xl"><span class="material-symbols-outlined text-primary text-2xl">agriculture</span></div>
      <div><p class="text-sm text-outline font-medium">Farmers</p><p class="text-2xl font-extrabold text-primary"><?= $stats['total_farmers'] ?></p></div>
    </div>
    <div class="bg-surface-container-lowest p-6 rounded-[2rem] shadow-sm flex items-center gap-4">
      <div class="p-3 bg-secondary-container rounded-2xl"><span class="material-symbols-outlined text-secondary text-2xl">shopping_bag</span></div>
      <div><p class="text-sm text-outline font-medium">Buyers</p><p class="text-2xl font-extrabold text-secondary"><?= $stats['total_buyers'] ?></p></div>
    </div>
    <div class="bg-surface-container-lowest p-6 rounded-[2rem] shadow-sm flex items-center gap-4">
      <div class="p-3 bg-tertiary-container rounded-2xl"><span class="material-symbols-outlined text-tertiary text-2xl">local_shipping</span></div>
      <div><p class="text-sm text-outline font-medium">Transporters</p><p class="text-2xl font-extrabold text-tertiary"><?= $stats['total_transporters'] ?></p></div>
    </div>
  </div>

</div><!-- /bento -->

</main>
</div>
</div>
<?php include BASE_PATH . '/app/views/partials/foot.php'; ?>
