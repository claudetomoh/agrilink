<?php include BASE_PATH . '/app/views/partials/head.php'; ?>
<div class="app-layout">
<?php include BASE_PATH . '/app/views/partials/sidebar.php'; ?>
<div class="main-wrap">
<?php include BASE_PATH . '/app/views/partials/topbar.php'; ?>
<main class="main-content">
<?php include BASE_PATH . '/app/views/partials/alerts.php'; ?>

<!-- Breadcrumb -->
<nav class="flex items-center gap-2 text-sm text-on-surface-variant mb-6">
  <a href="<?= APP_URL ?>/transport/dashboard" class="hover:text-primary">Dashboard</a>
  <span class="material-symbols-outlined" style="font-size:.9rem">chevron_right</span>
  <span class="font-semibold text-primary">Delivery #<?= str_pad($delivery['id'], 4, '0', STR_PAD_LEFT) ?></span>
</nav>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

  <!-- Timeline + Route -->
  <div class="lg:col-span-2 space-y-6">

    <!-- Route Card -->
    <div class="bg-primary-container rounded-[2rem] p-8 text-white relative overflow-hidden">
      <div class="absolute top-0 right-0 p-6 opacity-10">
        <span class="material-symbols-outlined" style="font-size:6rem">local_shipping</span>
      </div>
      <p class="text-emerald-200/70 text-xs font-bold uppercase tracking-wider mb-4">Active Route</p>
      <div class="flex items-center gap-6">
        <div>
          <p class="text-emerald-200/70 text-xs uppercase tracking-wider font-bold">From</p>
          <p class="text-2xl font-extrabold mt-1"><?= e($delivery['origin']) ?></p>
        </div>
        <div class="flex-1 flex items-center gap-2">
          <div class="flex-1 h-0.5 bg-white/20"></div>
          <span class="material-symbols-outlined">local_shipping</span>
          <div class="flex-1 h-0.5 bg-white/20"></div>
        </div>
        <div class="text-right">
          <p class="text-emerald-200/70 text-xs uppercase tracking-wider font-bold">To</p>
          <p class="text-2xl font-extrabold mt-1"><?= e($delivery['destination']) ?></p>
        </div>
      </div>
      <?php if ($delivery['distance_km'] ?? false): ?>
      <p class="text-emerald-200/70 text-sm mt-4">Distance: <?= e($delivery['distance_km']) ?> km</p>
      <?php endif; ?>
    </div>

    <!-- Status Timeline -->
    <div class="bg-surface-container-lowest rounded-[2rem] p-8 border border-outline-variant/10 shadow-sm">
      <h3 class="text-lg font-bold text-primary mb-6">Delivery Timeline</h3>
      <?php
      $steps = [
        ['key'=>'pending',    'label'=>'Job Created',    'icon'=>'schedule',       'desc'=>'Delivery request raised'],
        ['key'=>'assigned',   'label'=>'Driver Assigned', 'icon'=>'person_pin',    'desc'=>'You accepted this job'],
        ['key'=>'in_transit', 'label'=>'In Transit',     'icon'=>'local_shipping', 'desc'=>'Goods picked up and en route'],
        ['key'=>'delivered',  'label'=>'Delivered',       'icon'=>'inventory',      'desc'=>'Goods received at destination'],
      ];
      $statusOrder = ['pending'=>0,'assigned'=>1,'in_transit'=>2,'delivered'=>3];
      $current = $statusOrder[$delivery['status']] ?? 0;
      ?>
      <div class="relative">
        <?php foreach ($steps as $i => $step):
          $done   = $i <= $current;
          $active = $i === $current;
        ?>
        <div class="flex gap-5 <?= $i < count($steps)-1 ? 'pb-8' : '' ?> relative">
          <!-- Connector Line -->
          <?php if ($i < count($steps)-1): ?>
          <div class="absolute left-[1.4rem] top-10 w-0.5 bottom-0 <?= $done ? 'bg-primary' : 'bg-surface-container' ?>"></div>
          <?php endif; ?>
          <!-- Step Dot -->
          <div class="relative flex-shrink-0 w-11 h-11 rounded-full flex items-center justify-center
            <?= $done ? 'bg-primary' : 'bg-surface-container' ?>">
            <span class="material-symbols-outlined text-sm <?= $done ? 'text-white' : 'text-on-surface-variant' ?>">
              <?= $step['icon'] ?>
            </span>
            <?php if ($active && $delivery['status'] !== 'delivered'): ?>
            <span class="absolute -top-0.5 -right-0.5 w-3 h-3 rounded-full bg-secondary-container border-2 border-white"></span>
            <?php endif; ?>
          </div>
          <!-- Step Content -->
          <div class="flex-1 pt-1.5">
            <p class="font-bold text-sm <?= $done ? 'text-primary' : 'text-on-surface-variant' ?>">
              <?= $step['label'] ?>
            </p>
            <p class="text-xs text-on-surface-variant mt-0.5"><?= $step['desc'] ?></p>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- Actions Sidebar -->
  <div class="space-y-5">
    <!-- Update Status -->
    <?php if (!in_array($delivery['status'], ['delivered', 'cancelled'])): ?>
    <div class="bg-surface-container-lowest rounded-[1.5rem] p-6 border border-outline-variant/10 shadow-sm">
      <h3 class="font-bold text-primary mb-4">Update Status</h3>
      <form method="POST" action="<?= APP_URL ?>/transport/delivery">
        <input type="hidden" name="_token" value="<?= e(Session::csrfToken()) ?>">
        <input type="hidden" name="action" value="update_delivery_status">
        <input type="hidden" name="delivery_id" value="<?= e($delivery['id']) ?>">
        <div class="form-group mb-4">
          <label class="form-label text-xs">New Status</label>
          <select name="status" class="form-select text-sm" required>
            <?php
            $nextOptions = [
              'pending'    => ['assigned'=>'Mark Assigned'],
              'assigned'   => ['in_transit'=>'Start Transit (Picked Up)'],
              'in_transit' => ['delivered'=>'Mark Delivered'],
            ];
            foreach ($nextOptions[$delivery['status']] ?? [] as $val => $lbl):
            ?>
            <option value="<?= $val ?>"><?= $lbl ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group mb-4">
          <label class="form-label text-xs">Note (optional)</label>
          <textarea name="note" class="form-input text-sm" rows="2" placeholder="e.g. Goods loaded, heading to Accra…"></textarea>
        </div>
        <button type="submit" class="btn btn-primary btn-full">Update</button>
      </form>
    </div>
    <?php else: ?>
    <div class="bg-primary-fixed/20 rounded-[1.5rem] p-6 text-center">
      <span class="material-symbols-outlined text-primary" style="font-size:2.5rem">check_circle</span>
      <p class="font-bold text-primary mt-2">Delivery Complete</p>
    </div>
    <?php endif; ?>

    <!-- Details -->
    <div class="bg-surface-container-low rounded-[1.5rem] p-6 border border-outline-variant/10 space-y-3">
      <h4 class="font-bold text-primary text-sm">Job Details</h4>
      <?php
      $details = [
        'Order'      => '#' . str_pad($delivery['order_id'] ?? 0, 4, '0', STR_PAD_LEFT),
        'Scheduled'  => $delivery['estimated_arrival'] ? date('d M Y', strtotime($delivery['estimated_arrival'])) : '—',
        'Vehicle'      => $delivery['vehicle_code'] ?? 'TBD',
        'Status'     => ucfirst(str_replace('_',' ',$delivery['status'])),
      ];
      foreach ($details as $k => $v):
      ?>
      <div class="flex justify-between text-sm">
        <span class="text-on-surface-variant"><?= $k ?></span>
        <span class="font-bold"><?= e($v) ?></span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

</main>
</div>
</div>
<?php include BASE_PATH . '/app/views/partials/foot.php'; ?>
