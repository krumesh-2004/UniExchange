<?php
require_once __DIR__ . '/config/config.php';
$error = null;
Auth::requireLogin();

$itemModel = new Item();
$categoryModel = new Category();
$categories = $categoryModel->getAllCategories();

$id = (int) ($_GET['id'] ?? $_POST['item_id'] ?? 0);
$item = $itemModel->getById($id);

if (!$item || (int)$item['user_id'] !== Auth::id()) {
    Auth::flash('error', 'You are not authorized to edit this item.');
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = [
            'title' => trim($_POST['title']),
            'cat_id' => (int) $_POST['cat_id'],
            'description' => trim($_POST['description']),
            'condition' => $_POST['condition'],
            'price' => $_POST['price'],
            'original_price' => $_POST['original_price'],
            'location' => trim($_POST['location']),
            'contact_number' => trim($_POST['contact_number']),
        ];

        if (!empty($_FILES['image']['name'])) {
            $allowed = ['jpg','jpeg','png','webp'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) { throw new Exception('Invalid image format.'); }
            $imageName = 'item_' . uniqid() . '.' . $ext;
            if (!is_dir(UPLOAD_DIR)) { mkdir(UPLOAD_DIR, 0755, true); }
            move_uploaded_file($_FILES['image']['tmp_name'], UPLOAD_DIR . $imageName);
            $data['image'] = $imageName;
        }

        $itemModel->update($id, $data);
        (new ActivityLog())->log(Auth::id(), 'Edited item: ' . $data['title']);
        Auth::flash('success', 'Item updated successfully.');
        header('Location: dashboard.php');
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$pageTitle = 'Edit Item';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
?>
<section class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="ux-card p-4 p-md-5">
        <h3 class="fw-bold mb-4"><i class="fa-solid fa-pen text-purple me-2"></i>Edit Item</h3>
        <?php if (!empty($error)): ?><div class="alert alert-danger rounded-ux"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
        <form method="POST" enctype="multipart/form-data" class="ux-form">
          <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
          <div class="row g-3">
            <div class="col-12">
              <label>Item Title *</label>
              <input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($item['title']); ?>">
            </div>
            <div class="col-md-6">
              <label>Category *</label>
              <select name="cat_id" class="form-select" required>
                <?php foreach ($categories as $cat): ?>
                  <option value="<?php echo $cat['cat_id']; ?>" <?php echo $cat['cat_id']==$item['cat_id']?'selected':''; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label>Condition *</label>
              <select name="condition" class="form-select" required>
                <?php foreach (['like_new'=>'Like New','good'=>'Good','fair'=>'Fair','poor'=>'Poor'] as $val=>$label): ?>
                  <option value="<?php echo $val; ?>" <?php echo $item['condition']==$val?'selected':''; ?>><?php echo $label; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label>Selling Price (LKR) *</label>
              <input type="number" step="0.01" name="price" class="form-control" required value="<?php echo $item['price']; ?>">
            </div>
            <div class="col-md-6">
              <label>Original Price</label>
              <input type="number" step="0.01" name="original_price" class="form-control" value="<?php echo $item['original_price']; ?>">
            </div>
            <div class="col-12">
              <label>Description *</label>
              <textarea name="description" class="form-control" rows="4" required><?php echo htmlspecialchars($item['description']); ?></textarea>
            </div>
            <div class="col-md-6">
              <label>Pickup Location *</label>
              <input type="text" name="location" class="form-control" required value="<?php echo htmlspecialchars($item['location']); ?>">
            </div>
            <div class="col-md-6">
              <label>Contact Number *</label>
              <input type="text" name="contact_number" class="form-control" required value="<?php echo htmlspecialchars($item['contact_number']); ?>">
            </div>
            <div class="col-12">
              <label>Replace Image (optional)</label>
              <input type="file" name="image" accept="image/*" class="form-control">
              <?php if ($item['image']): ?>
                <img src="<?php echo UPLOAD_URL . htmlspecialchars($item['image']); ?>" class="img-fluid rounded-ux mt-3" style="max-height:180px;">
              <?php endif; ?>
            </div>
          </div>
          <button type="submit" class="btn btn-ux-primary w-100 mt-4">Save Changes</button>
        </form>
      </div>
    </div>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
