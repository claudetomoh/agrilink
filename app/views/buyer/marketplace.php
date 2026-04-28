<?php include BASE_PATH . '/app/views/partials/head.php'; ?>
<div class="app-layout">
<?php include BASE_PATH . '/app/views/partials/sidebar.php'; ?>
<div class="main-wrap">
<?php include BASE_PATH . '/app/views/partials/topbar.php'; ?>
<main class="main-content">
<?php include BASE_PATH . '/app/views/partials/alerts.php'; ?>

<!-- Page Header -->
<div class="flex flex-col md:flex-row justify-between items-end gap-6 mb-8">
  <div>
    <h1 class="text-3xl font-extrabold tracking-tight text-primary">Marketplace</h1>
    <p class="text-on-surface-variant text-sm mt-1">
      <?= count($listings) ?> produce listings available
    </p>
  </div>
</div>

<!-- Category Quick-Browse Banners -->
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
  <?php foreach ([
    ['Tomatoes',  'tomato',   'Vegetables',        'Brong-Ahafo, Ashanti'],
    ['Cocoa',     'cocoa',    'Cocoa & Cash Crops', 'Ashanti, Western'],
    ['Yam',       'yam',      'Tubers',             'Brong-Ahafo, Volta'],
    ['Plantain',  'plantain', 'Fruits',             'Ashanti, Eastern'],
  ] as [$pname, $pcat, $filter, $regions]): ?>
  <a href="<?= APP_URL ?>/buyer/marketplace?category=<?= urlencode($filter) ?>"
     class="relative rounded-[1.5rem] overflow-hidden shadow-sm group" style="height:130px;text-decoration:none">
    <img src="<?= Helpers::produceImage($pname, $pcat, 400) ?>" alt="<?= $pname ?>"
         class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
    <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/20 to-transparent"></div>
    <div class="absolute bottom-0 left-0 right-0 p-3">
      <p class="text-white text-sm font-extrabold leading-tight"><?= $pname ?></p>
      <p class="text-white/60 text-[10px] mt-0.5"><?= $regions ?></p>
    </div>
  </a>
  <?php endforeach; ?>
</div>

