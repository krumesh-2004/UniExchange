<?php
require_once __DIR__ . '/config/config.php';

$categoryModel = new Category();
$itemModel = new Item();
$savedModel = new SavedItem();

$categories = $categoryModel->getAllCategories();

$filters = [
    'keyword'   => trim($_GET['keyword'] ?? ''),
    'cat_id'    => $_GET['cat_id'] ?? '',
    'condition' => $_GET['condition'] ?? '',
    'min_price' => $_GET['min_price'] ?? '',
    'max_price' => $_GET['max_price'] ?? '',
    'sort'      => $_GET['sort'] ?? 'newest',
];

$items = $itemModel->search($filters);

$pageTitle = 'Browse Items';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
?>
<section class="container py-5">
  <h2 class="section-title">Browse Items</h2>

  <div class="row mt-4 g-4">
    <!-- FILTER SIDEBAR -->
    <div class="col-lg-3">
      <div class="sidebar-panel">
        <h6 class="fw-bold mb-3"><i class="fa-solid fa-filter me-2 text-purple"></i>Filters</h6>
        <form method="GET" class="ux-form">
          <div class="mb-3">
            <label>Keyword</label>
            <input type="text" name="keyword" class="form-control" placeholder="Search title or description" value="<?php echo htmlspecialchars($filters['keyword']); ?>">
          </div>
          <div class="mb-3">
            <label>Category</label>
            <select name="cat_id" class="form-select">
              <option value="">All Categories</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat['cat_id']; ?>" <?php echo ($filters['cat_id'] == $cat['cat_id']) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($cat['name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label>Condition</label>
            <select name="condition" class="form-select">
              <option value="">Any Condition</option>
              <option value="like_new" <?php echo $filters['condition']=='like_new'?'selected':''; ?>>Like New</option>
              <option value="good" <?php echo $filters['condition']=='good'?'selected':''; ?>>Good</option>
              <option value="fair" <?php echo $filters['condition']=='fair'?'selected':''; ?>>Fair</option>
              <option value="poor" <?php echo $filters['condition']=='poor'?'selected':''; ?>>Poor</option>
            </select>
          </div>
          <div class="mb-3">
            <label>Price Range (LKR)</label>
            <div class="d-flex gap-2">
              <input type="number" name="min_price" class="form-control" placeholder="Min" value="<?php echo htmlspecialchars($filters['min_price']); ?>">
              <input type="number" name="max_price" class="form-control" placeholder="Max" value="<?php echo htmlspecialchars($filters['max_price']); ?>">
            </div>
          </div>
          <div class="mb-3">
            <label>Sort By</label>
            <select name="sort" class="form-select">
              <option value="newest" <?php echo $filters['sort']=='newest'?'selected':''; ?>>Newest First</option>
              <option value="price_low" <?php echo $filters['sort']=='price_low'?'selected':''; ?>>Price: Low to High</option>
              <option value="price_high" <?php echo $filters['sort']=='price_high'?'selected':''; ?>>Price: High to Low</option>
            </select>
          </div>
          <button type="submit" class="btn btn-ux-primary w-100">Apply Filters</button>
          <a href="browse.php" class="btn btn-ux-outline w-100 mt-2">Clear All</a>
        </form>
      </div>
    </div>

    <!-- RESULTS -->
    <div class="col-lg-9">
      <p class="text-muted"><?php echo count($items); ?> item(s) found</p>
      <div class="row g-4">
        <?php if (empty($items)): ?>
          <div class="col-12 text-center py-5">
            <i class="fa-solid fa-box-open fa-3x text-muted mb-3"></i>
            <h5>No items found</h5>
            <p class="text-muted">Try adjusting your filters or search keyword.</p>
          </div>
        <?php endif; ?>
        <?php foreach ($items as $item):
            $isSaved = Auth::isLoggedIn() ? $savedModel->isSaved(Auth::id(), $item['item_id']) : false;
        ?>
        <div class="col-md-4 col-sm-6">
          <div class="item-card position-relative">
            <div class="img-wrap">
              <?php if ($item['image']): ?>
                <img src="<?php echo UPLOAD_URL . htmlspecialchars($item['image']); ?>" alt="">
              <?php else: ?>
                <i class="fa-solid fa-book" style="font-size:60px; color:#a29bfe;"></i>
              <?php endif; ?>
              <span class="price-tag">Rs. <?php echo number_format($item['price'], 2); ?></span>
              <?php if (Auth::isLoggedIn()): ?>
              <form method="POST" action="save-item.php" class="d-inline">
                <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                <input type="hidden" name="redirect" value="browse.php">
                <button type="submit" class="save-btn <?php echo $isSaved?'saved':''; ?>" title="Save to wishlist">
                  <i class="fa-<?php echo $isSaved?'solid':'regular'; ?> fa-heart"></i>
                </button>
              </form>
              <?php endif; ?>
            </div>
            <div class="card-body p-3">
              <span class="badge-cat"><?php echo htmlspecialchars($item['category_name']); ?></span>
              <span class="badge-cat text-capitalize"><?php echo str_replace('_',' ',$item['condition']); ?></span>
              <h5 class="mt-2 mb-1"><a href="item-details.php?id=<?php echo $item['item_id']; ?>" class="text-dark stretched-link"><?php echo htmlspecialchars($item['title']); ?></a></h5>
              <small class="text-muted"><i class="fa-solid fa-location-dot me-1"></i><?php echo htmlspecialchars($item['location']); ?></small><br>
              <small class="text-muted"><i class="fa-solid fa-user me-1"></i><?php echo htmlspecialchars($item['seller_name']); ?></small>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
