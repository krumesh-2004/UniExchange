<?php
require_once __DIR__ . '/config/config.php';
Auth::requireLogin();

$itemModel = new Item();
$messageModel = new Message();
$myItems = $itemModel->getByUser(Auth::id());

$totalItems = count($myItems);
$soldCount = count(array_filter($myItems, fn($i) => $i['status'] === 'sold'));
$availableCount = count(array_filter($myItems, fn($i) => $i['status'] === 'available'));
$unread = $messageModel->countUnread(Auth::id());

$pageTitle = 'Dashboard';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
?>
<section class="container py-5">
  <?php require __DIR__ . '/includes/alerts.php'; ?>
  <h2 class="section-title">My Dashboard</h2>
  <p class="text-muted">Welcome back, <?php echo htmlspecialchars(Auth::name()); ?>!</p>

  <div class="row g-4 mt-2">
    <div class="col-md-3 col-sm-6">
      <div class="dash-stat bg-purple"><i class="fa-solid fa-boxes-stacked"></i><h3 class="mt-2"><?php echo $totalItems; ?></h3><span>Total Listings</span></div>
    </div>
    <div class="col-md-3 col-sm-6">
      <div class="dash-stat bg-green"><i class="fa-solid fa-circle-check"></i><h3 class="mt-2"><?php echo $availableCount; ?></h3><span>Available</span></div>
    </div>
    <div class="col-md-3 col-sm-6">
      <div class="dash-stat bg-pink"><i class="fa-solid fa-handshake"></i><h3 class="mt-2"><?php echo $soldCount; ?></h3><span>Sold</span></div>
    </div>
    <div class="col-md-3 col-sm-6">
      <div class="dash-stat bg-orange"><i class="fa-solid fa-envelope"></i><h3 class="mt-2"><?php echo $unread; ?></h3><span>Unread Messages</span></div>
    </div>
  </div>

  <div class="d-flex justify-content-between align-items-center mt-5 mb-3 flex-wrap">
    <h4 class="fw-bold mb-0">My Listings</h4>
    <a href="post-item.php" class="btn btn-ux-primary"><i class="fa-solid fa-plus me-1"></i> Post New Item</a>
  </div>

  <div class="ux-card p-0 overflow-hidden">
    <div class="table-responsive">
      <table class="table table-ux mb-0 align-middle">
        <thead>
          <tr>
            <th>Item</th><th>Category</th><th>Price</th><th>Status</th><th>Views</th><th>Posted</th><th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($myItems)): ?>
            <tr><td colspan="7" class="text-center text-muted py-4">You haven't posted any items yet. <a href="post-item.php">Post your first item</a>.</td></tr>
          <?php endif; ?>
          <?php foreach ($myItems as $item): ?>
          <tr>
            <td>
              <div class="d-flex align-items-center gap-2">
                <div style="width:45px;height:45px;border-radius:10px;overflow:hidden;background:#f0f0fa;display:flex;align-items:center;justify-content:center;">
                  <?php if ($item['image']): ?>
                    <img src="<?php echo UPLOAD_URL . htmlspecialchars($item['image']); ?>" style="width:100%;height:100%;object-fit:cover;">
                  <?php else: ?><i class="fa-solid fa-book text-purple"></i><?php endif; ?>
                </div>
                <a href="item-details.php?id=<?php echo $item['item_id']; ?>" class="fw-semibold text-dark"><?php echo htmlspecialchars($item['title']); ?></a>
              </div>
            </td>
            <td><?php echo htmlspecialchars($item['category_name']); ?></td>
            <td>Rs. <?php echo number_format($item['price'],2); ?></td>
            <td><span class="status-badge status-<?php echo $item['status']; ?> text-capitalize"><?php echo $item['status']; ?></span></td>
            <td><?php echo $item['views']; ?></td>
            <td><?php echo date('M d, Y', strtotime($item['created_at'])); ?></td>
            <td class="text-end">
              <a href="edit-item.php?id=<?php echo $item['item_id']; ?>" class="btn btn-sm btn-ux-outline" title="Edit"><i class="fa-solid fa-pen"></i></a>
              <?php if ($item['status'] === 'available'): ?>
                <a href="mark-sold.php?id=<?php echo $item['item_id']; ?>" class="btn btn-sm btn-ux-green confirm-action" data-confirm="Mark this item as sold?" title="Mark Sold"><i class="fa-solid fa-check"></i></a>
              <?php endif; ?>
              <a href="delete-item.php?id=<?php echo $item['item_id']; ?>" class="btn btn-sm btn-ux-danger confirm-action" data-confirm="Delete this listing permanently?" title="Delete"><i class="fa-solid fa-trash"></i></a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
