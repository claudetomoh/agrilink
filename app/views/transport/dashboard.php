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
