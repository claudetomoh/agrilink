<?php include BASE_PATH . '/app/views/partials/head.php'; ?>
<div class="app-layout">
<?php include BASE_PATH . '/app/views/partials/sidebar.php'; ?>
<div class="main-wrap">
<?php include BASE_PATH . '/app/views/partials/topbar.php'; ?>
<main class="main-content">
<?php include BASE_PATH . '/app/views/partials/alerts.php'; ?>

<!-- Hero Header -->
<div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 mb-10">
  <div>
    <span class="text-primary font-bold text-[11px] uppercase tracking-widest block mb-2">Market Intelligence</span>
    <h1 class="text-4xl font-extrabold tracking-tight text-on-surface leading-tight">
      Marketplace <span class="text-primary">Analytics</span>
    </h1>
    <p class="text-on-surface-variant mt-2 max-w-xl font-medium">
      Real-time market data for Ghana's agricultural trade network — pricing, demand trends, and regional insights.
    </p>
  </div>
  <!-- Quick Summary Chips -->
  <div class="flex flex-wrap gap-3">
    <span class="inline-flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-full text-sm font-semibold shadow-sm shadow-primary/20">
      <span class="material-symbols-outlined text-base">payments</span>
      ₵<?= number_format($marketSummary['total_traded'] ?? 0, 0) ?> Traded
    </span>
    <span class="inline-flex items-center gap-2 bg-primary-container text-[#1B4332] px-4 py-2 rounded-full text-sm font-semibold">
      <span class="material-symbols-outlined text-base">group</span>
      <?= ($marketSummary['active_buyers'] ?? 0) ?> Active Buyers
    </span>
    <span class="inline-flex items-center gap-2 bg-secondary-container text-on-secondary-container px-4 py-2 rounded-full text-sm font-semibold">
      <span class="material-symbols-outlined text-base">storefront</span>
      <?= ($marketSummary['active_farmers'] ?? 0) ?> Active Farmers
    </span>
  </div>
</div>

<!-- Hero KPI Row -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

  <!-- Aggregate Market Value -->
  <div class="bg-surface-container-lowest p-6 rounded-[2rem] shadow-sm border-l-4 border-primary relative overflow-hidden group hover:shadow-md transition-shadow">
    <div class="flex justify-between items-start mb-4">
      <div class="p-2 bg-primary-container rounded-xl">
        <span class="material-symbols-outlined text-primary">payments</span>
      </div>
      <span class="text-[10px] font-bold uppercase tracking-widest text-outline">Total Traded Volume</span>
    </div>
    <h2 class="text-4xl font-extrabold tracking-tight">₵<?= number_format($marketSummary['total_traded'] ?? 0, 0) ?></h2>
    <p class="text-on-surface-variant text-sm mt-2 flex items-center gap-1">
      <span class="material-symbols-outlined text-sm text-primary">trending_up</span>
      Active marketplace liquidity
    </p>
    <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity">
      <span class="material-symbols-outlined text-[120px]">payments</span>
    </div>
  </div>

  <!-- Avg Order Value -->
  <div class="bg-surface-container-lowest p-6 rounded-[2rem] shadow-sm border-l-4 border-secondary relative overflow-hidden group hover:shadow-md transition-shadow">
    <div class="flex justify-between items-start mb-4">
      <div class="p-2 bg-secondary-container rounded-xl">
        <span class="material-symbols-outlined text-on-secondary-container">receipt</span>
      </div>
      <span class="text-[10px] font-bold uppercase tracking-widest text-outline">Avg Order Value</span>
    </div>
    <h2 class="text-4xl font-extrabold tracking-tight">₵<?= number_format($marketSummary['avg_order_value'] ?? 0, 0) ?></h2>
    <p class="text-on-surface-variant text-sm mt-2 flex items-center gap-1">
      <span class="material-symbols-outlined text-sm">info</span>
      Per confirmed order
    </p>
    <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity">
      <span class="material-symbols-outlined text-[120px]">receipt</span>
    </div>
  </div>

  <!-- Market Efficiency -->
  <div class="bg-primary p-6 rounded-[2rem] shadow-sm relative overflow-hidden group hover:shadow-md transition-shadow">
    <div class="flex justify-between items-start mb-4">
      <div class="p-2 bg-white/20 rounded-xl">
        <span class="material-symbols-outlined text-white">verified</span>
      </div>
      <span class="text-[10px] font-bold uppercase tracking-widest text-white/70">Precision Index</span>
    </div>
    <?php
      $totalBuyers  = max(1, $marketSummary['active_buyers']  ?? 1);
      $totalFarmers = max(1, $marketSummary['active_farmers'] ?? 1);
      $efficiencyPct = min(100, round(($totalBuyers / max(1, $totalBuyers + $totalFarmers)) * 100 + 50));
    ?>
    <h2 class="text-5xl font-extrabold tracking-tight text-white"><?= $efficiencyPct ?>%</h2>
    <p class="text-white/70 text-sm mt-2">Precision Stewardship Index</p>
    <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:opacity-20 transition-opacity">
      <span class="material-symbols-outlined text-[120px] text-white">insights</span>
    </div>
  </div>

