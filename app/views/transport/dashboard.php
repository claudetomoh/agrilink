<?php include BASE_PATH . '/app/views/partials/head.php'; ?>
<div class="app-layout">
<?php include BASE_PATH . '/app/views/partials/sidebar.php'; ?>
<div class="main-wrap">
<?php include BASE_PATH . '/app/views/partials/topbar.php'; ?>
<main class="main-content">
<?php include BASE_PATH . '/app/views/partials/alerts.php'; ?>

<?php $user = (new UserModel())->findById(Session::userId()); ?>

<!-- Header -->
<div class="flex flex-col md:flex-row justify-between items-end gap-6 mb-10">
  <div>
    <h1 class="text-4xl font-extrabold tracking-tight text-primary mb-1">
      Welcome, <?= e($user['name'] ?? 'Transporter') ?>
    </h1>
    <p class="text-on-surface-variant font-medium"><?= e($user['region'] ?? 'Ghana') ?> &bull; Transport Provider</p>
  </div>
  <a href="<?= APP_URL ?>/transport/jobs"
     class="btn btn-primary flex items-center gap-2">
    <span class="material-symbols-outlined" style="font-size:1rem">search</span>Find Jobs
  </a>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-5 mb-10">
  <?php
  $cards = [
    ['label'=>'Active Deliveries', 'value'=>$stats['active'],    'icon'=>'local_shipping', 'color'=>'bg-primary-container text-white'],
    ['label'=>'Pending Jobs',      'value'=>$stats['pending'],   'icon'=>'schedule',       'color'=>'bg-secondary-container text-on-secondary-container'],
    ['label'=>'Completed',         'value'=>$stats['completed'], 'icon'=>'inventory',      'color'=>'bg-surface-container-low text-primary'],
    ['label'=>'Total Earnings',    'value'=>'₵'.number_format($stats['earnings'],0), 'icon'=>'payments', 'color'=>'bg-surface-container-low text-primary'],
  ];
  foreach ($cards as $card):
  ?>
  <div class="<?= $card['color'] ?> p-6 rounded-[1.5rem] flex flex-col gap-3">
    <div class="flex items-center justify-between">
      <span class="text-xs font-bold uppercase tracking-wider opacity-70"><?= $card['label'] ?></span>
      <span class="material-symbols-outlined opacity-60"><?= $card['icon'] ?></span>
    </div>
    <p class="text-3xl font-extrabold"><?= $card['value'] ?></p>
  </div>
  <?php endforeach; ?>
</div>

