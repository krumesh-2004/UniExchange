<?php
require_once __DIR__ . '/config/config.php';
$hash = '$2y$10$GcFG92Oz..seQmoTF7sd4eB/vHIlowESaWAD9JCIjT2i0xjuQuR1u';
$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("UPDATE users SET password = ? WHERE email = ?");
$stmt->execute([$hash, 'admin@gmail.com']);
echo 'updated';
