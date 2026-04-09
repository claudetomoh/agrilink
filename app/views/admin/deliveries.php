<?php include BASE_PATH . '/app/views/partials/head.php'; ?>
<div class="app-layout">
<?php include BASE_PATH . '/app/views/partials/sidebar.php'; ?>
<div class="main-wrap">
<?php include BASE_PATH . '/app/views/partials/topbar.php'; ?>
<main class="main-content">
<?php include BASE_PATH . '/app/views/partials/alerts.php'; ?>

<div class="flex items-center justify-between mb-8">
  <div>
    <h1 class="text-3xl font-extrabold tracking-tight text-primary">All Deliveries</h1>
    <p class="text-on-surface-variant text-sm mt-1"><?= count($deliveries) ?> total deliveries</p>
  </div>
</div>

<div class="bg-surface-container-lowest rounded-[2rem] overflow-hidden border border-outline-variant/10 shadow-sm">
  <div class="overflow-x-auto">
    <table class="w-full text-left">
      <thead>
        <tr class="bg-surface-container-low/50">
          <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">#</th>
          <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Order</th>
          <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Route</th>
          <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Transporter</th>
          <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Est. Arrival</th>
          <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Status</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-surface-container">
        <?php foreach ($deliveries as $d): ?>
        <tr class="hover:bg-surface-container-low/30 transition-colors">
          <td class="px-6 py-4 font-mono text-xs font-bold">DEL-<?= str_pad($d['id'],4,'0',STR_PAD_LEFT) ?></td>
          <td class="px-6 py-4 font-mono text-xs">#<?= str_pad($d['order_id']??0,4,'0',STR_PAD_LEFT) ?></td>
          <td class="px-6 py-4 text-sm">
            <span class="font-semibold"><?= e($d['origin']) ?></span>
            <span class="text-on-surface-variant mx-1">→</span>
            <span class="font-semibold"><?= e($d['destination']) ?></span>
          </td>
          <td class="px-6 py-4 text-sm"><?= e($d['transport_name'] ?? 'Unassigned') ?></td>
          <td class="px-6 py-4 text-sm text-on-surface-variant"><?= $d['estimated_arrival'] ? date('d M Y', strtotime($d['estimated_arrival'])) : '—' ?></td>
          <td class="px-6 py-4">
            <?php $cls=['pending'=>'badge-warning','assigned'=>'badge-info','in_transit'=>'badge-warning','delivered'=>'badge-success','cancelled'=>'badge-error'][$d['status']]??'badge-info'; ?>
            <span class="badge <?= $cls ?>"><?= ucfirst(str_replace('_',' ',$d['status'])) ?></span>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

</main>
</div>
</div>
<?php include BASE_PATH . '/app/views/partials/foot.php'; ?>