</div>

<!-- Main Analytics Grid -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

  <!-- Price Trends by Category — main chart area -->
  <section class="lg:col-span-8 bg-surface-container-lowest p-8 rounded-[2rem] shadow-sm hover:shadow-md transition-shadow">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
      <div>
        <h3 class="text-xl font-bold tracking-tight">Market Pricing by Category</h3>
        <p class="text-on-surface-variant text-sm mt-1">Average price per unit &nbsp;(₵) across active listings</p>
      </div>
      <div class="flex flex-wrap gap-2">
        <?php $catColors = ['tubers'=>'bg-emerald-900','cereals'=>'bg-amber-600','legumes'=>'bg-green-700','vegetables'=>'bg-teal-600','fruits'=>'bg-orange-600','cash_crops'=>'bg-primary','other'=>'bg-outline']; ?>
        <?php foreach (array_slice($priceTrends, 0, 3) as $pt): ?>
        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold text-white"
              style="background:<?= ['tubers'=>'#064e3b','cereals'=>'#92400e','legumes'=>'#166534','vegetables'=>'#0f766e','fruits'=>'#9a3412','cash_crops'=>'#2c694e','other'=>'#6f7c88'][$pt['category']] ?? '#2c694e' ?>">
          <?= strtoupper($pt['category']) ?>
        </span>
        <?php endforeach; ?>
      </div>
    </div>

    <?php if (empty($priceTrends)): ?>
    <div class="h-52 flex items-center justify-center">
      <div class="text-center">
        <span class="material-symbols-outlined text-5xl text-outline">bar_chart</span>
        <p class="text-on-surface-variant text-sm mt-2">No listing data yet.</p>
      </div>
    </div>
    <?php else: ?>
    <?php
      $maxPrice = max(array_column($priceTrends, 'avg_price'));
      $catColHex = ['tubers'=>'#064e3b','cereals'=>'#92400e','legumes'=>'#166534','vegetables'=>'#0f766e','fruits'=>'#9a3412','cash_crops'=>'#2c694e','other'=>'#6f7c88'];
    ?>
    <!-- Bar Chart -->
    <div class="flex items-end gap-3 md:gap-5 h-52 mb-4">
      <?php foreach ($priceTrends as $pt):
        $barH = $maxPrice > 0 ? max(8, round(($pt['avg_price'] / $maxPrice) * 100)) : 8;
        $col  = $catColHex[$pt['category']] ?? '#2c694e';
      ?>
      <div class="flex-1 flex flex-col items-center gap-1 group relative min-w-0">
        <!-- Tooltip -->
        <div class="absolute -top-10 left-1/2 -translate-x-1/2 bg-on-surface text-surface-container-lowest text-[10px] font-bold px-2 py-1 rounded-md opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-10 pointer-events-none">
          ₵<?= number_format($pt['avg_price'], 0) ?> avg · <?= $pt['listings'] ?> listings
        </div>
        <div class="w-full rounded-t-lg transition-all duration-500"
             style="height:<?= $barH ?>%; background:<?= $col ?>; min-height: 8px;"></div>
        <span class="text-[9px] font-bold text-on-surface-variant uppercase tracking-tight text-center truncate w-full">
          <?= substr(str_replace('_',' ',$pt['category']), 0, 5) ?>
        </span>
      </div>
      <?php endforeach; ?>
    </div>
    <!-- Category Details Table -->
    <div class="space-y-1 mt-4 border-t border-outline-variant/10 pt-4">
      <?php foreach ($priceTrends as $pt):
        $col = $catColHex[$pt['category']] ?? '#2c694e';
      ?>
      <div class="flex items-center justify-between py-2 hover:bg-surface-container-low px-2 rounded-lg transition-colors">
        <div class="flex items-center gap-3">
          <span class="w-3 h-3 rounded-full flex-shrink-0" style="background:<?= $col ?>"></span>
          <span class="text-sm font-semibold"><?= e(ucwords(str_replace('_',' ',$pt['category']))) ?></span>
          <span class="text-xs text-outline"><?= $pt['listings'] ?> listings</span>
        </div>
        <div class="flex items-center gap-6 text-right">
          <div>
            <p class="text-xs text-outline">Avg Price</p>
            <p class="text-sm font-bold text-primary">₵<?= number_format($pt['avg_price'], 2) ?></p>
          </div>
          <div>
            <p class="text-xs text-outline">Total Stock</p>
            <p class="text-sm font-bold"><?= number_format($pt['total_qty'], 0) ?> units</p>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </section>

  <!-- Right Column -->
  <div class="lg:col-span-4 space-y-6">

    <!-- Category Demand Breakdown -->
    <div class="bg-surface-container-lowest p-6 rounded-[2rem] shadow-sm">
      <h3 class="font-bold text-lg mb-4">Category Demand</h3>
      <p class="text-xs text-outline mb-4">Orders placed per produce type</p>
      <?php if (empty($categoryDemand)): ?>
        <p class="text-outline text-sm text-center py-4">No order data yet.</p>
      <?php else:
        $maxOrders = max(array_column($categoryDemand, 'order_count'));
      ?>
      <div class="space-y-3">
        <?php foreach ($categoryDemand as $cd):
          $pct = $maxOrders > 0 ? round(($cd['order_count'] / $maxOrders) * 100) : 0;
          $col = $catColHex[$cd['category']] ?? '#2c694e';
        ?>
        <div>
          <div class="flex justify-between text-xs mb-1.5">
            <span class="font-semibold"><?= e(ucwords(str_replace('_',' ',$cd['category']))) ?></span>
            <span class="font-bold text-primary"><?= $cd['order_count'] ?> orders</span>
          </div>
          <div class="h-2 bg-surface-container rounded-full overflow-hidden">
            <div class="h-full rounded-full" style="width:<?= max(5,$pct) ?>%; background:<?= $col ?>"></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>

    <!-- Top Produce by Revenue -->
    <div class="bg-surface-container-lowest p-6 rounded-[2rem] shadow-sm">
      <h3 class="font-bold text-lg mb-1">Top Produce</h3>
      <p class="text-xs text-outline mb-4">By total revenue generated</p>
      <?php if (empty($topProduce)): ?>
        <p class="text-outline text-sm text-center py-4">No data available.</p>
      <?php else: ?>
      <div class="space-y-2">
        <?php foreach ($topProduce as $i => $tp): ?>
        <div class="flex items-center gap-3 py-2 hover:bg-surface-container-low px-2 rounded-xl transition-colors">
          <div class="w-8 h-8 rounded-full bg-primary-container flex items-center justify-center text-primary font-extrabold text-xs flex-shrink-0">
            <?= $i + 1 ?>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-bold truncate"><?= e($tp['name']) ?></p>
            <p class="text-xs text-outline"><?= number_format($tp['total_qty'] ?? 0) ?> units</p>
          </div>
          <p class="text-sm font-extrabold text-primary flex-shrink-0">₵<?= number_format($tp['revenue'] ?? 0, 0) ?></p>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>

    <!-- Quick Links -->
    <div class="bg-gradient-to-br from-primary to-primary-dim p-6 rounded-[2rem] relative overflow-hidden">
      <div class="relative z-10">
        <h4 class="text-white font-bold mb-1">Smart Matching</h4>
        <p class="text-white/70 text-xs mb-4">Find produce ranked by relevance to your region and preferences.</p>
        <a href="<?= APP_URL ?>/buyer/matching"
           class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white text-sm font-bold px-4 py-2.5 rounded-lg transition-colors">
          <span class="material-symbols-outlined text-[18px]">auto_awesome</span>
          Open Smart Matching
        </a>
      </div>
      <div class="absolute -right-4 -bottom-4 opacity-10 pointer-events-none">
        <span class="material-symbols-outlined text-[100px] text-white">hub</span>
      </div>
    </div>

  </div>

</div>

</main>
<?php include BASE_PATH . '/app/views/partials/foot.php'; ?>
</div>
</div>
