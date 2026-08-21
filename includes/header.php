<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Base URL for consistent linking
$base_url = '/Online-Job-Portal-System';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Job Portal</title>
    <!-- CSS Dependencies -->
    <link rel="stylesheet" href="<?= $base_url ?>/css/variables.css">
    <link rel="stylesheet" href="<?= $base_url ?>/css/base.css">
    <link rel="stylesheet" href="<?= $base_url ?>/css/layout.css">
    <link rel="stylesheet" href="<?= $base_url ?>/css/forms.css">
    <link rel="stylesheet" href="<?= $base_url ?>/css/tables.css">
    <link rel="stylesheet" href="<?= $base_url ?>/css/components.css">
</head>
<body>
    <header class="site-header">
        <div class="container header-container">
            <a href="<?= $base_url ?>/jobs/index.php" class="brand-logo">JobPortal</a>
            <nav class="site-nav">
                <ul>
                    <li><a href="<?= $base_url ?>/jobs/index.php">Browse Jobs</a></li>
                    <?php if (isset($_SESSION['admin_id'])): ?>
                        <li><a href="<?= $base_url ?>/admin/dashboard.php">Dashboard</a></li>
                        <li><a href="<?= $base_url ?>/admin/view_applications.php">Applications</a></li>
                        <li><a href="<?= $base_url ?>/admin/logout.php" class="btn btn-outline">Logout</a></li>
                    <?php else: ?>
                        <li><a href="<?= $base_url ?>/admin/login.php" class="btn btn-outline">Admin Login</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container main-content">