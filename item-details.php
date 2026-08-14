<?php
require_once __DIR__ . '/config/config.php';
$error = null;

$itemModel = new Item();
$savedModel = new SavedItem();
$messageModel = new Message();

$id = (int) ($_GET['id'] ?? 0);
$item = $itemModel->getById($id);

if (!$item) {
    Auth::flash('error', 'Item not found.');
    header('Location: browse.php');
    exit;
}
$itemModel->incrementViews($id);

// Handle "Report Item" form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['report_reason'])) {
    Auth::requireLogin();
    try {
        $reason = trim($_POST['report_reason']);
        if ($reason === '') { throw new Exception('Please provide a reason for reporting.'); }
        (new Report())->submit($id, Auth::id(), $reason);
        Auth::flash('success', 'Thank you. This listing has been reported to the admin team.');
        header('Location: item-details.php?id=' . $id);
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Handle "Send Message" form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    Auth::requireLogin();
    try {
        $text = trim($_POST['message']);
        if ($text === '') { throw new Exception('Message cannot be empty.'); }
        if (Auth::id() == $item['user_id']) { throw new Exception('You cannot message yourself about your own item.'); }
        $messageModel->send(Auth::id(), (int)$item['user_id'], $id, $text);
        Auth::flash('success', 'Message sent to the seller!');
        header('Location: item-details.php?id=' . $id);
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$isSaved = Auth::isLoggedIn() ? $savedModel->isSaved(Auth::id(), $id) : false;

$pageTitle = $item['title'];
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
?>
<section class="container py-5">
  <?php require __DIR__ . '/includes/alerts.php'; ?>
  <?php if (!empty($error)): ?>
    <div class="alert alert-danger rounded-ux"><?php echo htmlspecialchars($error); ?></div>
  <?php endif; ?>

  <div class="row g-5">
    <div class="col-lg-5">
      <div class="ux-card rounded-ux position-relative p-0" style="aspect-ratio: 3/4; max-width: 320px; margin: 0 auto; overflow: hidden;">
        <?php if ($item['status'] === 'sold'): ?><span class="badge-sold">SOLD</span><?php endif; ?>
        <?php if ($item['image']): ?>
          <img src="<?php echo UPLOAD_URL . htmlspecialchars($item['image']); ?>" class="rounded-ux" style="width:100%; height:100%; object-fit: contain;" alt="">
        <?php else: ?>
          <i class="fa-solid fa-book" style="font-size:120px; color:#a29bfe;"></i>
        <?php endif; ?>
      </div>
    </div>
    <div class="col-lg-7">
      <span class="badge-cat"><?php echo htmlspecialchars($item['category_name']); ?></span>
      <span class="status-badge status-<?php echo $item['status']; ?> ms-2 text-capitalize"><?php echo $item['status']; ?></span>
      <h2 class="fw-bold mt-3"><?php echo htmlspecialchars($item['title']); ?></h2>
      <h3 class="text-white fw-bold">Rs. <?php echo number_format($item['price'], 2); ?>
        <?php if ($item['original_price']): ?>
          <small class="text-muted text-decoration-line-through fs-6">Rs. <?php echo number_format($item['original_price'],2); ?></small>
        <?php endif; ?>
      </h3>

      <div class="d-flex gap-3 my-3 flex-wrap">
        <span class="condition-badge text-capitalize"><i class="fa-solid fa-star me-1"></i><?php echo str_replace('_',' ',$item['condition']); ?></span>
        <span class="badge-cat"><i class="fa-solid fa-location-dot me-1"></i><?php echo htmlspecialchars($item['location']); ?></span>
        <span class="badge-cat"><i class="fa-solid fa-eye me-1"></i><?php echo $item['views']; ?> views</span>
      </div>

      <h6 class="fw-bold mt-4">Description</h6>
      <p class="text-muted"><?php echo nl2br(htmlspecialchars($item['description'])); ?></p>

      <div class="ux-card p-3 mt-3 bg-soft-purple">
        <h6 class="fw-bold mb-2"><i class="fa-solid fa-user me-2 text-purple"></i>Seller Information</h6>
        <p class="mb-1"><strong><?php echo htmlspecialchars($item['seller_name']); ?></strong></p>
        <p class="mb-1 small text-muted"><?php echo htmlspecialchars($item['seller_faculty'] ?? ''); ?> <?php echo $item['seller_year'] ? '&middot; '.htmlspecialchars($item['seller_year']) : ''; ?></p>
        <p class="mb-0 small"><i class="fa-solid fa-phone me-1"></i><?php echo htmlspecialchars($item['contact_number']); ?></p>
      </div>

      <div class="d-flex gap-2 mt-4">
        <?php if (Auth::isLoggedIn()): ?>
          <form method="POST" action="save-item.php">
            <input type="hidden" name="item_id" value="<?php echo $id; ?>">
            <input type="hidden" name="redirect" value="item-details.php?id=<?php echo $id; ?>">
            <button type="submit" class="btn <?php echo $isSaved ? 'btn-ux-danger' : 'btn-ux-outline'; ?>">
              <i class="fa-<?php echo $isSaved?'solid':'regular'; ?> fa-heart me-1"></i> <?php echo $isSaved ? 'Saved' : 'Save to Wishlist'; ?>
            </button>
          </form>
        <?php endif; ?>
        <?php if (Auth::isLoggedIn() && Auth::id() != $item['user_id']): ?>
          <button type="button" class="btn btn-ux-outline" data-bs-toggle="modal" data-bs-target="#reportModal">
            <i class="fa-solid fa-flag me-1"></i> Report
          </button>
        <?php endif; ?>
      </div>

      <?php if ($item['status'] === 'available'): ?>
      <div class="ux-card p-4 mt-4">
        <h6 class="fw-bold mb-3"><i class="fa-solid fa-comment-dots me-2 text-purple"></i>Message Seller</h6>
        <?php if (!Auth::isLoggedIn()): ?>
          <p class="text-muted">Please <a href="login.php" class="text-purple fw-bold">login</a> to contact the seller.</p>
        <?php elseif (Auth::id() == $item['user_id']): ?>
          <p class="text-muted mb-0">This is your own listing.</p>
        <?php else: ?>
          <form method="POST" class="ux-form">
            <textarea name="message" class="form-control" rows="3" placeholder="Hi, is this item still available?" required></textarea>
            <button type="submit" class="btn btn-ux-primary mt-3"><i class="fa-solid fa-paper-plane me-1"></i>Send Message</button>
          </form>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- Report Modal -->
<div class="modal fade" id="reportModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content rounded-ux">
      <form method="POST">
        <div class="modal-header">
          <h5 class="modal-title fw-bold"><i class="fa-solid fa-flag text-purple me-2"></i>Report Listing</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <label class="fw-semibold mb-2">Why are you reporting this listing?</label>
          <textarea name="report_reason" class="form-control" rows="3" placeholder="e.g. Misleading description, prohibited item, spam..." required></textarea>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-ux-outline" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-ux-danger">Submit Report</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
