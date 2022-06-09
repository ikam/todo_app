<?php

$pdo = require __DIR__ . '/database/database.php';
$authDAO = require './database/security.php';

$sessionId = $_COOKIE['session'];
if ($sessionId) {
    $authDAO->logout($sessionId);
    header('Location: /');
}