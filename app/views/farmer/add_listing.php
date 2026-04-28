<?php include BASE_PATH . '/app/views/partials/head.php'; ?>
<div class="app-layout">
<?php include BASE_PATH . '/app/views/partials/sidebar.php'; ?>
<div class="main-wrap">
<?php include BASE_PATH . '/app/views/partials/topbar.php'; ?>
<main class="main-content">
<?php include BASE_PATH . '/app/views/partials/alerts.php'; ?>

<div class="flex items-center gap-3 mb-8">
  <a href="<?= APP_URL ?>/farmer/listings" class="p-2 rounded-xl hover:bg-surface-container transition-colors text-on-surface-variant">
    <span class="material-symbols-outlined">arrow_back</span>
  </a>
  <div>
    <h1 class="text-3xl font-extrabold tracking-tight text-primary">Add New Listing</h1>
    <p class="text-on-surface-variant text-sm mt-0.5">List your produce on the AgriLink marketplace</p>
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
  <!-- Form -->
  <div class="lg:col-span-2">
    <form method="POST" action="<?= APP_URL ?>/farmer/listings/add" enctype="multipart/form-data" novalidate>
      <input type="hidden" name="_token" value="<?= e(Session::csrfToken()) ?>">
      <input type="hidden" name="action" value="add_listing">

      <div class="bg-surface-container-lowest rounded-[2rem] p-8 border border-outline-variant/10 shadow-sm mb-6">
        <h2 class="text-lg font-bold text-primary mb-6 flex items-center gap-2">
          <span class="material-symbols-outlined text-primary">eco</span>Produce Details
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <div class="form-group md:col-span-2">
            <label class="form-label" for="name">Produce Name <span class="text-error">*</span></label>
            <input type="text" id="name" name="name" class="form-input" required
                   placeholder="e.g. White Yam, Cassava, Premium Cocoa"
                   value="<?= e($old['name'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label class="form-label" for="category">Category <span class="text-error">*</span></label>
            <select id="category" name="category" class="form-select" required>
              <option value="">Select category</option>
              <?php foreach(['Tubers','Cereals','Legumes','Vegetables','Fruits','Cocoa & Cash Crops','Spices'] as $cat): ?>
              <option value="<?= e($cat) ?>" <?= ($old['category']??'')===$cat?'selected':'' ?>><?= e($cat) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label" for="harvest_date">Harvest Date</label>
            <input type="date" id="harvest_date" name="harvest_date" class="form-input"
                   value="<?= e($old['harvest_date'] ?? '') ?>">
          </div>
          <div class="form-group md:col-span-2">
            <label class="form-label" for="description">Description</label>
            <textarea id="description" name="description" class="form-input" rows="3"
                      placeholder="Describe quality, grade, packaging…"><?= e($old['description'] ?? '') ?></textarea>
          </div>
          <div class="form-group md:col-span-2">
            <label class="form-label" for="produce_image">Produce Image <span class="text-on-surface-variant font-normal">(optional, max 2MB)</span></label>
            <div class="relative">
              <input type="file" id="produce_image" name="produce_image"
                     class="form-input" accept="image/jpeg,image/png,image/webp"
                     onchange="previewImage(this)">
            </div>
            <div id="imgPreviewWrap" style="display:none;margin-top:.5rem">
              <img id="imgPreview" src="" alt="Preview"
                   style="max-height:160px;border-radius:1rem;border:1px solid var(--outline-variant)">
            </div>
            <p class="text-xs text-on-surface-variant mt-1">Accepted: JPG, PNG, WebP. Good photos attract more buyers.</p>
          </div>
        </div>
      </div>

      <div class="bg-surface-container-lowest rounded-[2rem] p-8 border border-outline-variant/10 shadow-sm mb-6">
        <h2 class="text-lg font-bold text-primary mb-6 flex items-center gap-2">
          <span class="material-symbols-outlined text-primary">inventory_2</span>Quantity & Pricing
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
          <div class="form-group">
            <label class="form-label" for="quantity">Quantity <span class="text-error">*</span></label>
            <input type="number" id="quantity" name="quantity" class="form-input" required min="0" step="0.01"
                   placeholder="e.g. 50"
                   value="<?= e($old['quantity'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label class="form-label" for="unit">Unit</label>
            <select id="unit" name="unit" class="form-select">
              <?php foreach(['bag','kg','tonne','crate','bunch','sack','dozen'] as $u): ?>
              <option value="<?= e($u) ?>" <?= ($old['unit']??'bag')===$u?'selected':'' ?>><?= ucfirst($u) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label" for="price_per_unit">Price per Unit (₵) <span class="text-error">*</span></label>
            <input type="number" id="price_per_unit" name="price_per_unit" class="form-input" required min="0" step="0.01"
                   placeholder="e.g. 150"
                   value="<?= e($old['price_per_unit'] ?? '') ?>">
          </div>
        </div>
      </div>

      <div class="bg-surface-container-lowest rounded-[2rem] p-8 border border-outline-variant/10 shadow-sm mb-8">
        <h2 class="text-lg font-bold text-primary mb-6 flex items-center gap-2">
          <span class="material-symbols-outlined text-primary">location_on</span>Location
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <div class="form-group">
            <label class="form-label" for="region">Region <span class="text-error">*</span></label>
            <select id="region" name="region" class="form-select" required>
              <option value="">Select region</option>
              <?php foreach (GH_REGIONS as $r): ?>
              <option value="<?= e($r) ?>" <?= ($old['region']??'')===$r?'selected':'' ?>><?= e($r) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label" for="town">Town / City</label>
            <input type="text" id="town" name="town" class="form-input"
                   placeholder="e.g. Kumasi"
                   value="<?= e($old['town'] ?? '') ?>">
          </div>
        </div>
      </div>

      <div class="flex gap-4">
        <button type="submit" class="btn btn-primary btn-lg flex items-center gap-2">
          <span class="material-symbols-outlined" style="font-size:1rem">check_circle</span>Publish Listing
        </button>
        <a href="<?= APP_URL ?>/farmer/listings" class="btn btn-secondary btn-lg">Cancel</a>
      </div>
    </form>
  </div>

  <!-- Info Sidebar -->
  <div class="space-y-5">
    <div class="bg-primary rounded-[1.5rem] p-6">
      <h3 class="font-bold text-white mb-3 flex items-center gap-2">
        <span class="material-symbols-outlined text-emerald-300">tips_and_updates</span> Listing Tips
      </h3>
      <ul class="text-sm text-emerald-100/90 space-y-2">
        <li class="flex items-start gap-2"><span class="material-symbols-outlined text-emerald-400" style="font-size:.9rem;margin-top:.1rem">check</span>Use clear, specific produce names</li>
        <li class="flex items-start gap-2"><span class="material-symbols-outlined text-emerald-400" style="font-size:.9rem;margin-top:.1rem">check</span>Mention grade (A, B) and packaging</li>
        <li class="flex items-start gap-2"><span class="material-symbols-outlined text-emerald-400" style="font-size:.9rem;margin-top:.1rem">check</span>Set competitive ₵ price per unit</li>
        <li class="flex items-start gap-2"><span class="material-symbols-outlined text-emerald-400" style="font-size:.9rem;margin-top:.1rem">check</span>Buyers prefer verified harvest dates</li>
      </ul>
    </div>
    <div class="bg-surface-container-low rounded-[1.5rem] p-6 border border-outline-variant/10">
      <h3 class="font-bold text-primary mb-3">Popular Categories</h3>
      <div class="flex flex-wrap gap-2">
        <?php foreach(['White Yam','Cassava','Cocoa','Maize','Tomatoes','Plantain','Pepper'] as $p): ?>
        <button type="button" onclick="document.getElementById('name').value='<?= $p ?>'"
                class="text-xs px-3 py-1 rounded-full bg-surface-container border border-outline-variant/20 text-on-surface-variant hover:border-primary hover:text-primary transition-colors">
          <?= $p ?>
        </button>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>

<script>
function previewImage(input) {
  var wrap = document.getElementById('imgPreviewWrap');
  var img  = document.getElementById('imgPreview');
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function(e) {
      img.src = e.target.result;
      wrap.style.display = 'block';
    };
    reader.readAsDataURL(input.files[0]);
  } else {
    wrap.style.display = 'none';
  }
}
</script>

</main>
</div>
</div>
<?php include BASE_PATH . '/app/views/partials/foot.php'; ?>
