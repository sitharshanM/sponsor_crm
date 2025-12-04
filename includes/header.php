<?php
// includes/header.php
require_once __DIR__ . '/auth.php';
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($pageTitle) ? $pageTitle . ' - ' : '' ?>Sponsor CRM</title>
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<header>
  <div class="header-container">
    <h1><a href="/index.php"><i class="fas fa-building"></i> Sponsor CRM</a></h1>
    <nav>
      <?php if(isLoggedIn()): ?>
        <a href="/index.php" class="<?= $currentPage == 'index.php' ? 'active' : '' ?>">
          <i class="fas fa-home"></i> Dashboard
        </a>
        <a href="/sponsors.php" class="<?= $currentPage == 'sponsors.php' ? 'active' : '' ?>">
          <i class="fas fa-users"></i> Sponsors
        </a>
        <a href="/interactions.php" class="<?= $currentPage == 'interactions.php' ? 'active' : '' ?>">
          <i class="fas fa-comments"></i> Interactions
        </a>
        <a href="/add_sponsor.php" class="<?= $currentPage == 'add_sponsor.php' ? 'active' : '' ?>">
          <i class="fas fa-plus-circle"></i> Add Sponsor
        </a>
        <a href="/import_sponsors.php" class="<?= $currentPage == 'import_sponsors.php' ? 'active' : '' ?>">
          <i class="fas fa-file-excel"></i> Import
        </a>
        <a href="/logout.php">
          <i class="fas fa-sign-out-alt"></i> Logout
        </a>
        <?php $user = getCurrentUser(); if($user): ?>
          <div class="user-info">
            <div class="user-avatar"><?= strtoupper(substr($user['username'], 0, 1)) ?></div>
            <span><?=htmlspecialchars($user['username'])?></span>
          </div>
        <?php endif; ?>
      <?php else: ?>
        <a href="/login.php">
          <i class="fas fa-sign-in-alt"></i> Login
        </a>
      <?php endif; ?>
    </nav>
  </div>
</header>
<main>
