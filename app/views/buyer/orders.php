<?php include BASE_PATH . '/app/views/partials/head.php'; ?>
<div class="app-layout">
<?php include BASE_PATH . '/app/views/partials/sidebar.php'; ?>
<div class="main-wrap">
<?php include BASE_PATH . '/app/views/partials/topbar.php'; ?>
<main class="main-content">
<?php include BASE_PATH . '/app/views/partials/alerts.php'; ?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
  <div>
    <h1 class="text-3xl font-extrabold tracking-tight text-primary">My Orders</h1>
    <p class="text-on-surface-variant text-sm mt-1"><?= count($orders) ?> orders placed</p>
  </div>
  <div class="flex items-center gap-3 flex-wrap">
    <!-- Inline Search -->
    <div class="relative">
      <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none" style="font-size:1.1rem">search</span>
      <input
        type="text"
        id="order-search"
        placeholder="Search orders…"
        oninput="searchOrders(this.value)"
        class="pl-9 pr-4 py-2 rounded-xl border border-outline-variant bg-surface-container-lowest text-sm font-medium text-on-surface focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 w-52 transition-all"
        autocomplete="off">
    </div>
    <a href="<?= APP_URL ?>/buyer/marketplace" class="btn btn-primary flex items-center gap-2">
      <span class="material-symbols-outlined" style="font-size:1rem">storefront</span>Browse Marketplace
    </a>
  </div>
</div>

<!-- Status Filter Tabs -->
<div class="flex flex-wrap gap-2 mb-6" id="status-tabs">
  <button onclick="filterOrders('all')" class="order-tab active px-4 py-2 rounded-xl text-sm font-bold border border-outline-variant transition-colors" data-filter="all">All</button>
  <?php foreach(['pending','confirmed','in_transit','delivered','cancelled'] as $s): ?>
  <button onclick="filterOrders('<?= $s ?>')"
          class="order-tab px-4 py-2 rounded-xl text-sm font-bold border border-outline-variant transition-colors"
          data-filter="<?= $s ?>">
    <?= ucfirst(str_replace('_',' ',$s)) ?>
  </button>
  <?php endforeach; ?>
</div>

