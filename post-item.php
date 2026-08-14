<?php
require_once __DIR__ . '/config/config.php';
$error = null;
Auth::requireLogin();

$categoryModel = new Category();
$itemModel = new Item();
$categories = $categoryModel->getAllCategories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $title       = trim($_POST['title'] ?? '');
        $catId       = (int) ($_POST['cat_id'] ?? 0);
        $condition   = $_POST['condition'] ?? 'good';
        $price       = $_POST['price'] ?? '';
        $origPrice   = $_POST['original_price'] ?? '';
        $description = trim($_POST['description'] ?? '');
        $location    = trim($_POST['location'] ?? '');
        $contact     = trim($_POST['contact_number'] ?? '');

        if ($title === '' || $catId === 0 || $price === '' || $description === '' || $location === '' || $contact === '') {
            throw new Exception('Please fill in all required fields.');
        }
        if (!is_numeric($price) || $price <= 0) {
            throw new Exception('Please enter a valid price.');
        }

        $imageName = null;
        if (!empty($_FILES['image']['name'])) {
            if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('Image upload failed. Please try again.');
            }
            $allowed = ['jpg','jpeg','png','webp'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                throw new Exception('Only JPG, PNG or WEBP images are allowed.');
            }
            if ($_FILES['image']['size'] > MAX_UPLOAD_SIZE) {
                throw new Exception('Image must be smaller than 3MB.');
            }
            $imageName = 'item_' . uniqid() . '.' . $ext;
            if (!is_dir(UPLOAD_DIR)) { mkdir(UPLOAD_DIR, 0755, true); }
            $targetPath = UPLOAD_DIR . $imageName;
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                throw new Exception('Unable to save the uploaded image.');
            }
        }

        $newItemId = $itemModel->create([
            'user_id' => Auth::id(), 'cat_id' => $catId, 'title' => $title,
            'description' => $description, 'condition' => $condition,
            'price' => $price, 'original_price' => $origPrice, 'image' => $imageName,
            'location' => $location, 'contact_number' => $contact,
        ]);

        (new ActivityLog())->log(Auth::id(), 'Posted new item: ' . $title);

        Auth::flash('success', 'Your item has been posted successfully!');
        header('Location: item-details.php?id=' . $newItemId);
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$pageTitle = 'Post an Item';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
?>
<section class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="ux-card p-4 p-md-5">
        <h3 class="fw-bold mb-1"><i class="fa-solid fa-tag text-purple me-2"></i>Post an Item for Sale</h3>
        <p class="text-muted mb-4">Fill in the details below to list your book or equipment.</p>
        <?php if (!empty($error)): ?>
          <div class="alert alert-danger rounded-ux"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data" class="ux-form">
          <div class="row g-3">
            <div class="col-12">
              <label>Item Title *</label>
              <input type="text" name="title" class="form-control" required placeholder="e.g. Data Structures Textbook - 3rd Edition">
            </div>
            <div class="col-md-6">
              <label>Category *</label>
              <select name="cat_id" class="form-select" required>
                <option value="">Select category</option>
                <?php foreach ($categories as $cat): ?>
                  <option value="<?php echo $cat['cat_id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label>Condition *</label>
              <select name="condition" class="form-select" required>
                <option value="like_new">Like New</option>
                <option value="good" selected>Good</option>
                <option value="fair">Fair</option>
                <option value="poor">Poor</option>
              </select>
            </div>
            <div class="col-md-6">
              <label>Selling Price (LKR) *</label>
              <input type="number" step="0.01" name="price" class="form-control" required placeholder="1500.00">
            </div>
            <div class="col-md-6">
              <label>Original Price (Optional)</label>
              <input type="number" step="0.01" name="original_price" class="form-control" placeholder="4500.00">
            </div>
            <div class="col-12">
              <label>Description *</label>
              <textarea name="description" class="form-control" rows="4" required placeholder="Describe the item's condition, edition, any markings, etc."></textarea>
            </div>
            <div class="col-md-6">
              <label>Pickup Location *</label>
              <input type="text" name="location" class="form-control" required placeholder="e.g. Main Campus Library">
            </div>
            <div class="col-md-6">
              <label>Contact Number *</label>
              <input type="text" name="contact_number" class="form-control" required placeholder="07XXXXXXXX">
            </div>
            <div class="col-12">
              <label>Upload Image (Max 3MB)</label>
              <input type="file" name="image" id="itemImage" accept="image/*" class="form-control">
              <img id="imgPreview" src="" class="img-fluid rounded-ux mt-3 d-none" style="max-height:220px;">
            </div>
          </div>
          <button type="submit" class="btn btn-ux-primary w-100 mt-4">
            <i class="fa-solid fa-paper-plane me-1"></i> Post Ad
          </button>
        </form>
      </div>
    </div>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
