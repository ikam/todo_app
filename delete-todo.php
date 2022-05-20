<?php

$filename = __DIR__ . '/data/todos.json';

$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$id = $_GET['id'] ?? '';

if ($id) {
    $todos = json_decode(json: file_get_contents($filename), associative: true) ?? [];
    $todoIndex= array_search($id, haystack: array_column($todos, 'id'));
    array_splice($todos, offset: $todoIndex, length: 1);
    file_put_contents($filename, data: json_encode($todos));
}

header('location: /');