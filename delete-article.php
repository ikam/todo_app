<?php

$filename = __DIR__ . '/data/articles.json';
$articles = [];

$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$id = $_GET['id'] ?? '';

if (!$id) {
    header('Location: /');
} else if (file_exists($filename)) {
    $articles = json_decode(json: file_get_contents($filename), associative: true) ?? [];
    $articleIdx = array_search($id, haystack: array_column($articles, 'id'));
    array_splice($articles, $articleIdx, 1);
    file_put_contents($filename, data: json_encode($articles));
    header('Location: /');
}
