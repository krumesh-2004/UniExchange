<?php
require_once __DIR__ . '/config/config.php';
Auth::requireLogin();

$savedModel = new SavedItem();
$wishlist = $savedModel->getWishlist(Auth::id());

$pageTitle = 'Wishlist';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
?>
<section class="container py-5">
  <?php require __DIR__ . '/includes/alerts.php'; ?>
  <h2 class="section-title">My Wishlist</h2>
  <p class="text-muted">Items you've saved for later.</p>

  <div class="row g-4 mt-2">
    <?php if (empty($wishlist)): ?>
      <div class="col-12 text-center py-5">
        <i class="fa-solid fa-heart-crack fa-3x text-muted mb-3"></i>
        <h5>Your wishlist is empty</h5>
        <a href="browse.php" class="btn btn-ux-primary mt-2">Browse Items</a>
      </div>
    <?php endif; ?>
    <?php foreach ($wishlist as $item): ?>
    <div class="col-md-3 col-sm-6">
      <div class="item-card position-relative">
        <div class="img-wrap">
          <?php if ($item['image']): ?>
            <img src="<?php echo UPLOAD_URL . htmlspecialchars($item['image']); ?>" alt="">
          <?php else: ?><i class="fa-solid fa-book" style="font-size:60px; color:#a29bfe;"></i><?php endif; ?>
          <span class="price-tag">Rs. <?php echo number_format($item['price'], 2); ?></span>
          <?php if ($item['status']==='sold'): ?><span class="badge-sold">SOLD</span><?php endif; ?>
          <form method="POST" action="save-item.php">
            <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
            <input type="hidden" name="redirect" value="wishlist.php">
            <button type="submit" class="save-btn saved"><i class="fa-solid fa-heart"></i></button>
          </form>
        </div>
        <div class="card-body p-3">
          <span class="badge-cat"><?php echo htmlspecialchars($item['category_name']); ?></span>
          <h5 class="mt-2 mb-1"><a href="item-details.php?id=<?php echo $item['item_id']; ?>" class="text-dark stretched-link"><?php echo htmlspecialchars($item['title']); ?></a></h5>
          <small class="text-muted"><i class="fa-solid fa-location-dot me-1"></i><?php echo htmlspecialchars($item['location']); ?></small>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
