<?php
require_once __DIR__ . '/config/config.php';
Auth::requireLogin();

$itemId = (int) ($_POST['item_id'] ?? 0);
$redirect = $_POST['redirect'] ?? 'browse.php';
$savedModel = new SavedItem();

if ($savedModel->isSaved(Auth::id(), $itemId)) {
    $savedModel->remove(Auth::id(), $itemId);
    Auth::flash('success', 'Removed from wishlist.');
} else {
    $savedModel->save(Auth::id(), $itemId);
    Auth::flash('success', 'Added to wishlist!');
}

header('Location: ' . $redirect);
exit;
