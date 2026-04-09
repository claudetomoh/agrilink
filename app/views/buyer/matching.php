<?php include BASE_PATH . '/app/views/partials/head.php'; ?>
<div class="app-layout">
<?php include BASE_PATH . '/app/views/partials/sidebar.php'; ?>
<div class="main-wrap">
<?php include BASE_PATH . '/app/views/partials/topbar.php'; ?>
<main class="main-content">
<?php include BASE_PATH . '/app/views/partials/alerts.php'; ?>

<!-- Page Header -->
<div class="mb-10">
  <span class="text-primary font-bold text-[11px] uppercase tracking-widest block mb-2">Smart Feature</span>
  <h1 class="text-4xl font-extrabold tracking-tight text-on-surface leading-tight mb-2">
    Supply-Demand <span class="text-primary">Matching</span>
  </h1>
  <p class="text-on-surface-variant max-w-2xl font-medium">
    Precision stewardship for Ghana's food systems. We score every listing against your profile to surface the best matches first.
  </p>
</div>

<!-- Filter Form -->
<div class="bg-surface-container-lowest rounded-[1.5rem] p-6 shadow-sm mb-8 border border-outline-variant/10">
  <form method="GET" action="<?= APP_URL ?>/buyer/matching" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
    <div>
      <label class="block text-[11px] font-bold uppercase tracking-wider text-outline mb-2">Produce Category</label>
      <select name="category" class="form-select text-sm w-full">
        <option value="">Any Category</option>
        <?php foreach ($categories as $c): ?>
        <option value="<?= e($c) ?>" <?= ($category === $c) ? 'selected' : '' ?>><?= e(ucwords(str_replace('_',' ',$c))) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label class="block text-[11px] font-bold uppercase tracking-wider text-outline mb-2">Min Quantity (bags/kg)</label>
      <input type="number" name="quantity" value="<?= $quantity ?: '' ?>" min="0" step="1"
             placeholder="e.g. 50"
             class="form-input text-sm w-full">
    </div>
    <div>
      <label class="block text-[11px] font-bold uppercase tracking-wider text-outline mb-2">Max Price / Unit (₵)</label>
      <input type="number" name="budget" value="<?= $budget ?: '' ?>" min="0" step="0.01"
             placeholder="e.g. 120"
             class="form-input text-sm w-full">
    </div>
    <div class="flex items-end">
      <button type="submit"
              class="btn btn-primary w-full flex items-center justify-center gap-2">
        <span class="material-symbols-outlined text-[18px]">analytics</span>
        Find Matches
      </button>
    </div>
  </form>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

  <!-- ── Left Column: Regional Demand + Insights ─────────────────── -->
  <div class="lg:col-span-8 space-y-8">

    <!-- Regional Demand Heatmap (data-driven) -->
    <section class="bg-surface-container-lowest rounded-[2rem] overflow-hidden shadow-sm border border-outline-variant/10">
      <div class="p-6 pb-4 flex justify-between items-center">
        <h3 class="text-xl font-bold tracking-tight">Regional Supply Overview</h3>
        <span class="text-[11px] font-bold text-primary bg-primary-container px-3 py-1 rounded-full uppercase tracking-wider">
          Live Data
        </span>
      </div>

      <?php if (!empty($regionCounts)): ?>
      <div class="px-6 pb-6 space-y-3">
        <?php
        $maxCount = max(array_column(array_values($regionCounts), 'count'));
        $colors   = ['#2c694e','#1e5d43','#3f6750','#126c4a','#006040','#004930'];
        $ci = 0;
        foreach ($regionCounts as $region => $data):
          $pct  = $maxCount > 0 ? round(($data['count'] / $maxCount) * 100) : 0;
          $col  = $colors[$ci % count($colors)]; $ci++;
        ?>
        <div class="flex items-center gap-4">
          <div class="w-28 flex-shrink-0 text-sm font-semibold text-on-surface truncate"><?= e($region) ?></div>
          <div class="flex-1 h-8 bg-surface-container rounded-md overflow-hidden relative">
            <div class="h-full rounded-md transition-all duration-500 flex items-center px-3"
                 style="width:<?= max(10, $pct) ?>%; background:<?= $col ?>">
              <span class="text-white text-xs font-bold"><?= $data['count'] ?> listings</span>
            </div>
          </div>
          <div class="w-24 text-right text-sm font-bold text-primary flex-shrink-0">
            ₵<?= number_format($data['value'], 0) ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
      <div class="px-6 pb-6">
        <div class="h-40 flex items-center justify-center bg-surface-container rounded-xl">
          <div class="text-center">
            <span class="material-symbols-outlined text-5xl text-outline">map</span>
            <p class="text-on-surface-variant text-sm mt-2">No regional data available yet</p>
          </div>
        </div>
      </div>
      <?php endif; ?>
    </section>

    <!-- Insights Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

      <!-- Demand Insight Card -->
      <div class="bg-primary p-8 rounded-[2rem] relative overflow-hidden">
        <div class="relative z-10">
          <span class="material-symbols-outlined text-secondary-container text-4xl mb-4 block">insights</span>
          <h4 class="text-2xl font-bold text-white mb-2">Demand Insights</h4>
          <?php
            $topRegion = !empty($regionCounts) ? array_key_first($regionCounts) : 'Greater Accra';
            $topCount  = $regionCounts[$topRegion]['count'] ?? 0;
          ?>
          <p class="text-green-100 text-base font-medium">
            Highest supply concentration in <strong><?= e($topRegion) ?></strong>
            (<?= $topCount ?> listings)
          </p>
          <div class="mt-5 flex items-center gap-2 text-green-200">
            <span class="material-symbols-outlined text-sm">trending_up</span>
            <span class="text-xs font-bold uppercase tracking-widest">
              <?= count($all) ?> Active Listings Scored
            </span>
          </div>
        </div>
        <div class="absolute -right-8 -bottom-8 opacity-10">
          <span class="material-symbols-outlined text-[160px]">agriculture</span>
        </div>
      </div>

      <!-- Logistics Status Card -->
      <div class="bg-surface-container-low p-8 rounded-[2rem] flex flex-col justify-between border border-outline-variant/10">
        <div>
          <h4 class="text-xl font-bold mb-4">Match Scoring Key</h4>
          <ul class="space-y-3">
            <li class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <span class="w-8 h-1.5 rounded-full inline-block" style="background:#2c694e"></span>
                <span class="text-on-surface-variant font-medium text-sm">Same Region</span>
              </div>
              <span class="text-primary font-extrabold">+40</span>
            </li>
            <li class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <span class="w-8 h-1.5 rounded-full inline-block" style="background:#3f6750"></span>
                <span class="text-on-surface-variant font-medium text-sm">Category Match</span>
              </div>
              <span class="text-primary font-extrabold">+30</span>
            </li>
            <li class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <span class="w-8 h-1.5 rounded-full inline-block bg-secondary-container inline-block"></span>
                <span class="text-on-surface-variant font-medium text-sm">Quantity Sufficient</span>
              </div>
              <span class="text-primary font-extrabold">+20</span>
            </li>
            <li class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <span class="w-8 h-1.5 rounded-full inline-block bg-outline-variant inline-block"></span>
                <span class="text-on-surface-variant font-medium text-sm">Within Budget</span>
              </div>
              <span class="text-primary font-extrabold">+10</span>
            </li>
          </ul>
        </div>
        <p class="text-xs text-outline mt-4 font-medium border-t border-outline-variant/20 pt-4">
          Max possible score: <strong class="text-primary">100 pts</strong>. Region contributes the most — confirm yours in your profile.
        </p>
      </div>
    </div>

  </div>

  <!-- ── Right Column: Match Cards ───────────────────────────────── -->
  <div class="lg:col-span-4 space-y-5">
    <h3 class="text-xl font-bold tracking-tight">
      <?= count($all) ?> Match<?= count($all) !== 1 ? 'es' : '' ?> Found
    </h3>

    <?php if (empty($all)): ?>
    <div class="bg-surface-container-lowest rounded-[2rem] p-12 text-center border border-outline-variant/10">
      <span class="material-symbols-outlined text-5xl text-outline">search_off</span>
      <p class="mt-3 font-bold text-on-surface">No listings available</p>
      <p class="text-xs text-outline mt-1">Check back soon or adjust your filters.</p>
    </div>

    <?php else: ?>
    <?php
    $borderColors = ['emerald' => 'border-emerald-600', 'green' => 'border-green-500', 'amber' => 'border-amber-400', 'slate' => 'border-slate-400'];
    $labelColors  = ['emerald' => 'text-emerald-700 bg-emerald-50', 'green' => 'text-green-700 bg-green-50', 'amber' => 'text-amber-700 bg-amber-50', 'slate' => 'text-slate-600 bg-slate-100'];
    $strokeColors = ['emerald' => '#059669', 'green' => '#16a34a', 'amber' => '#d97706', 'slate' => '#94a3b8'];
    $circumference = 2 * 3.14159 * 28; // ≈ 175.9
    ?>

    <?php foreach (array_slice($all, 0, 8) as $i => $item):
      $score    = $item['match_score'];
      $cls      = $item['score_class'];
      $offset   = $circumference * (1 - $score / 100);
      $border   = $borderColors[$cls] ?? 'border-slate-400';
      $lblCls   = $labelColors[$cls]  ?? 'text-slate-600 bg-slate-100';
      $stroke   = $strokeColors[$cls] ?? '#94a3b8';
      $opacity  = $i === 0 ? '' : 'opacity-90';
    ?>
    <article class="bg-surface-container-lowest rounded-[1.5rem] p-5 shadow-sm hover:shadow-md transition-all <?= $opacity ?> border-l-4 <?= $border ?>">
      <div class="flex justify-between items-start mb-4">
        <div class="flex-1 min-w-0">
          <span class="text-[10px] font-bold <?= $lblCls ?> px-2 py-0.5 rounded-full mb-1.5 inline-block uppercase tracking-wider">
            <?= e($item['match_label']) ?>
          </span>
          <h4 class="text-base font-extrabold text-primary leading-tight truncate"><?= e($item['name']) ?></h4>
          <p class="text-xs text-on-surface-variant mt-0.5"><?= e($item['town'] ?? $item['region']) ?></p>
        </div>

        <!-- Circle Score Indicator -->
        <div class="relative flex items-center justify-center w-14 h-14 flex-shrink-0 ml-3">
          <svg class="w-full h-full -rotate-90" viewBox="0 0 64 64">
            <circle cx="32" cy="32" r="28" fill="transparent" stroke="#e7eff8" stroke-width="5"></circle>
            <circle cx="32" cy="32" r="28" fill="transparent"
                    stroke="<?= e($stroke) ?>"
                    stroke-width="5"
                    stroke-dasharray="<?= round($circumference, 1) ?>"
                    stroke-dashoffset="<?= round($offset, 1) ?>"
                    stroke-linecap="round"></circle>
          </svg>
          <span class="absolute text-xs font-extrabold" style="color:<?= e($stroke) ?>"><?= $score ?>%</span>
        </div>
      </div>

      <!-- Details -->
      <div class="space-y-1.5 mb-4">
        <div class="flex items-center gap-2 text-xs text-on-surface-variant">
          <span class="material-symbols-outlined text-[16px]">person</span>
          <span class="font-medium">Farmer:</span>
          <span><?= e($item['farmer_name'] ?? '—') ?></span>
        </div>
        <div class="flex items-center gap-2 text-xs text-on-surface-variant">
          <span class="material-symbols-outlined text-[16px]">inventory_2</span>
          <span class="font-medium">Stock:</span>
          <span><?= number_format($item['quantity'], 0) ?> <?= e($item['unit']) ?></span>
        </div>
        <div class="flex items-center gap-2 text-xs text-on-surface-variant">
          <span class="material-symbols-outlined text-[16px]">category</span>
          <span class="font-medium">Category:</span>
          <span><?= e(ucfirst(str_replace('_',' ',$item['category']))) ?></span>
        </div>
      </div>

      <div class="flex items-center justify-between pt-3 border-t border-outline-variant/10">
        <div>
          <span class="text-base font-extrabold text-primary">₵<?= number_format($item['price_per_unit'], 2) ?></span>
          <span class="text-xs text-outline">/ <?= e($item['unit']) ?></span>
        </div>
        <a href="<?= APP_URL ?>/buyer/product?id=<?= $item['id'] ?>"
           class="bg-gradient-to-br from-primary to-primary-dim text-white text-xs font-bold px-4 py-2 rounded-lg flex items-center gap-1.5 hover:opacity-90 transition-opacity">
          View &amp; Order
          <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
        </a>
      </div>
    </article>
    <?php endforeach; ?>

    <?php if (count($all) > 8): ?>
    <a href="<?= APP_URL ?>/buyer/marketplace"
       class="block w-full text-center py-3 bg-surface-container text-primary font-bold text-sm rounded-xl hover:bg-surface-container-high transition-colors">
      View All <?= count($all) ?> Listings in Marketplace
      <span class="material-symbols-outlined text-[14px] align-middle ml-1">open_in_new</span>
    </a>
    <?php endif; ?>
    <?php endif; ?>

    <!-- System Recommendation -->
    <div class="p-5 bg-surface-container-low rounded-[1.5rem] border border-outline-variant/10">
      <div class="flex items-center gap-2 mb-2">
        <span class="material-symbols-outlined text-primary text-[20px]">verified</span>
        <h5 class="text-xs font-extrabold uppercase tracking-widest text-primary">System Recommendation</h5>
      </div>
      <?php
        $topItem = $all[0] ?? null;
        if ($topItem && $topItem['match_score'] >= 40):
      ?>
      <p class="text-sm font-medium text-on-surface-variant">
        Ordering <strong><?= e($topItem['name']) ?></strong> from
        <strong><?= e($topItem['farmer_name'] ?? $topItem['region']) ?></strong>
        reduces procurement friction with a <?= $topItem['match_score'] ?>% match to your profile.
      </p>
      <?php else: ?>
      <p class="text-sm font-medium text-on-surface-variant">
        Update your region in your profile for better match scoring. Buyers in same-region transactions save on logistics by an average of 14%.
      </p>
      <?php endif; ?>
    </div>

  </div>
</div>

</main>
<?php include BASE_PATH . '/app/views/partials/foot.php'; ?>
</div>
</div>
