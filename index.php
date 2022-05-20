<?php
const ERROR_REQUEST = "Veuillez remplir ce champ";
const ERROR_TOO_SHORT = 'Veuillez entrer au moins 5 caractères';

$filename = __DIR__ . '/data/todos.json';
$error = '';
$t = '';
$todos = [];

if (file_exists($filename)) {
    $data = file_get_contents($filename);
    $todos = json_decode(json: $data, associative: true) ?? [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $t = $_POST['todo'] ?? '';
    if (!$t) {
        $error = ERROR_REQUEST;
    } elseif (mb_strlen($t) < 5) {
        $error = ERROR_TOO_SHORT;
    }

    if (!$error) {
        $todos = [...$todos, ['name' => $t, 'done' => false, 'id' => time()]];
        file_put_contents($filename, data: json_encode(value: $todos));
        header('Location: /');
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once 'includes/head.php' ?>
    <title>TODO APP</title>
</head>

<body>

<div class="container">
    <?php require_once 'includes/header.php' ?>
    <div class="content">
        <div class="todo-container">
            <h1>Ma todo liste</h1>
            <form action="/" method="post" class="todo-form">
                <label for="todo"></label>
                <!--                <input type="text" name="todo" id="todo" placeholder="">-->
                <input value="<?= $t ?>" type="text" name="todo" id="todo">
                <button class="btn btn-primary">Ajouter</button>
            </form>
            <?php if ($error) : ?>
                <p class="text-danger"><?= $error ?></p>
            <?php endif; ?>
            <ul class="todo-list">

                <?php foreach ($todos as $t) : ?>
                    <li class="todo-item <?= $t['done'] ? 'low-opacity' : '' ?>">
                        <span class="todo-name">
                            <?= $t['name'] ?>
                        </span>
                        <a href="/edit-todo.php?id=<?php echo $t['id'] ?>">
                            <button class="btn btn-primary btn-small"><?= $t['done'] ? 'Annuler' : 'Valider' ?></button>
                        </a>
                        <a href="/delete-todo.php?id=<?php echo $t['id'] ?>">
                            <button class="btn btn-danger btn-small">Supprimer</button>
                        </a>
                        <!--                        <a href="/modify-todo.php?id=--><?php //echo $t['id'] ?><!--">-->
                        <!--                            <button class="btn btn-danger btn-small">Modifier</button>-->
                        <!--                        </a>-->
                    </li>

                <?php endforeach; ?>

            </ul>
        </div>
    </div>
    <?php require_once 'includes/footer.php' ?>
</div>

</body>

</html>