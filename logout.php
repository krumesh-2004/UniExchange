<?php
require_once __DIR__ . '/config/config.php';
if (Auth::isLoggedIn()) {
    (new ActivityLog())->log(Auth::id(), 'Logged out');
}
(new User())->logout();
header('Location: login.php');
exit;