<!-- Fleet Map Section -->
<section class="mb-10">
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    <!-- Left: Live Logistics Metrics -->
    <div class="lg:col-span-3 flex flex-col gap-5">

      <!-- Fleet Uptime -->
      <div class="bg-surface-container-lowest rounded-2xl p-6 border-l-4 border-emerald-900 shadow-sm">
        <p class="text-xs text-on-surface-variant font-bold uppercase tracking-wider mb-1">Fleet Uptime</p>
        <div class="flex items-baseline gap-2">
          <span class="text-4xl font-extrabold text-primary">98.4%</span>
          <span class="text-green-600 text-sm font-bold flex items-center">
            <span class="material-symbols-outlined" style="font-size:.875rem">arrow_upward</span> 2%
          </span>
        </div>
        <p class="text-xs text-on-surface-variant mt-2">Real-time tracking active across your routes</p>
      </div>

      <!-- Next / Estimated Arrival -->
      <div class="bg-surface-container-lowest rounded-2xl p-6 border-l-4 border-secondary shadow-sm">
        <?php $nextDelivery = !empty($active) ? $active[0] : null; ?>
        <p class="text-xs text-on-surface-variant font-bold uppercase tracking-wider mb-1">Next Arrival</p>
        <div class="flex items-baseline gap-2">
          <?php if ($nextDelivery && !empty($nextDelivery['estimated_arrival'])): ?>
            <span class="text-4xl font-extrabold text-primary"><?= date('H:i', strtotime($nextDelivery['estimated_arrival'])) ?></span>
            <span class="text-on-surface-variant text-sm font-medium">GMT</span>
          <?php else: ?>
            <span class="text-4xl font-extrabold text-primary">—</span>
          <?php endif; ?>
        </div>
        <p class="text-xs text-on-surface-variant mt-2">
          <?= $nextDelivery ? e($nextDelivery['origin'] ?? 'Origin') . ' ➔ ' . e($nextDelivery['destination'] ?? 'Destination') : 'No active route' ?>
        </p>
      </div>

      <!-- Active Count Highlight -->
      <div class="bg-primary-container rounded-2xl p-6 overflow-hidden relative flex-1 flex flex-col justify-between" style="min-height:120px">
        <div class="relative z-10">
          <p class="text-emerald-200/70 text-sm font-medium mb-1">Active Deliveries</p>
          <h3 class="text-4xl font-bold text-white"><?= (int)($stats['active'] ?? 0) ?></h3>
          <p class="text-xs text-emerald-100/60 mt-3 leading-relaxed">
            Full route visibility &amp; on-time stewardship
          </p>
        </div>
        <div class="absolute -right-4 -bottom-4 opacity-10 pointer-events-none">
          <span class="material-symbols-outlined text-white" style="font-size:120px;font-variation-settings:'FILL' 1">local_shipping</span>
        </div>
      </div>

    </div>

    <!-- Right: Map Container -->
    <div class="lg:col-span-9 relative rounded-3xl overflow-hidden" style="min-height:420px;background:#112a1c;">

      <!-- OpenStreetMap embed centered on Ghana -->
      <iframe
        src="https://www.openstreetmap.org/export/embed.html?bbox=-3.3%2C4.5%2C1.2%2C11.2&amp;layer=mapnik"
        class="absolute inset-0 w-full h-full border-0"
        style="opacity:.65"
        title="Ghana Fleet Map"
        loading="lazy"
        sandbox="allow-scripts allow-same-origin"></iframe>

      <!-- Gradient overlay -->
      <div class="absolute inset-0 pointer-events-none" style="background:linear-gradient(to top, rgba(1,45,29,.45) 0%, transparent 60%)"></div>

      <!-- Glass Fleet Tracker Panel -->
      <div class="absolute top-5 left-5 glass-panel p-4 rounded-2xl shadow-md z-10 pointer-events-none" style="max-width:260px">
        <div class="flex items-center gap-2 mb-3">
          <span class="pulse-dot w-3 h-3 rounded-full bg-emerald-500 inline-block" style="min-width:.75rem;min-height:.75rem"></span>
          <span class="font-bold text-primary text-sm tracking-tight">Live Fleet Tracker</span>
        </div>
        <div class="space-y-3">
          <?php if (!empty($active)): ?>
            <?php foreach (array_slice($active, 0, 3) as $d): ?>
            <div class="flex items-center gap-3">
              <span class="material-symbols-outlined bg-emerald-100 text-emerald-900 rounded-lg p-1.5" style="font-size:1.125rem">local_shipping</span>
              <div>
                <p class="text-xs font-bold text-on-surface">DEL-<?= str_pad($d['id'], 4, '0', STR_PAD_LEFT) ?></p>
                <p style="font-size:.625rem" class="text-on-surface-variant"><?= e($d['origin'] ?? 'Origin') ?> → <?= e($d['destination'] ?? 'Destination') ?></p>
              </div>
            </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p class="text-xs text-on-surface-variant">No active routes</p>
          <?php endif; ?>
        </div>
      </div>

      <!-- Zoom/Layer controls (decorative) -->
      <div class="absolute top-5 right-5 flex flex-col gap-2 z-10 pointer-events-none">
        <div class="bg-white rounded-xl p-2.5 shadow-md text-primary flex items-center justify-center">
          <span class="material-symbols-outlined" style="font-size:1.125rem">add</span>
        </div>
        <div class="bg-white rounded-xl p-2.5 shadow-md text-primary flex items-center justify-center">
          <span class="material-symbols-outlined" style="font-size:1.125rem">remove</span>
        </div>
        <div class="bg-primary rounded-xl p-2.5 shadow-md text-white flex items-center justify-center">
          <span class="material-symbols-outlined" style="font-size:1.125rem">layers</span>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- Active Deliveries -->
<div class="mb-8">
  <div class="flex items-center justify-between mb-5">
    <h2 class="text-xl font-bold text-primary">Active Deliveries</h2>
    <a href="<?= APP_URL ?>/transport/jobs" class="text-sm font-bold text-primary hover:underline flex items-center gap-1">
      <span class="material-symbols-outlined" style="font-size:1rem">add</span>Accept New Job
    </a>
  </div>

  <?php if (empty($active)): ?>
  <div class="bg-surface-container-lowest rounded-[2rem] p-12 text-center border border-outline-variant/10">
    <span class="material-symbols-outlined" style="font-size:3.5rem;color:var(--outline)">local_shipping</span>
    <p class="mt-3 font-bold text-on-surface">No active deliveries</p>
    <a href="<?= APP_URL ?>/transport/jobs" class="btn btn-primary mt-4 inline-flex">Browse Available Jobs</a>
  </div>
  <?php else: ?>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <?php foreach ($active as $d): ?>
    <a href="<?= APP_URL ?>/transport/delivery?id=<?= $d['id'] ?>"
       class="bg-surface-container-lowest rounded-[1.5rem] p-6 border border-outline-variant/10 shadow-sm hover:shadow-md transition-shadow block">
      <div class="flex items-center justify-between mb-4">
        <span class="font-mono text-xs font-bold text-on-surface-variant">
          DEL-<?= str_pad($d['id'], 4, '0', STR_PAD_LEFT) ?>
        </span>
        <span class="badge badge-warning">In Transit</span>
      </div>
      <div class="flex items-center gap-3 mb-3">
        <div class="flex-1 text-center">
          <p class="text-xs text-on-surface-variant font-bold uppercase tracking-wider mb-1">From</p>
          <p class="font-bold text-primary text-sm"><?= e($d['origin']) ?></p>
        </div>
        <span class="material-symbols-outlined text-primary">east</span>
        <div class="flex-1 text-center">
          <p class="text-xs text-on-surface-variant font-bold uppercase tracking-wider mb-1">To</p>
          <p class="font-bold text-primary text-sm"><?= e($d['destination']) ?></p>
        </div>
      </div>
      <p class="text-xs text-on-surface-variant">
        Order #<?= str_pad($d['order_id'] ?? 0, 4, '0', STR_PAD_LEFT) ?> &bull;
        Scheduled <?= $d['estimated_arrival'] ? date('d M Y', strtotime($d['estimated_arrival'])) : 'Flexible' ?>
      </p>
    </a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<!-- Performance Score -->
