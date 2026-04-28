<?php include BASE_PATH . '/app/views/partials/head.php'; ?>
<div class="app-layout">
<?php include BASE_PATH . '/app/views/partials/sidebar.php'; ?>
<div class="main-wrap">
<?php include BASE_PATH . '/app/views/partials/topbar.php'; ?>
<main class="main-content">
<?php include BASE_PATH . '/app/views/partials/alerts.php'; ?>

<div class="flex items-center justify-between mb-8">
  <div>
    <h1 class="text-3xl font-extrabold tracking-tight text-primary">Orders Received</h1>
    <p class="text-on-surface-variant text-sm mt-1"><?= count($orders) ?> orders on your produce</p>
  </div>
</div>

<!-- Status Summary Chips -->
<div class="flex flex-wrap gap-3 mb-8">
  <?php
  $allStatuses = ['pending','confirmed','in_transit','delivered','cancelled'];
  $statusColors = [
    'pending'    => 'bg-secondary-container text-on-secondary-container',
    'confirmed'  => 'bg-primary-container/50 text-on-primary-container',
    'in_transit' => 'bg-tertiary-fixed text-on-tertiary-fixed-variant',
    'delivered'  => 'bg-primary-fixed text-on-primary-fixed-variant',
    'cancelled'  => 'bg-error-container text-error',
  ];
  foreach ($allStatuses as $s):
    $cnt = count(array_filter($orders, fn($o) => $o['status'] === $s));
  ?>
  <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-sm font-bold <?= $statusColors[$s] ?>">
    <?= ucfirst(str_replace('_',' ',$s)) ?>
    <span class="font-extrabold">(<?= $cnt ?>)</span>
  </span>
  <?php endforeach; ?>
</div>

<div class="bg-surface-container-lowest rounded-[2rem] overflow-hidden shadow-sm border border-outline-variant/10">
  <?php if (empty($orders)): ?>
    <div class="p-16 text-center text-on-surface-variant">
      <span class="material-symbols-outlined" style="font-size:4rem">receipt_long</span>
      <p class="mt-3 text-lg font-bold">No orders yet</p>
      <p class="text-sm">Orders placed on your listings will appear here.</p>
    </div>
  <?php else: ?>
  <div class="overflow-x-auto">
    <table class="w-full text-left">
      <thead>
        <tr class="bg-surface-container-low/50">
          <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Order</th>
          <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Buyer</th>
          <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Produce</th>
          <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Qty</th>
          <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Total</th>
          <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Date</th>
          <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Status</th>
          <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Update</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-surface-container">
        <?php foreach ($orders as $o): ?>
        <tr class="hover:bg-surface-container-low/30 transition-colors">
          <td class="px-6 py-5 font-mono text-sm font-bold text-primary">#<?= str_pad($o['id'], 4, '0', STR_PAD_LEFT) ?></td>
          <td class="px-6 py-5">
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white text-xs font-bold">
                <?= strtoupper(substr($o['buyer_name'] ?? 'B', 0, 1)) ?>
              </div>
              <span class="font-semibold text-sm"><?= e($o['buyer_name'] ?? '—') ?></span>
            </div>
          </td>
          <td class="px-6 py-5 font-semibold"><?= e($o['produce_name'] ?? '—') ?></td>
          <td class="px-6 py-5 text-sm"><?= e($o['quantity']) ?> <?= e($o['unit'] ?? '') ?></td>
          <td class="px-6 py-5 font-bold text-primary">₵<?= number_format($o['total_price'], 2) ?></td>
          <td class="px-6 py-5 text-sm text-on-surface-variant"><?= date('d M Y', strtotime($o['created_at'])) ?></td>
          <td class="px-6 py-5">
            <?php
              $cls = [
                'pending'   =>'badge-warning','confirmed'=>'badge-info',
                'in_transit'=>'badge-warning','delivered'=>'badge-success','cancelled'=>'badge-error'
              ][$o['status']] ?? 'badge-info';
            ?>
            <span class="badge <?= $cls ?>"><?= ucfirst(str_replace('_',' ',$o['status'])) ?></span>
          </td>
          <td class="px-6 py-5">
            <?php if (in_array($o['status'], ['pending','confirmed'])): ?>
            <form method="POST" action="<?= APP_URL ?>/farmer/orders" class="inline-flex gap-1">
              <input type="hidden" name="_token" value="<?= e(Session::csrfToken()) ?>">
              <input type="hidden" name="action" value="update_order_status">
              <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
              <?php if ($o['status'] === 'pending'): ?>
              <button name="status" value="confirmed"
                      class="text-xs px-3 py-1.5 rounded-lg bg-primary-container text-on-primary-container font-bold hover:opacity-90">
                Confirm
              </button>
              <button name="status" value="cancelled"
                      class="text-xs px-3 py-1.5 rounded-lg bg-error-container/30 text-error font-bold hover:bg-error-container/50">
                Reject
              </button>
              <?php elseif ($o['status'] === 'confirmed'): ?>
              <button name="status" value="in_transit"
                      class="text-xs px-3 py-1.5 rounded-lg bg-tertiary-fixed text-on-tertiary-fixed-variant font-bold hover:opacity-90">
                Mark Shipped
              </button>
              <?php endif; ?>
            </form>
            <?php else: ?>
            <span class="text-xs text-on-surface-variant">—</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

</main>
</div>
</div>
<?php include BASE_PATH . '/app/views/partials/foot.php'; ?>
