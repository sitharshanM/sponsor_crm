<?php
// Serve React app if built, otherwise redirect to login
if (file_exists(__DIR__ . '/dist/index.html')) {
    // Serve React app
    readfile(__DIR__ . '/dist/index.html');
} else {
    // Fallback: redirect to login
    header('Location: /login.php');
    exit;
}
