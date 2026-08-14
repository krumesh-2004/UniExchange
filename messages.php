<?php
require_once __DIR__ . '/config/config.php';
Auth::requireLogin();

$messageModel = new Message();
$itemModel = new Item();
$userModel = new User();

$inbox = $messageModel->getInbox(Auth::id());

$activeItemId = (int) ($_GET['item_id'] ?? 0);
$activeUserId = (int) ($_GET['user_id'] ?? 0);

if (!$activeItemId && !empty($inbox)) {
    $activeItemId = $inbox[0]['item_id'];
    $activeUserId = $inbox[0]['other_user_id'];
}

// Send reply
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $text = trim($_POST['message'] ?? '');
        $itemId = (int) $_POST['item_id'];
        $receiverId = (int) $_POST['receiver_id'];
        if ($text !== '') {
            $messageModel->send(Auth::id(), $receiverId, $itemId, $text);
        }
        header('Location: messages.php?item_id=' . $itemId . '&user_id=' . $receiverId);
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$conversation = [];
$activeItem = null;
$otherUser = null;
if ($activeItemId && $activeUserId) {
    $messageModel->markRead($activeItemId, Auth::id(), $activeUserId);
    $conversation = $messageModel->getConversation(Auth::id(), $activeUserId, $activeItemId);
    $activeItem = $itemModel->getById($activeItemId);
    $otherUser = $userModel->findById($activeUserId);
}

$pageTitle = 'Messages';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
?>
<section class="container py-5">
  <h2 class="section-title">Messages</h2>
  <div class="row g-4 mt-2">
    <!-- Thread list -->
    <div class="col-lg-4">
      <div class="sidebar-panel p-0 overflow-hidden">
        <?php if (empty($inbox)): ?>
          <p class="text-muted p-4 mb-0">No conversations yet. Message a seller from an item page to get started.</p>
        <?php endif; ?>
        <?php foreach ($inbox as $thread):
            $active = ($thread['item_id'] == $activeItemId && $thread['other_user_id'] == $activeUserId);
        ?>
          <a href="messages.php?item_id=<?php echo $thread['item_id']; ?>&user_id=<?php echo $thread['other_user_id']; ?>"
             class="d-flex align-items-center gap-3 p-3 text-decoration-none border-bottom <?php echo $active ? 'bg-soft-purple' : ''; ?>">
            <div style="width:48px;height:48px;border-radius:12px;overflow:hidden;background:#f0f0fa;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
              <?php if ($thread['item_image']): ?>
                <img src="<?php echo UPLOAD_URL . htmlspecialchars($thread['item_image']); ?>" style="width:100%;height:100%;object-fit:cover;">
              <?php else: ?><i class="fa-solid fa-book text-purple"></i><?php endif; ?>
            </div>
            <div class="flex-grow-1 overflow-hidden">
              <div class="fw-semibold text-dark text-truncate"><?php echo htmlspecialchars($thread['other_user_name']); ?></div>
              <div class="small text-muted text-truncate"><?php echo htmlspecialchars($thread['item_title']); ?></div>
              <div class="small text-muted text-truncate"><?php echo htmlspecialchars($thread['message']); ?></div>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Chat window -->
    <div class="col-lg-8">
      <?php if ($activeItem && $otherUser): ?>
      <div class="ux-card p-4">
        <div class="d-flex align-items-center gap-3 border-bottom pb-3 mb-3">
          <div style="width:45px;height:45px;border-radius:10px;overflow:hidden;background:#f0f0fa;display:flex;align-items:center;justify-content:center;">
            <?php if ($activeItem['image']): ?>
              <img src="<?php echo UPLOAD_URL . htmlspecialchars($activeItem['image']); ?>" style="width:100%;height:100%;object-fit:cover;">
            <?php else: ?><i class="fa-solid fa-book text-purple"></i><?php endif; ?>
          </div>
          <div>
            <div class="fw-bold"><?php echo htmlspecialchars($otherUser['name']); ?></div>
            <a href="item-details.php?id=<?php echo $activeItem['item_id']; ?>" class="small text-purple"><?php echo htmlspecialchars($activeItem['title']); ?></a>
          </div>
        </div>

        <div class="chat-box" id="chatBox">
          <?php foreach ($conversation as $msg):
              $isMe = $msg['sender_id'] == Auth::id();
          ?>
            <div class="chat-bubble <?php echo $isMe ? 'me' : 'them'; ?>">
              <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
              <small><?php echo date('M d, g:i A', strtotime($msg['created_at'])); ?></small>
            </div>
          <?php endforeach; ?>
        </div>

        <form method="POST" class="d-flex gap-2 mt-3">
          <input type="hidden" name="item_id" value="<?php echo $activeItem['item_id']; ?>">
          <input type="hidden" name="receiver_id" value="<?php echo $otherUser['user_id']; ?>">
          <input type="text" name="message" class="form-control" placeholder="Type your message..." required>
          <button type="submit" class="btn btn-ux-primary"><i class="fa-solid fa-paper-plane"></i></button>
        </form>
      </div>
      <?php else: ?>
        <div class="ux-card p-5 text-center text-muted">
          <i class="fa-solid fa-comments fa-3x mb-3"></i>
          <p>Select a conversation to view messages.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
