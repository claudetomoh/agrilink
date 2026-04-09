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
    <h1 class="text-3xl font-extrabold tracking-tight text-primary">Edit Listing</h1>
    <p class="text-on-surface-variant text-sm mt-0.5">Update your produce details</p>
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
  <div class="lg:col-span-2">
    <form method="POST" action="<?= APP_URL ?>/farmer/listings/edit" enctype="multipart/form-data" novalidate>
      <input type="hidden" name="_token" value="<?= e(Session::csrfToken()) ?>">
      <input type="hidden" name="action" value="edit_listing">
      <input type="hidden" name="listing_id" value="<?= e($listing['id']) ?>">

      <div class="bg-surface-container-lowest rounded-[2rem] p-8 border border-outline-variant/10 shadow-sm mb-6">
        <h2 class="text-lg font-bold text-primary mb-6 flex items-center gap-2">
          <span class="material-symbols-outlined">eco</span>Produce Details
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <div class="form-group md:col-span-2">
            <label class="form-label" for="name">Produce Name <span class="text-error">*</span></label>
            <input type="text" id="name" name="name" class="form-input" required
                   value="<?= e($listing['name']) ?>">
          </div>
          <div class="form-group">
            <label class="form-label" for="category">Category</label>
            <select id="category" name="category" class="form-select">
              <?php foreach(['Tubers','Cereals','Legumes','Vegetables','Fruits','Cocoa & Cash Crops','Spices'] as $cat): ?>
              <option value="<?= e($cat) ?>" <?= $listing['category']===$cat?'selected':'' ?>><?= e($cat) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label" for="status">Status</label>
            <select id="status" name="status" class="form-select">
              <?php foreach(['available','sold','in_transit'] as $s): ?>
              <option value="<?= $s ?>" <?= $listing['status']===$s?'selected':'' ?>><?= ucfirst(str_replace('_',' ',$s)) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label" for="harvest_date">Harvest Date</label>
            <input type="date" id="harvest_date" name="harvest_date" class="form-input"
                   value="<?= e($listing['harvest_date'] ?? '') ?>">
          </div>
          <div class="form-group md:col-span-2">
            <label class="form-label" for="description">Description</label>
            <textarea id="description" name="description" class="form-input" rows="3"><?= e($listing['description'] ?? '') ?></textarea>
          </div>
          <div class="form-group md:col-span-2">
            <label class="form-label">Update Produce Image <span class="text-on-surface-variant font-normal">(optional, replaces current)</span></label>
            <?php if (!empty($listing['image'])): ?>
            <div class="mb-2 flex items-center gap-3">
              <img src="<?= APP_URL . e($listing['image']) ?>" alt="Current image"
                   style="width:80px;height:60px;object-fit:cover;border-radius:.75rem;border:1px solid var(--outline-variant)">
              <span class="text-xs text-on-surface-variant">Current image</span>
            </div>
            <?php endif; ?>
            <input type="file" name="produce_image" class="form-input" accept="image/jpeg,image/png,image/webp"
                   onchange="previewEditImage(this)">
            <div id="editImgWrap" style="display:none;margin-top:.5rem">
              <img id="editImgPreview" src="" alt="New image preview"
                   style="max-height:120px;border-radius:.75rem;border:1px solid var(--outline-variant)">
            </div>
          </div>
        </div>
      </div>

      <div class="bg-surface-container-lowest rounded-[2rem] p-8 border border-outline-variant/10 shadow-sm mb-6">
        <h2 class="text-lg font-bold text-primary mb-6 flex items-center gap-2">
          <span class="material-symbols-outlined">inventory_2</span>Quantity & Pricing
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
          <div class="form-group">
            <label class="form-label" for="quantity">Quantity <span class="text-error">*</span></label>
            <input type="number" id="quantity" name="quantity" class="form-input" required min="0" step="0.01"
                   value="<?= e($listing['quantity']) ?>">
          </div>
          <div class="form-group">
            <label class="form-label" for="unit">Unit</label>
            <select id="unit" name="unit" class="form-select">
              <?php foreach(['bag','kg','tonne','crate','bunch','sack','dozen'] as $u): ?>
              <option value="<?= e($u) ?>" <?= $listing['unit']===$u?'selected':'' ?>><?= ucfirst($u) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label" for="price_per_unit">Price per Unit (₵) <span class="text-error">*</span></label>
            <input type="number" id="price_per_unit" name="price_per_unit" class="form-input" required min="0" step="0.01"
                   value="<?= e($listing['price_per_unit']) ?>">
          </div>
        </div>
      </div>

      <div class="bg-surface-container-lowest rounded-[2rem] p-8 border border-outline-variant/10 shadow-sm mb-8">
        <h2 class="text-lg font-bold text-primary mb-6 flex items-center gap-2">
          <span class="material-symbols-outlined">location_on</span>Location
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <div class="form-group">
            <label class="form-label" for="region">Region</label>
            <select id="region" name="region" class="form-select">
              <?php foreach (GH_REGIONS as $r): ?>
              <option value="<?= e($r) ?>" <?= $listing['region']===$r?'selected':'' ?>><?= e($r) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label" for="town">Town</label>
            <input type="text" id="town" name="town" class="form-input"
                   value="<?= e($listing['town'] ?? '') ?>">
          </div>
        </div>
      </div>

      <div class="flex gap-4">
        <button type="submit" class="btn btn-primary btn-lg flex items-center gap-2">
          <span class="material-symbols-outlined" style="font-size:1rem">save</span>Save Changes
        </button>
        <a href="<?= APP_URL ?>/farmer/listings" class="btn btn-secondary btn-lg">Cancel</a>
      </div>
    </form>
  </div>

  <div class="space-y-5">
    <div class="bg-surface-container-low rounded-[1.5rem] p-6 border border-outline-variant/10">
      <h3 class="font-bold text-primary mb-4">Listing Actions</h3>
      <form method="POST" action="<?= APP_URL ?>/farmer/listings"
            onsubmit="return confirm('Are you sure you want to delete this listing?')">
        <input type="hidden" name="_token" value="<?= e(Session::csrfToken()) ?>">
        <input type="hidden" name="action" value="delete_listing">
        <input type="hidden" name="listing_id" value="<?= e($listing['id']) ?>">
        <button type="submit"
                class="w-full flex items-center justify-center gap-2 py-3 rounded-xl text-error bg-error-container/20 hover:bg-error-container/40 font-bold transition-colors">
          <span class="material-symbols-outlined" style="font-size:1rem">delete</span>Delete Listing
        </button>
      </form>
    </div>
    <div class="bg-surface-container-low rounded-[1.5rem] p-6 border border-outline-variant/10">
      <h3 class="font-bold text-primary mb-2">Created</h3>
      <p class="text-sm text-on-surface-variant"><?= date('d M Y, g:i a', strtotime($listing['created_at'])) ?></p>
    </div>
  </div>
</div>

<script>
function previewEditImage(input) {
  var wrap = document.getElementById('editImgWrap');
  var img  = document.getElementById('editImgPreview');
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function(e) { img.src = e.target.result; wrap.style.display = 'block'; };
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