<?php if (!empty($performance)): ?>
<div class="mt-8">
  <h2 class="text-xl font-bold text-primary mb-5 flex items-center gap-2">
    <span class="material-symbols-outlined">insights</span>Your Performance
  </h2>
  <div class="grid grid-cols-1 lg:grid-cols-4 gap-5">

    <!-- Score Circle -->
    <div class="lg:col-span-1 bg-primary-container rounded-[1.5rem] p-6 flex flex-col items-center justify-center text-white relative overflow-hidden">
      <div class="absolute inset-0 opacity-5 flex items-center justify-center">
        <span class="material-symbols-outlined" style="font-size:8rem">speed</span>
      </div>
      <p class="text-xs font-bold uppercase tracking-widest text-emerald-200 mb-2">Performance Score</p>
      <?php $score = (int)($performance['performance_score'] ?? 0); ?>
      <div class="relative flex items-center justify-center" style="width:100px;height:100px">
        <svg viewBox="0 0 100 100" style="width:100px;height:100px;transform:rotate(-90deg)">
          <circle cx="50" cy="50" r="42" fill="none" stroke="rgba(255,255,255,.15)" stroke-width="10"/>
          <circle cx="50" cy="50" r="42" fill="none" stroke="#a7f3d0" stroke-width="10"
                  stroke-dasharray="<?= round(2 * 3.14159 * 42 * $score / 100, 1) ?> 264"
                  stroke-linecap="round"/>
        </svg>
        <span class="absolute text-3xl font-extrabold text-white"><?= $score ?>%</span>
      </div>
      <p class="text-xs text-emerald-200 mt-3 font-medium text-center">
        <?php
          if ($score >= 85) echo 'Excellent — Top Performer';
          elseif ($score >= 70) echo 'Good — Keep it up!';
          elseif ($score >= 50) echo 'Average — Room to grow';
          else echo 'Needs Improvement';
        ?>
      </p>
    </div>

    <!-- Stats -->
    <div class="lg:col-span-3 grid grid-cols-1 sm:grid-cols-3 gap-5">
      <div class="bg-surface-container-lowest rounded-[1.5rem] p-6 border border-outline-variant/10 shadow-sm flex flex-col gap-2">
        <div class="flex items-center justify-between">
          <span class="text-xs font-bold uppercase tracking-wider text-on-surface-variant">Jobs Completed</span>
          <span class="material-symbols-outlined text-primary opacity-60">inventory</span>
        </div>
        <p class="text-4xl font-extrabold text-primary"><?= (int)($performance['completed'] ?? 0) ?></p>
        <p class="text-xs text-on-surface-variant">out of <?= (int)($performance['total_jobs'] ?? 0) ?> total jobs</p>
      </div>
      <div class="bg-surface-container-lowest rounded-[1.5rem] p-6 border border-outline-variant/10 shadow-sm flex flex-col gap-2">
        <div class="flex items-center justify-between">
          <span class="text-xs font-bold uppercase tracking-wider text-on-surface-variant">On-Time Rate</span>
          <span class="material-symbols-outlined text-green-600 opacity-60">schedule</span>
        </div>
        <p class="text-4xl font-extrabold text-primary"><?= (int)($performance['on_time_rate'] ?? 0) ?><span class="text-lg font-medium">%</span></p>
        <div class="mt-1 h-1.5 bg-surface-container rounded-full overflow-hidden">
          <div class="h-full bg-green-500 rounded-full" style="width:<?= min(100, (int)($performance['on_time_rate'] ?? 0)) ?>%"></div>
        </div>
      </div>
      <div class="bg-surface-container-lowest rounded-[1.5rem] p-6 border border-outline-variant/10 shadow-sm flex flex-col gap-2">
        <div class="flex items-center justify-between">
          <span class="text-xs font-bold uppercase tracking-wider text-on-surface-variant">Avg Delivery Time</span>
          <span class="material-symbols-outlined text-blue-500 opacity-60">timer</span>
        </div>
        <p class="text-4xl font-extrabold text-primary">
          <?= $performance['avg_hours'] ? number_format($performance['avg_hours'], 0) : '—' ?>
          <span class="text-lg font-medium"><?= $performance['avg_hours'] ? 'hrs' : '' ?></span>
        </p>
        <p class="text-xs text-on-surface-variant">average per job</p>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

</main>
</div>
</div>
<?php include BASE_PATH . '/app/views/partials/foot.php'; ?>
