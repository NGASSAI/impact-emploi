<?php
require 'config.php';

try {
    $sql = "SELECT COUNT(*) FROM users WHERE `role`='admin'";
    $count = $pdo->query($sql)->fetchColumn();
    echo "✓ Backtick works! Count: " . $count;
} catch(Exception $e) {
    echo "✗ Error: " . $e->getMessage();
}
?>