<!-- Recommended for You strip (only when no active filters) -->
<?php if (!empty($recommended) && !array_filter($filters)): ?>
<section class="mb-8">
  <div class="flex items-center justify-between mb-4">
    <h2 class="text-lg font-bold text-primary flex items-center gap-2">
      <span class="material-symbols-outlined text-amber-500">auto_awesome</span>
      Recommended for You
    </h2>
    <span class="text-xs text-on-surface-variant font-medium">Based on your region</span>
  </div>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <?php foreach ($recommended as $rec): ?>
    <a href="<?= APP_URL ?>/buyer/product?id=<?= $rec['id'] ?>"
       class="bg-gradient-to-br from-primary to-primary-dim rounded-[1.5rem] p-5
              border border-primary/30 hover:shadow-lg transition-all no-underline block"
       style="text-decoration:none;color:inherit">
      <div class="flex items-center gap-3 mb-3">
        <div class="w-11 h-11 rounded-xl overflow-hidden flex-shrink-0">
          <img src="<?= !empty($rec['image']) ? APP_URL . e($rec['image']) : Helpers::produceImage($rec['name'], $rec['category'] ?? '') ?>"
               alt="<?= e($rec['name']) ?>"
               class="w-full h-full object-cover">
        </div>
        <div class="flex-1 min-w-0">
          <p class="font-extrabold text-white truncate text-sm"><?= e($rec['name']) ?></p>
          <p class="text-xs text-emerald-100/80 truncate"><?= e($rec['region']) ?></p>
        </div>
      </div>
      <div class="flex items-center justify-between">
        <p class="text-xl font-extrabold text-white">₵<?= number_format($rec['price_per_unit'], 0) ?><span class="text-xs font-normal opacity-70">/<?= e($rec['unit']) ?></span></p>
        <span class="text-xs bg-white/20 text-white px-2 py-0.5 rounded-full font-bold">Match</span>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

  <!-- Filters Sidebar -->
  <aside class="lg:col-span-1">
    <div class="bg-surface-container-lowest rounded-[1.5rem] p-6 border border-outline-variant/10 shadow-sm sticky top-20">
      <h3 class="font-bold text-primary mb-5 flex items-center gap-2">
        <span class="material-symbols-outlined" style="font-size:1.1rem">tune</span>Filters
      </h3>
      <form method="GET" action="<?= APP_URL ?>/buyer/marketplace">
        <!-- Search -->
        <div class="form-group">
          <label class="form-label text-xs">Search</label>
          <input type="text" name="q" class="form-input text-sm" placeholder="Yam, Cocoa…"
                 value="<?= e($filters['search']) ?>">
        </div>
        <!-- Region -->
        <div class="form-group">
          <label class="form-label text-xs">Region</label>
          <select name="region" class="form-select text-sm">
            <option value="">All Regions</option>
            <?php foreach (GH_REGIONS as $r): ?>
            <option value="<?= e($r) ?>" <?= $filters['region']===$r?'selected':'' ?>><?= e($r) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <!-- Category -->
        <div class="form-group">
          <label class="form-label text-xs">Category</label>
          <select name="category" class="form-select text-sm">
            <option value="">All Categories</option>
            <?php foreach(['Tubers','Cereals','Legumes','Vegetables','Fruits','Cocoa & Cash Crops','Spices'] as $cat): ?>
            <option value="<?= e($cat) ?>" <?= $filters['category']===$cat?'selected':'' ?>><?= e($cat) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <!-- Price Range -->
        <div class="form-group">
          <label class="form-label text-xs">Min Price (₵)</label>
          <input type="number" name="min_price" class="form-input text-sm" placeholder="0" min="0"
                 value="<?= $filters['min_price'] ?: '' ?>">
        </div>
        <div class="form-group">
          <label class="form-label text-xs">Max Price (₵)</label>
          <input type="number" name="max_price" class="form-input text-sm" placeholder="Any" min="0"
                 value="<?= $filters['max_price'] ?: '' ?>">
        </div>
        <!-- Verified Only -->
        <div class="form-group">
          <label class="flex items-center gap-2 cursor-pointer text-sm font-medium text-on-surface">
            <input type="checkbox" name="verified_only" value="1" class="rounded"
                   <?= !empty($filters['verified_only']) ? 'checked' : '' ?>>
            Verified farmers only
          </label>
        </div>
        <button type="submit" class="btn btn-primary btn-full text-sm mt-2">Apply Filters</button>
        <?php if (array_filter($filters)): ?>
        <a href="<?= APP_URL ?>/buyer/marketplace" class="btn btn-secondary btn-full text-sm mt-2 text-center block">Clear All</a>
        <?php endif; ?>
      </form>
    </div>
  </aside>

  <!-- Product Grid -->
  <div class="lg:col-span-3">
    <?php if (empty($listings)): ?>
    <div class="bg-surface-container-lowest rounded-[2rem] p-16 text-center border border-outline-variant/10">
      <span class="material-symbols-outlined" style="font-size:4rem;color:var(--outline)">search_off</span>
      <p class="mt-4 text-xl font-bold text-primary">No produce found</p>
      <p class="text-on-surface-variant text-sm mt-1">Try adjusting your filters or check back later.</p>
      <a href="<?= APP_URL ?>/buyer/marketplace" class="btn btn-primary mt-6 inline-flex">View All Listings</a>
    </div>
    <?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
      <?php foreach ($listings as $item): ?>
      <div class="group bg-surface-container-lowest rounded-[1.5rem] overflow-hidden border border-outline-variant/10 shadow-sm hover:shadow-lg transition-all flex flex-col">
        <!-- Image -->
        <div class="h-40 relative overflow-hidden">
          <img src="<?= !empty($item['image']) ? APP_URL . e($item['image']) : Helpers::produceImage($item['name'], $item['category'] ?? '') ?>"
               alt="<?= e($item['name']) ?>"
               class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
          <span class="absolute top-3 right-3 flex flex-col gap-1 items-end">
            <?php if ($item['status'] === 'available'): ?>
            <span class="badge badge-success">Available</span>
            <?php endif; ?>
            <?php if (!empty($item['farmer_verified'])): ?>
            <span class="badge" style="background:#0891b2;color:#fff;font-size:.6rem;
                                       display:flex;align-items:center;gap:.15rem">
              <span class="material-symbols-outlined" style="font-size:.75rem">verified</span>Verified
            </span>
            <?php endif; ?>
          </span>
        </div>
        <div class="p-5 flex flex-col flex-1">
          <div class="mb-2">
            <h3 class="font-extrabold text-primary text-lg leading-tight"><?= e($item['name']) ?></h3>
            <p class="text-xs text-on-surface-variant mt-0.5 flex items-center gap-1">
              <span class="material-symbols-outlined" style="font-size:.85rem">location_on</span>
              <?= e($item['region']) ?><?= $item['town'] ? ', ' . e($item['town']) : '' ?>
            </p>
          </div>
          <!-- Star rating -->
          <?php if (!empty($item['farmer_avg_rating']) && $item['farmer_avg_rating'] > 0): ?>
          <div class="flex items-center gap-1 mb-2">
            <?php
              $avgR = (float)$item['farmer_avg_rating'];
              for ($s = 1; $s <= 5; $s++):
            ?>
            <span class="material-symbols-outlined"
                  style="font-size:.85rem;color:<?= $s <= round($avgR) ? '#f59e0b' : '#d1d5db' ?>">star</span>
            <?php endfor; ?>
            <span class="text-xs text-on-surface-variant font-semibold">
              <?= number_format($avgR, 1) ?>
              <?php if (!empty($item['farmer_review_count'])): ?>
              (<?= $item['farmer_review_count'] ?>)
              <?php endif; ?>
            </span>
          </div>
          <?php endif; ?>
          <div class="flex items-center justify-between mt-2 mb-4">
            <div>
              <span class="text-2xl font-extrabold text-primary">₵<?= number_format($item['price_per_unit'], 0) ?></span>
              <span class="text-xs text-on-surface-variant">/ <?= e($item['unit']) ?></span>
            </div>
            <span class="text-sm font-semibold text-on-surface-variant"><?= e($item['quantity']) ?> <?= e($item['unit']) ?> left</span>
          </div>
          <div class="flex items-center gap-2 text-xs text-on-surface-variant mb-4">
            <span class="material-symbols-outlined" style="font-size:.9rem">person</span>
            <?= e($item['farmer_name'] ?? 'Farmer') ?>
            <?php if (!empty($item['farmer_verified'])): ?>
            <span class="material-symbols-outlined" style="font-size:.85rem;color:#0891b2" title="Verified Farmer">verified</span>
            <?php endif; ?>
            <span class="ml-auto bg-surface-container px-2 py-0.5 rounded-full"><?= e($item['category']) ?></span>
          </div>
          <div class="mt-auto">
            <a href="<?= APP_URL ?>/buyer/product?id=<?= $item['id'] ?>"
               class="btn btn-primary btn-full text-sm">
              View &amp; Order
            </a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>

</div>

</main>
</div>
</div>
<?php include BASE_PATH . '/app/views/partials/foot.php'; ?>
