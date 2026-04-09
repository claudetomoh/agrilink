<?php include BASE_PATH . '/app/views/partials/head.php'; ?>
<div class="app-layout">
<?php include BASE_PATH . '/app/views/partials/sidebar.php'; ?>
<div class="main-wrap">
<?php include BASE_PATH . '/app/views/partials/topbar.php'; ?>
<main class="main-content">
<?php include BASE_PATH . '/app/views/partials/alerts.php'; ?>

<div class="flex items-center justify-between mb-8">
  <div>
    <h1 class="text-3xl font-extrabold tracking-tight text-primary">All Orders</h1>
    <p class="text-on-surface-variant text-sm mt-1"><?= count($orders) ?> total orders</p>
  </div>
</div>

<div class="bg-surface-container-lowest rounded-[2rem] overflow-hidden border border-outline-variant/10 shadow-sm">
  <div class="overflow-x-auto">
    <table class="w-full text-left">
      <thead>
        <tr class="bg-surface-container-low/50">
          <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">#</th>
          <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Produce</th>
          <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Buyer</th>
          <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Seller</th>
          <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Qty</th>
          <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Total</th>
          <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Date</th>
          <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Status</th>
          <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Change</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-surface-container">
        <?php foreach ($orders as $o): ?>
        <tr class="hover:bg-surface-container-low/30 transition-colors">
          <td class="px-6 py-4 font-mono text-xs font-bold">#<?= str_pad($o['id'],4,'0',STR_PAD_LEFT) ?></td>
          <td class="px-6 py-4 font-semibold text-sm"><?= e($o['produce_name'] ?? '—') ?></td>
          <td class="px-6 py-4 text-sm"><?= e($o['buyer_name']  ?? '—') ?></td>
          <td class="px-6 py-4 text-sm"><?= e($o['farmer_name'] ?? '—') ?></td>
          <td class="px-6 py-4 text-sm"><?= e($o['quantity']) ?> <?= e($o['unit'] ?? '') ?></td>
          <td class="px-6 py-4 font-bold text-primary">₵<?= number_format($o['total_price'],2) ?></td>
          <td class="px-6 py-4 text-sm text-on-surface-variant"><?= date('d M Y', strtotime($o['created_at'])) ?></td>
          <td class="px-6 py-4">
            <?php $cls=['pending'=>'badge-warning','confirmed'=>'badge-info','in_transit'=>'badge-warning','delivered'=>'badge-success','cancelled'=>'badge-error'][$o['status']]??'badge-info'; ?>
            <span class="badge <?= $cls ?>"><?= ucfirst(str_replace('_',' ',$o['status'])) ?></span>
          </td>
          <td class="px-6 py-4">
            <form method="POST" action="<?= APP_URL ?>/admin/orders" class="flex items-center gap-1">
              <input type="hidden" name="_token"   value="<?= e(Session::csrfToken()) ?>">
              <input type="hidden" name="action"   value="update_order_status">
              <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
              <select name="status" class="form-select text-xs py-1 px-2" style="min-width:110px">
                <?php foreach(['pending','confirmed','in_transit','delivered','cancelled'] as $s): ?>
                <option value="<?= $s ?>" <?= $o['status']===$s?'selected':'' ?>><?= ucfirst(str_replace('_',' ',$s)) ?></option>
                <?php endforeach; ?>
              </select>
              <button type="submit" class="p-1.5 rounded-lg bg-primary-container/30 text-primary hover:bg-primary-container/60 transition-colors">
                <span class="material-symbols-outlined" style="font-size:.9rem">check</span>
              </button>
            </form>
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
