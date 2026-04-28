<?php include BASE_PATH . '/app/views/partials/head.php'; ?>
<div class="app-layout">
<?php include BASE_PATH . '/app/views/partials/sidebar.php'; ?>
<div class="main-wrap">
<?php include BASE_PATH . '/app/views/partials/topbar.php'; ?>
<main class="main-content">
<?php include BASE_PATH . '/app/views/partials/alerts.php'; ?>

<div class="flex items-center justify-between mb-8">
  <div>
    <h1 class="text-3xl font-extrabold tracking-tight text-primary">My Listings</h1>
    <p class="text-on-surface-variant text-sm mt-1"><?= count($listings) ?> produce items listed</p>
  </div>
  <a href="<?= APP_URL ?>/farmer/listings/add"
     class="btn btn-primary flex items-center gap-2">
    <span class="material-symbols-outlined" style="font-size:1rem">add</span>Add Listing
  </a>
</div>

<div class="bg-surface-container-lowest rounded-[2rem] overflow-hidden shadow-sm border border-outline-variant/10">
  <?php if (empty($listings)): ?>
    <div class="p-16 text-center text-on-surface-variant">
      <span class="material-symbols-outlined" style="font-size:4rem">inventory_2</span>
      <p class="mt-3 text-lg font-bold">No listings yet</p>
      <a href="<?= APP_URL ?>/farmer/listings/add" class="btn btn-primary mt-4 inline-flex">Add your first listing</a>
    </div>
  <?php else: ?>
  <div class="overflow-x-auto">
    <table class="w-full text-left">
      <thead>
        <tr class="bg-surface-container-low/50">
          <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Produce</th>
          <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Qty</th>
          <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Price</th>
          <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Region</th>
          <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Status</th>
          <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Listed</th>
          <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-surface-container">
        <?php foreach ($listings as $item): ?>
        <tr class="hover:bg-surface-container-low/30 transition-colors">
          <td class="px-6 py-5">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl overflow-hidden flex-shrink-0 shadow-sm">
                <img src="<?= Helpers::produceImage($item['name'], $item['category'] ?? '') ?>"
                     alt="<?= e($item['name']) ?>"
                     class="w-full h-full object-cover">
              </div>
              <div>
                <p class="font-bold text-primary"><?= e($item['name']) ?></p>
                <p class="text-xs text-on-surface-variant"><?= e($item['category']) ?></p>
              </div>
            </div>
          </td>
          <td class="px-6 py-5 font-bold"><?= e($item['quantity']) ?> <?= e($item['unit']) ?></td>
          <td class="px-6 py-5 font-bold text-primary">₵<?= number_format($item['price_per_unit'], 2) ?></td>
          <td class="px-6 py-5 text-sm text-on-surface-variant"><?= e($item['region']) ?></td>
          <td class="px-6 py-5">
            <?php $cls=['available'=>'badge-success','sold'=>'badge-info','in_transit'=>'badge-warning'][$item['status']] ?? 'badge-info'; ?>
            <span class="badge <?= $cls ?>"><?= ucfirst(str_replace('_',' ',$item['status'])) ?></span>
          </td>
          <td class="px-6 py-5 text-sm text-on-surface-variant"><?= date('d M Y', strtotime($item['created_at'])) ?></td>
          <td class="px-6 py-5">
            <div class="flex items-center gap-1">
              <a href="<?= APP_URL ?>/farmer/listings/edit?id=<?= $item['id'] ?>"
                 class="p-1.5 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container transition-colors"
                 title="Edit">
                <span class="material-symbols-outlined" style="font-size:1.1rem">edit</span>
              </a>
              <form method="POST" action="<?= APP_URL ?>/farmer/listings"
                    onsubmit="return confirm('Delete this listing?')" style="display:inline">
                <input type="hidden" name="_token" value="<?= e(Session::csrfToken()) ?>">
                <input type="hidden" name="action" value="delete_listing">
                <input type="hidden" name="listing_id" value="<?= $item['id'] ?>">
                <button type="submit"
                        class="p-1.5 rounded-lg text-on-surface-variant hover:text-error hover:bg-error-container transition-colors"
                        title="Delete">
                  <span class="material-symbols-outlined" style="font-size:1.1rem">delete</span>
                </button>
              </form>
            </div>
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
