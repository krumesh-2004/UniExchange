<?php
require_once __DIR__ . '/config/config.php';

$categoryModel = new Category();
$itemModel = new Item();
$savedModel = new SavedItem();

$categories = $categoryModel->getAllCategories();
$latestItems = $itemModel->getLatest(8);

$pageTitle = 'Home';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
?>

<!-- HERO -->
<section class="ux-hero">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-7">
        <h1>Buy, Sell &amp; Exchange <br>Books &amp; Equipment <br>with Fellow Students</h1>
        <p class="lead mt-3">Save 50-70% on textbooks. Find what you need in minutes, not weeks. Built exclusively for university students.</p>
        <div class="d-flex gap-3 mt-4 flex-wrap">
          <?php if (!Auth::isLoggedIn()): ?>
            <a href="register.php" class="btn btn-light btn-lg rounded-pill fw-bold text-purple">Get Started Free</a>
          <?php endif; ?>
          <a href="browse.php" class="btn btn-outline-light btn-lg rounded-pill fw-bold">Browse Items</a>
        </div>
        <div class="d-flex gap-3 mt-4 flex-wrap">
          <div class="stat-box"><h4 class="mb-0 fw-bold">50-70%</h4><small>Money Saved</small></div>
          <div class="stat-box"><h4 class="mb-0 fw-bold"><?php echo count($latestItems); ?>+</h4><small>Active Listings</small></div>
          <div class="stat-box"><h4 class="mb-0 fw-bold">100%</h4><small>Campus Only</small></div>
        </div>
      </div>
      <div class="col-lg-5 d-none d-lg-block text-center">
        <i class="fa-solid fa-book-open-reader" style="font-size:220px; opacity:0.25;"></i>
      </div>
    </div>

    <!-- Search Bar -->
    <form action="browse.php" method="GET" class="ux-search-bar row g-2 align-items-center mt-4 mx-1">
      <div class="col-md-6">
        <input type="text" name="keyword" class="form-control ps-3" placeholder="Search for textbooks, calculators, notes...">
      </div>
      <div class="col-md-3">
        <select name="cat_id" class="form-select">
          <option value="">All Categories</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?php echo $cat['cat_id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <button class="btn btn-ux-primary w-100" type="submit"><i class="fa-solid fa-magnifying-glass me-1"></i> Search</button>
      </div>
    </form>
  </div>
</section>

<!-- CATEGORIES -->
<section class="container py-5">
  <h2 class="section-title">Browse by Category</h2>
  <div class="d-flex flex-wrap gap-3 mt-4">
    <?php foreach ($categories as $cat): ?>
      <a href="browse.php?cat_id=<?php echo $cat['cat_id']; ?>" class="cat-pill">
        <i class="fa-solid <?php echo htmlspecialchars($cat['icon']); ?>"></i> <?php echo htmlspecialchars($cat['name']); ?>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<!-- LATEST ITEMS -->
<section class="container py-4">
  <div class="d-flex justify-content-between align-items-end flex-wrap">
    <h2 class="section-title">Latest Listings</h2>
    <a href="browse.php" class="btn btn-ux-outline btn-sm">View All <i class="fa-solid fa-arrow-right ms-1"></i></a>
  </div>
  <div class="row g-4 mt-2">
    <?php if (empty($latestItems)): ?>
      <p class="text-muted">No items posted yet. Be the first to <a href="post-item.php">sell something</a>!</p>
    <?php endif; ?>
    <?php foreach ($latestItems as $item):
        $isSaved = Auth::isLoggedIn() ? $savedModel->isSaved(Auth::id(), $item['item_id']) : false;
    ?>
    <div class="col-md-3 col-sm-6">
      <div class="item-card position-relative">
        <div class="img-wrap">
          <?php if ($item['image']): ?>
            <img src="<?php echo UPLOAD_URL . htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
          <?php else: ?>
            <i class="fa-solid fa-book" style="font-size:60px; color:#a29bfe;"></i>
          <?php endif; ?>
          <span class="price-tag">Rs. <?php echo number_format($item['price'], 2); ?></span>
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

<!-- HOW IT WORKS -->
<section class="container py-5">
  <h2 class="section-title">How UniExchange Works</h2>
  <div class="row g-4 mt-3 text-center">
    <div class="col-md-3">
      <div class="dash-stat bg-purple mx-auto" style="width:70px;height:70px;border-radius:50%;display:flex;align-items:center;justify-content:center;"><i class="fa-solid fa-user-plus"></i></div>
      <h6 class="mt-3 fw-bold">1. Register</h6>
      <p class="small text-muted">Sign up with your student details in seconds.</p>
    </div>
    <div class="col-md-3">
      <div class="dash-stat bg-pink mx-auto" style="width:70px;height:70px;border-radius:50%;display:flex;align-items:center;justify-content:center;"><i class="fa-solid fa-upload"></i></div>
      <h6 class="mt-3 fw-bold">2. Post or Search</h6>
      <p class="small text-muted">List your items or search what you need.</p>
    </div>
    <div class="col-md-3">
      <div class="dash-stat bg-green mx-auto" style="width:70px;height:70px;border-radius:50%;display:flex;align-items:center;justify-content:center;"><i class="fa-solid fa-comments"></i></div>
      <h6 class="mt-3 fw-bold">3. Message</h6>
      <p class="small text-muted">Chat directly with the buyer or seller.</p>
    </div>
    <div class="col-md-3">
      <div class="dash-stat bg-orange mx-auto" style="width:70px;height:70px;border-radius:50%;display:flex;align-items:center;justify-content:center;"><i class="fa-solid fa-handshake"></i></div>
      <h6 class="mt-3 fw-bold">4. Meet &amp; Exchange</h6>
      <p class="small text-muted">Meet on campus and complete the exchange.</p>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
