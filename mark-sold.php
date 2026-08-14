<?php
require_once __DIR__ . '/config/config.php';
Auth::requireLogin();

$itemModel = new Item();
$id = (int) ($_GET['id'] ?? 0);

if ($itemModel->markAsSold($id, Auth::id())) {
    (new ActivityLog())->log(Auth::id(), 'Marked item #' . $id . ' as sold');
    Auth::flash('success', 'Item marked as sold!');
} else {
    Auth::flash('error', 'Could not update item status.');
}

header('Location: dashboard.php');
exit;
