<?php include BASE_PATH . '/app/views/partials/head.php'; ?>
<div class="app-layout">
<?php include BASE_PATH . '/app/views/partials/sidebar.php'; ?>
<div class="main-wrap">
<?php include BASE_PATH . '/app/views/partials/topbar.php'; ?>
<main class="main-content">
<?php include BASE_PATH . '/app/views/partials/alerts.php'; ?>

<!-- Breadcrumb -->
<nav class="flex items-center gap-2 text-sm text-on-surface-variant mb-6">
  <a href="<?= APP_URL ?>/buyer/marketplace" class="hover:text-primary transition-colors">Marketplace</a>
  <span class="material-symbols-outlined" style="font-size:.9rem">chevron_right</span>
  <span class="font-semibold text-primary"><?= e($listing['name']) ?></span>
</nav>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

  <!-- Main Details -->
  <div class="lg:col-span-2">
    <!-- Produce Card -->
    <div class="bg-surface-container-lowest rounded-[2rem] overflow-hidden border border-outline-variant/10 shadow-sm mb-6">
      <div class="h-80 relative overflow-hidden">
        <img src="<?= !empty($listing['image']) ? APP_URL . e($listing['image']) : Helpers::produceImage($listing['name'], $listing['category'] ?? '') ?>"
             alt="<?= e($listing['name']) ?>"
             class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
        <div class="absolute bottom-5 left-8">
          <span class="text-white text-2xl font-extrabold drop-shadow-lg"><?= e($listing['name']) ?></span>
        </div>
      </div>
      <div class="p-8">
        <div class="flex items-start justify-between mb-4">
          <div>
            <h1 class="text-3xl font-extrabold text-primary"><?= e($listing['name']) ?></h1>
            <p class="text-on-surface-variant mt-1 flex items-center gap-1">
              <span class="material-symbols-outlined" style="font-size:1rem">person</span>
              <?= e($listing['farmer_name'] ?? 'Farmer') ?>
              &bull;
              <span class="material-symbols-outlined" style="font-size:1rem">location_on</span>
              <?= e($listing['region']) ?><?= $listing['town'] ? ', ' . e($listing['town']) : '' ?>
            </p>
          </div>
          <span class="badge badge-success text-sm">Available</span>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
          <div class="bg-surface-container-low rounded-xl p-4 text-center">
            <p class="text-xs text-on-surface-variant uppercase tracking-wider font-bold mb-1">Price</p>
            <p class="text-2xl font-extrabold text-primary">₵<?= number_format($listing['price_per_unit'], 0) ?></p>
            <p class="text-xs text-on-surface-variant">per <?= e($listing['unit']) ?></p>
          </div>
          <div class="bg-surface-container-low rounded-xl p-4 text-center">
            <p class="text-xs text-on-surface-variant uppercase tracking-wider font-bold mb-1">Available</p>
            <p class="text-2xl font-extrabold text-primary"><?= e($listing['quantity']) ?></p>
            <p class="text-xs text-on-surface-variant"><?= e($listing['unit']) ?></p>
          </div>
          <div class="bg-surface-container-low rounded-xl p-4 text-center">
            <p class="text-xs text-on-surface-variant uppercase tracking-wider font-bold mb-1">Category</p>
            <p class="text-sm font-bold text-primary mt-1"><?= e($listing['category']) ?></p>
          </div>
          <div class="bg-surface-container-low rounded-xl p-4 text-center">
            <p class="text-xs text-on-surface-variant uppercase tracking-wider font-bold mb-1">Harvested</p>
            <p class="text-sm font-bold text-primary mt-1">
              <?= $listing['harvest_date'] ? date('d M Y', strtotime($listing['harvest_date'])) : 'Recent' ?>
            </p>
          </div>
        </div>

        <?php if ($listing['description']): ?>
        <div class="border-t border-outline-variant/10 pt-5">
          <h3 class="font-bold text-primary mb-2">Description</h3>
          <p class="text-on-surface-variant text-sm leading-relaxed"><?= e($listing['description']) ?></p>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Recent Bids -->
    <?php if (!empty($bids)): ?>
    <div class="bg-surface-container-lowest rounded-[2rem] p-6 border border-outline-variant/10 shadow-sm">
      <h3 class="font-bold text-primary mb-4">Recent Bids (<?= count($bids) ?>)</h3>
      <div class="space-y-3">
        <?php foreach (array_slice($bids, 0, 5) as $bid): ?>
        <div class="flex items-center gap-4 py-3 border-b border-outline-variant/10 last:border-0">
          <div class="w-9 h-9 rounded-full bg-primary flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
            <?= strtoupper(substr($bid['buyer_name'] ?? 'B', 0, 1)) ?>
          </div>
          <div class="flex-1">
            <p class="text-sm font-bold"><?= e($bid['buyer_name'] ?? 'Anonymous') ?></p>
            <p class="text-xs text-on-surface-variant"><?= e($bid['quantity']) ?> <?= e($listing['unit']) ?> &bull; <?= date('d M', strtotime($bid['created_at'])) ?></p>
          </div>
          <p class="font-extrabold text-primary">₵<?= number_format($bid['bid_price'], 0) ?></p>
          <?php
            $bs=['pending'=>'badge-warning','accepted'=>'badge-success','rejected'=>'badge-error'][$bid['status']] ?? 'badge-info';
          ?>
          <span class="badge <?= $bs ?>"><?= ucfirst($bid['status']) ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <!-- Order / Bid Sidebar -->
  <div class="lg:col-span-1 space-y-5">

    <!-- Direct Buy -->
    <div class="bg-primary-container rounded-[1.5rem] p-6">
      <h3 class="font-bold text-white mb-4 flex items-center gap-2">
        <span class="material-symbols-outlined text-emerald-300">shopping_cart</span>Place Order
      </h3>
      <form method="POST" action="<?= APP_URL ?>/buyer/product">
        <input type="hidden" name="_token" value="<?= e(Session::csrfToken()) ?>">
        <input type="hidden" name="action" value="place_order">
        <input type="hidden" name="listing_id" value="<?= e($listing['id']) ?>">
        <div class="form-group mb-4">
          <label class="form-label text-emerald-200 text-xs">Quantity (<?= e($listing['unit']) ?>)</label>
          <input type="number" name="quantity" class="form-input bg-white/20 text-white placeholder-emerald-200/60 border-white/20"
                 placeholder="e.g. 10" min="1" max="<?= e($listing['quantity']) ?>" step="1" required
                 id="order_qty">
        </div>
        <div class="bg-white/10 rounded-xl p-3 mb-4 text-sm text-emerald-100">
          Total: <strong id="order_total" class="text-white text-base">₵0</strong>
        </div>
        <button type="submit" class="btn bg-white text-primary font-extrabold btn-full hover:bg-emerald-50">
          Order Now
        </button>
      </form>
    </div>

    <!-- Bid -->
    <div class="bg-surface-container-lowest rounded-[1.5rem] p-6 border border-outline-variant/10 shadow-sm">
      <h3 class="font-bold text-primary mb-4 flex items-center gap-2">
        <span class="material-symbols-outlined">gavel</span>Place Bid
      </h3>
      <form method="POST" action="<?= APP_URL ?>/buyer/product">
        <input type="hidden" name="_token" value="<?= e(Session::csrfToken()) ?>">
        <input type="hidden" name="action" value="place_bid">
        <input type="hidden" name="listing_id" value="<?= e($listing['id']) ?>">
        <div class="form-group">
          <label class="form-label text-xs">Your Bid per <?= e($listing['unit']) ?> (₵)</label>
          <input type="number" name="bid_amount" class="form-input text-sm" placeholder="e.g. 140" min="1" step="0.01" required>
        </div>
        <div class="form-group">
          <label class="form-label text-xs">Quantity</label>
          <input type="number" name="quantity" class="form-input text-sm" placeholder="e.g. 20" min="1" step="1" required>
        </div>
        <div class="form-group">
          <label class="form-label text-xs">Message to Farmer</label>
          <textarea name="message" class="form-input text-sm" rows="2" placeholder="Optional note…"></textarea>
        </div>
        <button type="submit" class="btn btn-secondary btn-full text-sm">Submit Bid</button>
      </form>
    </div>

    <!-- Farmer Details -->
    <div class="bg-surface-container-low rounded-[1.5rem] p-5 border border-outline-variant/10">
      <h4 class="font-bold text-primary mb-3 text-sm">Seller Information</h4>
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-white font-bold text-sm">
          <?= strtoupper(substr($listing['farmer_name'] ?? 'F', 0, 1)) ?>
        </div>
        <div>
          <p class="font-bold text-sm"><?= e($listing['farmer_name'] ?? 'Farmer') ?></p>
          <p class="text-xs text-on-surface-variant"><?= e($listing['region']) ?></p>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
const pricePerUnit = <?= (float)$listing['price_per_unit'] ?>;
document.getElementById('order_qty')?.addEventListener('input', function() {
  const qty = parseFloat(this.value) || 0;
  document.getElementById('order_total').textContent = '₵' + (qty * pricePerUnit).toLocaleString('en-GH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
});
</script>
</main>
</div>
</div>
<?php include BASE_PATH . '/app/views/partials/foot.php'; ?>
