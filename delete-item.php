<?php
require_once __DIR__ . '/config/config.php';
Auth::requireLogin();

$itemModel = new Item();
$id = (int) ($_GET['id'] ?? 0);

if ($itemModel->isOwner($id, Auth::id())) {
    $itemModel->delete($id);
    (new ActivityLog())->log(Auth::id(), 'Deleted item #' . $id);
    Auth::flash('success', 'Item deleted successfully.');
} else {
    Auth::flash('error', 'You are not authorized to delete this item.');
}

header('Location: dashboard.php');
exit;
