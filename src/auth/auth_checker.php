<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit();
}
?>