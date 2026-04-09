<?php include BASE_PATH . '/app/views/partials/head.php'; ?>
<div class="app-layout">
<?php include BASE_PATH . '/app/views/partials/sidebar.php'; ?>
<div class="main-wrap">
<?php include BASE_PATH . '/app/views/partials/topbar.php'; ?>
<main class="main-content">
<?php include BASE_PATH . '/app/views/partials/alerts.php'; ?>

<div class="flex items-center justify-between mb-8">
  <div>
    <h1 class="text-3xl font-extrabold tracking-tight text-primary">Available Jobs</h1>
    <p class="text-on-surface-variant text-sm mt-1"><?= count($unassigned) ?> jobs waiting for a transporter</p>
  </div>
  <a href="<?= APP_URL ?>/transport/dashboard" class="btn btn-secondary flex items-center gap-2">
    <span class="material-symbols-outlined" style="font-size:1rem">arrow_back</span>Dashboard
  </a>
</div>

<?php if (empty($unassigned)): ?>
<div class="bg-surface-container-lowest rounded-[2rem] p-16 text-center border border-outline-variant/10">
  <span class="material-symbols-outlined" style="font-size:4rem;color:var(--outline)">local_shipping</span>
  <p class="mt-4 text-xl font-bold text-primary">No jobs available right now</p>
  <p class="text-on-surface-variant text-sm">Check back later for new delivery requests.</p>
</div>
<?php else: ?>
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
  <?php foreach ($unassigned as $job): ?>
  <div class="bg-surface-container-lowest rounded-[1.5rem] border border-outline-variant/10 shadow-sm overflow-hidden flex flex-col">
    <!-- Header strip -->
    <div class="bg-primary-container/20 px-6 py-4 flex items-center justify-between">
      <span class="font-mono text-xs font-bold text-primary">DEL-<?= str_pad($job['id'],4,'0',STR_PAD_LEFT) ?></span>
      <span class="badge badge-success">Open</span>
    </div>
    <div class="p-6 flex flex-col flex-1">
      <!-- Route -->
      <div class="flex items-center gap-3 mb-5">
        <div class="flex-1">
          <p class="text-xs text-on-surface-variant uppercase tracking-wider font-bold mb-0.5">Pickup</p>
          <p class="font-extrabold text-primary"><?= e($job['origin']) ?></p>
        </div>
        <div class="flex flex-col items-center gap-1">
          <span class="material-symbols-outlined text-primary">east</span>
          <?php if ($job['distance_km'] ?? false): ?>
          <span class="text-xs text-on-surface-variant"><?= e($job['distance_km']) ?>km</span>
          <?php endif; ?>
        </div>
        <div class="flex-1 text-right">
          <p class="text-xs text-on-surface-variant uppercase tracking-wider font-bold mb-0.5">Delivery</p>
          <p class="font-extrabold text-primary"><?= e($job['destination']) ?></p>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-3 mb-5">
        <div class="bg-surface-container-low rounded-xl p-3 text-center">
          <p class="text-xs text-on-surface-variant font-bold uppercase">Scheduled</p>
          <p class="text-sm font-bold mt-1"><?= $job['estimated_arrival'] ? date('d M Y', strtotime($job['estimated_arrival'])) : 'Flexible' ?></p>
        </div>
        <div class="bg-surface-container-low rounded-xl p-3 text-center">
          <p class="text-xs text-on-surface-variant font-bold uppercase">Produce</p>
          <p class="text-sm font-bold text-primary mt-1"><?= e($job['produce_name'] ?? '—') ?></p>
        </div>
      </div>

      <?php if ($job['notes'] ?? null): ?>
      <p class="text-xs text-on-surface-variant bg-surface-container-low rounded-xl p-3 mb-4 line-clamp-2">
        <?= e($job['notes']) ?>
      </p>
      <?php endif; ?>

      <div class="mt-auto">
        <form method="POST" action="<?= APP_URL ?>/transport/jobs">
          <input type="hidden" name="_token" value="<?= e(Session::csrfToken()) ?>">
          <input type="hidden" name="action" value="accept_job">
          <input type="hidden" name="delivery_id" value="<?= $job['id'] ?>">
          <button type="submit" class="btn btn-primary btn-full">
            <span class="material-symbols-outlined" style="font-size:1rem">check_circle</span>Accept Job
          </button>
        </form>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

</main>
</div>
</div>
<?php include BASE_PATH . '/app/views/partials/foot.php'; ?>
