<?php include BASE_PATH . '/app/views/partials/head.php'; ?>
<div class="app-layout">
<?php include BASE_PATH . '/app/views/partials/sidebar.php'; ?>
<div class="main-wrap">
<?php include BASE_PATH . '/app/views/partials/topbar.php'; ?>
<main class="main-content">
<?php include BASE_PATH . '/app/views/partials/alerts.php'; ?>

<div class="flex items-center gap-3 mb-8">
  <a href="<?= APP_URL ?>/buyer/orders"
     class="p-2 rounded-xl hover:bg-surface-container transition-colors text-on-surface-variant">
    <span class="material-symbols-outlined">arrow_back</span>
  </a>
  <div>
    <h1 class="text-3xl font-extrabold tracking-tight text-primary">Write a Review</h1>
    <p class="text-on-surface-variant text-sm mt-0.5">Share your experience to help other buyers</p>
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 max-w-4xl">

  <!-- Review Form -->
  <div class="lg:col-span-2">

    <!-- Order Summary Card -->
    <div class="bg-surface-container-lowest rounded-[1.5rem] p-6 border border-outline-variant/10 shadow-sm mb-6">
      <h2 class="text-sm font-bold uppercase tracking-widest text-on-surface-variant mb-4">Order Summary</h2>
      <div class="flex items-center gap-4">
        <div class="w-14 h-14 rounded-2xl bg-surface-container flex items-center justify-center flex-shrink-0">
          <span class="material-symbols-outlined text-primary" style="font-size:2rem">yard</span>
        </div>
        <div class="flex-1">
          <h3 class="font-extrabold text-primary text-lg leading-tight"><?= e($order['produce_name'] ?? 'Produce') ?></h3>
          <p class="text-sm text-on-surface-variant mt-0.5">
            <?= e($order['quantity'] ?? '') ?> <?= e($order['unit'] ?? '') ?> &bull;
            Sold by <strong class="text-on-surface"><?= e($order['farmer_name'] ?? 'Farmer') ?></strong>
          </p>
        </div>
        <div class="text-right flex-shrink-0">
          <p class="text-2xl font-extrabold text-primary">₵<?= number_format($order['total_price'] ?? 0, 0) ?></p>
          <p class="text-xs text-on-surface-variant badge badge-success mt-1">Delivered</p>
        </div>
      </div>
    </div>

    <!-- Review Form -->
    <form method="POST" action="<?= APP_URL ?>/buyer/orders" class="bg-surface-container-lowest rounded-[1.5rem] p-8 border border-outline-variant/10 shadow-sm">
      <input type="hidden" name="_token"   value="<?= e(Session::csrfToken()) ?>">
      <input type="hidden" name="action"   value="submit_review">
      <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
      <input type="hidden" name="rating"   id="rating-input" value="5">

      <!-- Star Rating Widget -->
      <div class="mb-8">
        <label class="form-label text-base font-bold mb-4 block">
          Your Rating for <?= e($order['farmer_name'] ?? 'this farmer') ?> <span class="text-error">*</span>
        </label>
        <div class="flex items-center gap-2" id="starWidget" role="group" aria-label="Star rating">
          <?php for ($i = 1; $i <= 5; $i++): ?>
          <button type="button" class="star-btn" data-value="<?= $i ?>"
                  onclick="setRating(<?= $i ?>)"
                  onmouseover="hoverRating(<?= $i ?>)"
                  onmouseout="resetHover()"
                  style="background:none;border:none;cursor:pointer;padding:.15rem;
                         transition:transform .1s;line-height:1"
                  aria-label="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">
            <span class="material-symbols-outlined"
                  id="star-<?= $i ?>"
                  style="font-size:2.5rem;color:<?= $i <= 5 ? '#f59e0b' : '#d1d5db' ?>;
                         transition:color .15s">
              star
            </span>
          </button>
          <?php endfor; ?>
        </div>
        <p id="rating-label" class="text-sm font-bold text-amber-600 mt-2">Excellent — 5 Stars</p>
      </div>

      <!-- Comment -->
      <div class="form-group mb-8">
        <label class="form-label" for="comment">Your Review <span class="text-error">*</span></label>
        <textarea id="comment" name="comment" class="form-input" rows="5" required
                  placeholder="How was the quality? Was the farmer responsive? Would you order again?"
                  minlength="10" maxlength="1000"><?= e($_POST['comment'] ?? '') ?></textarea>
        <p class="text-xs text-on-surface-variant mt-1">Minimum 10 characters. Be honest and specific.</p>
      </div>

      <div class="flex gap-4">
        <button type="submit" class="btn btn-primary btn-lg flex items-center gap-2">
          <span class="material-symbols-outlined" style="font-size:1rem">send</span>Submit Review
        </button>
        <a href="<?= APP_URL ?>/buyer/orders" class="btn btn-secondary btn-lg">Cancel</a>
      </div>
    </form>
  </div>

  <!-- Guide Sidebar -->
  <div class="space-y-5">
    <div class="bg-primary-container rounded-[1.5rem] p-6">
      <h3 class="font-bold text-white mb-3 flex items-center gap-2">
        <span class="material-symbols-outlined text-emerald-300">tips_and_updates</span>Review Guide
      </h3>
      <ul class="text-sm text-emerald-100/90 space-y-2.5">
        <li class="flex items-start gap-2">
          <span class="material-symbols-outlined text-emerald-400" style="font-size:.9rem;margin-top:.15rem">check</span>
          Rate based on produce quality
        </li>
        <li class="flex items-start gap-2">
          <span class="material-symbols-outlined text-emerald-400" style="font-size:.9rem;margin-top:.15rem">check</span>
          Mention freshness and packaging
        </li>
        <li class="flex items-start gap-2">
          <span class="material-symbols-outlined text-emerald-400" style="font-size:.9rem;margin-top:.15rem">check</span>
          Note if quantity was as listed
        </li>
        <li class="flex items-start gap-2">
          <span class="material-symbols-outlined text-emerald-400" style="font-size:.9rem;margin-top:.15rem">check</span>
          Comment on communication
        </li>
      </ul>
    </div>

    <div class="bg-surface-container-low rounded-[1.5rem] p-6 border border-outline-variant/10">
      <h3 class="font-bold text-primary mb-3">Rating Scale</h3>
      <?php foreach ([5=>'Excellent',4=>'Very Good',3=>'Good',2=>'Fair',1=>'Poor'] as $s=>$label): ?>
      <div class="flex items-center gap-2 mb-2 text-sm">
        <div class="flex gap-0.5">
          <?php for($i=1;$i<=5;$i++): ?>
          <span class="material-symbols-outlined" style="font-size:.85rem;color:<?=$i<=$s?'#f59e0b':'#d1d5db'?>">star</span>
          <?php endfor; ?>
        </div>
        <span class="text-on-surface-variant font-medium"><?= $label ?></span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

</div>

<script>
var currentRating = 5;
var labels = {1:'Poor — 1 Star',2:'Fair — 2 Stars',3:'Good — 3 Stars',4:'Very Good — 4 Stars',5:'Excellent — 5 Stars'};

function setRating(val) {
  currentRating = val;
  document.getElementById('rating-input').value = val;
  updateStars(val);
  document.getElementById('rating-label').textContent = labels[val];
}
function hoverRating(val) { updateStars(val); }
function resetHover() { updateStars(currentRating); }
function updateStars(val) {
  for (var i = 1; i <= 5; i++) {
    var s = document.getElementById('star-' + i);
    s.style.color = i <= val ? '#f59e0b' : '#d1d5db';
    s.closest('.star-btn').style.transform = i <= val ? 'scale(1.15)' : 'scale(1)';
  }
}
// init
updateStars(5);
</script>

</main>
</div>
</div>
<?php include BASE_PATH . '/app/views/partials/foot.php'; ?>
