<?php
// includes/header.php
require_once __DIR__ . '/auth.php';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Sponsor CRM</title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<header>
  <h1><a href="/index.php">Sponsor CRM</a></h1>
  <nav>
    <?php if(isLoggedIn()): ?>
      <a href="/index.php">Dashboard</a> |
      <a href="/sponsors.php">Sponsors</a> |
      <a href="/interactions.php">Interactions</a> |
      <a href="/add_sponsor.php">Add Sponsor</a> |
      <a href="/import_sponsors.php">Import Excel</a> |
      <a href="/logout.php">Logout</a>
      <?php $user = getCurrentUser(); if($user): ?>
        <span style="float:right;">Logged in as: <?=htmlspecialchars($user['username'])?></span>
      <?php endif; ?>
    <?php else: ?>
      <a href="/login.php">Login</a>
    <?php endif; ?>
  </nav>
  <hr/>
</header>
<main>
