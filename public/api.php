<?php
// API entry point
$_SERVER['REQUEST_URI'] = str_replace('/api.php', '', $_SERVER['REQUEST_URI']);
require __DIR__ . '/../api/index.php';