<div class="space-y-4" id="orders-list">
  <?php if (empty($orders)): ?>
  <div class="bg-surface-container-lowest rounded-[2rem] p-16 text-center border border-outline-variant/10">
    <span class="material-symbols-outlined" style="font-size:4rem;color:var(--outline)">receipt_long</span>
    <p class="mt-4 text-xl font-bold text-primary">No orders yet</p>
    <p class="text-on-surface-variant text-sm mt-1">Browse the marketplace and place your first order.</p>
    <a href="<?= APP_URL ?>/buyer/marketplace" class="btn btn-primary mt-6 inline-flex">Go to Marketplace</a>
  </div>
  <?php else: ?>
    <?php foreach ($orders as $o): ?>
    <div class="order-card bg-surface-container-lowest rounded-[1.5rem] p-6 border border-outline-variant/10 shadow-sm"
         data-status="<?= e($o['status']) ?>"
         data-search="<?= strtolower(e(($o['produce_name'] ?? '') . ' ' . str_pad($o['id'], 4, '0', STR_PAD_LEFT) . ' ' . ($o['farmer_name'] ?? ''))) ?>">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-surface-container flex items-center justify-center flex-shrink-0">
            <span class="material-symbols-outlined text-primary">yard</span>
          </div>
          <div>
            <div class="flex items-center gap-3 flex-wrap">
              <h3 class="font-extrabold text-primary"><?= e($o['produce_name'] ?? '—') ?></h3>
              <?php
                $cls = [
                  'pending'   =>'badge-warning','confirmed'=>'badge-info',
                  'in_transit'=>'badge-warning','delivered'=>'badge-success','cancelled'=>'badge-error'
                ][$o['status']] ?? 'badge-info';
              ?>
              <span class="badge <?= $cls ?>"><?= ucfirst(str_replace('_',' ',$o['status'])) ?></span>
            </div>
            <p class="text-sm text-on-surface-variant mt-0.5">
              Order <?= str_pad($o['id'], 4, '0', STR_PAD_LEFT) ?> &bull;
              <?= e($o['quantity']) ?> <?= e($o['unit'] ?? '') ?> &bull;
              From <?= e($o['farmer_name'] ?? 'Farmer') ?>
            </p>
          </div>
        </div>
        <div class="text-right flex-shrink-0">
          <p class="text-2xl font-extrabold text-primary">₵<?= number_format($o['total_price'], 2) ?></p>
          <p class="text-xs text-on-surface-variant mt-1"><?= date('d M Y', strtotime($o['created_at'])) ?></p>
        </div>
      </div>

      <!-- Progress Steps -->
      <div class="mt-5 flex items-center gap-1 overflow-x-auto pb-1">
        <?php
        $steps = ['pending','confirmed','in_transit','delivered'];
        $stepLabels = ['Pending','Confirmed','In Transit','Delivered'];
        $stepIcons = ['schedule','check_circle','local_shipping','inventory'];
        $currentIdx = array_search($o['status'], $steps);
        foreach ($steps as $i => $step):
          $done = is_int($currentIdx) && $i <= $currentIdx && $o['status'] !== 'cancelled';
          $active = $step === $o['status'] && $o['status'] !== 'cancelled';
        ?>
        <div class="flex items-center gap-1 flex-shrink-0">
          <div class="flex flex-col items-center">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs
              <?= $done ? 'bg-primary text-white' : 'bg-surface-container text-on-surface-variant' ?>">
              <span class="material-symbols-outlined" style="font-size:.85rem"><?= $stepIcons[$i] ?></span>
            </div>
            <span class="text-[10px] mt-1 font-semibold <?= $active ? 'text-primary' : 'text-on-surface-variant' ?>">
              <?= $stepLabels[$i] ?>
            </span>
          </div>
          <?php if ($i < count($steps) - 1): ?>
          <div class="w-8 h-0.5 mb-4 <?= ($done && $i < $currentIdx) ? 'bg-primary' : 'bg-surface-container' ?>"></div>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
        <?php if ($o['status'] === 'cancelled'): ?>
        <span class="ml-2 text-xs text-error font-bold">Cancelled</span>
        <?php endif; ?>
      </div>

      <!-- Review / Reviewed indicator -->
      <?php if ($o['status'] === 'delivered'): ?>
      <div class="mt-4 flex items-center gap-3">
        <?php if (empty($reviewed[$o['id']])): ?>
        <a href="<?= APP_URL ?>/buyer/review?order_id=<?= (int)$o['id'] ?>"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-bold
                  bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 transition-colors">
          <span class="material-symbols-outlined" style="font-size:.95rem">star</span>Write a Review
        </a>
        <?php else: ?>
        <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold
                     bg-surface-container text-on-surface-variant">
          <span class="material-symbols-outlined" style="font-size:.95rem">check_circle</span>Reviewed
        </span>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<script>
var _currentFilter = 'all';

function filterOrders(status) {
  _currentFilter = status;
  document.querySelectorAll('.order-tab').forEach(b => {
    b.classList.toggle('bg-primary', b.dataset.filter === status);
    b.classList.toggle('text-white',  b.dataset.filter === status);
    b.classList.toggle('border-primary', b.dataset.filter === status);
  });
  var q = (document.getElementById('order-search').value || '').toLowerCase().trim();
  applyFilters(q, status);
}

function searchOrders(q) {
  applyFilters(q.toLowerCase().trim(), _currentFilter);
}

function applyFilters(q, status) {
  document.querySelectorAll('.order-card').forEach(function(c) {
    var matchStatus = (status === 'all' || c.dataset.status === status);
    var matchQuery  = !q || (c.dataset.search || '').indexOf(q) !== -1;
    c.style.display = (matchStatus && matchQuery) ? '' : 'none';
  });
}
</script>

</main>
</div>
</div>
<?php include BASE_PATH . '/app/views/partials/foot.php'; ?>
