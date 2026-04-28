<?php include BASE_PATH . '/app/views/partials/head.php'; ?>
<div class="app-layout">
<?php include BASE_PATH . '/app/views/partials/sidebar.php'; ?>
<div class="main-wrap">
<?php include BASE_PATH . '/app/views/partials/topbar.php'; ?>
<main class="main-content">
<?php include BASE_PATH . '/app/views/partials/alerts.php'; ?>

<div class="flex items-center justify-between mb-8">
  <div>
    <h1 class="text-3xl font-extrabold tracking-tight text-primary">Manage Users</h1>
    <p class="text-on-surface-variant text-sm mt-1"><?= count($users) ?> registered users</p>
  </div>
</div>

<!-- Search & Filter -->
<form method="GET" action="<?= APP_URL ?>/admin/users" class="flex flex-wrap gap-3 mb-6">
  <input type="text" name="q" class="form-input flex-1 min-w-[200px] text-sm" placeholder="Search name or email…"
         value="<?= e($_GET['q'] ?? '') ?>">
  <select name="role" class="form-select text-sm" style="width:auto">
    <option value="">All Roles</option>
    <?php foreach(['farmer','buyer','transport','admin'] as $r): ?>
    <option value="<?= $r ?>" <?= ($_GET['role']??'')===$r?'selected':'' ?>><?= ucfirst($r) ?></option>
    <?php endforeach; ?>
  </select>
  <button type="submit" class="btn btn-primary text-sm">Search</button>
  <a href="<?= APP_URL ?>/admin/users" class="btn btn-secondary text-sm">Clear</a>
</form>

<div class="bg-surface-container-lowest rounded-[2rem] overflow-hidden border border-outline-variant/10 shadow-sm">
  <?php if (empty($users)): ?>
  <div class="p-16 text-center text-on-surface-variant">
    <span class="material-symbols-outlined" style="font-size:3.5rem">person_search</span>
    <p class="mt-3 font-bold">No users found</p>
  </div>
  <?php else: ?>
  <div class="overflow-x-auto">
    <table class="w-full text-left">
      <thead>
        <tr class="bg-surface-container-low/50">
          <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">User</th>
          <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Email</th>
          <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Phone</th>
          <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Role</th>
          <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Region</th>
          <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Joined</th>
          <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Status</th>
          <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-surface-container">
        <?php foreach ($users as $u): ?>
        <tr class="hover:bg-surface-container-low/30 transition-colors <?= empty($u['is_active']) ? 'opacity-50' : '' ?>">
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-full bg-primary flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                <?= strtoupper(substr($u['name'],0,1)) ?>
              </div>
              <div>
                <span class="font-bold text-sm"><?= e($u['name']) ?></span>
                <?php if (!empty($u['is_verified'])): ?>
                <span class="ml-1 inline-flex items-center gap-0.5 text-xs text-cyan-700 font-semibold">
                  <span class="material-symbols-outlined" style="font-size:.75rem">verified</span>Verified
                </span>
                <?php endif; ?>
              </div>
            </div>
          </td>
          <td class="px-6 py-4 text-sm text-on-surface-variant"><?= e($u['email']) ?></td>
          <td class="px-6 py-4 text-sm"><?= e($u['phone'] ?? '—') ?></td>
          <td class="px-6 py-4">
            <?php $roles=['farmer'=>'badge-success','buyer'=>'badge-info','transport'=>'badge-warning','admin'=>'badge-error']; ?>
            <span class="badge <?= $roles[$u['role']] ?? 'badge-info' ?> text-xs capitalize"><?= e($u['role']) ?></span>
          </td>
          <td class="px-6 py-4 text-sm"><?= e($u['region'] ?? '—') ?></td>
          <td class="px-6 py-4 text-sm text-on-surface-variant"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
          <td class="px-6 py-4">
            <?php if (!empty($u['is_active'])): ?>
            <span class="badge badge-success text-xs">Active</span>
            <?php else: ?>
            <span class="badge badge-error text-xs">Inactive</span>
            <?php endif; ?>
          </td>
          <td class="px-6 py-4">
            <div class="flex items-center gap-2">
              <!-- Verify / Unverify -->
              <form method="POST" action="<?= APP_URL ?>/admin/users" class="inline">
                <input type="hidden" name="_token"   value="<?= e(Session::csrfToken()) ?>">
                <input type="hidden" name="action"   value="verify_user">
                <input type="hidden" name="user_id"  value="<?= (int)$u['id'] ?>">
                <input type="hidden" name="verified" value="<?= empty($u['is_verified']) ? '1' : '0' ?>">
                <button type="submit"
                        class="p-1.5 rounded-lg transition-colors text-xs font-bold
                               <?= empty($u['is_verified'])
                                   ? 'bg-cyan-50 text-cyan-700 hover:bg-cyan-100 border border-cyan-200'
                                   : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high border border-outline-variant/20' ?>"
                        title="<?= empty($u['is_verified']) ? 'Verify user' : 'Unverify user' ?>">
                  <span class="material-symbols-outlined" style="font-size:.95rem">
                    <?= empty($u['is_verified']) ? 'verified' : 'remove_moderator' ?>
                  </span>
                </button>
              </form>
              <!-- Activate / Deactivate -->
              <?php if ((int)$u['id'] !== Session::userId()): ?>
              <form method="POST" action="<?= APP_URL ?>/admin/users" class="inline">
                <input type="hidden" name="_token"  value="<?= e(Session::csrfToken()) ?>">
                <input type="hidden" name="action"  value="toggle_active">
                <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                <button type="submit"
                        onclick="return confirm('<?= empty($u['is_active']) ? 'Activate' : 'Deactivate' ?> this user?')"
                        class="p-1.5 rounded-lg transition-colors text-xs font-bold
                               <?= empty($u['is_active'])
                                   ? 'bg-green-50 text-green-700 hover:bg-green-100 border border-green-200'
                                   : 'bg-red-50 text-red-600 hover:bg-red-100 border border-red-200' ?>"
                        title="<?= empty($u['is_active']) ? 'Activate user' : 'Deactivate user' ?>">
                  <span class="material-symbols-outlined" style="font-size:.95rem">
                    <?= empty($u['is_active']) ? 'person_check' : 'person_off' ?>
                  </span>
                </button>
              </form>
              <?php endif; ?>
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
