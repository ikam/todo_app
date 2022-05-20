<?php

$filename = __DIR__ . '/data/todos.json';

//header('Location: /');
//print_r($_GET);
$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$id = $_GET['id'] ?? '';

if ($id) {
    $data = file_get_contents($filename);
    $todos = json_decode($data, true) ?? [];

    if (count($todos)) {
        $todoIndex = array_search($id, haystack: array_column($todos, column_key: 'id'));
        $todos[$todoIndex] ['done'] = !$todos[$todoIndex]['done'];
        file_put_contents($filename, json_encode($todos));
    }
}

header('location: /');