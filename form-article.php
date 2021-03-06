<?php

require_once __DIR__ . '/database/database.php';
$authDAO = require __DIR__ . '/database/security.php';
$currentUser = $authDAO->isLoggedIn();

if (!$currentUser) {
    header('Location: /');
}

/**
 * @var ArticleDAO
 */

$articleDAO = require './database/models/ArticleDAO.php';

const ERROR_REQUIRED = "Veuillez renseigner ce champ !";
const ERROR_TITLE_TOO_SHORT = "Le titre est trop court !";
const ERROR_CONTENT_TOO_SHORT = "L'article est trop court !";
const ERROR_IMAGE_URL = "L'image doit être une URL valide !";

$category = '';

$errors = [
    'title' => '',
    'image' => '',
    'category' => '',
    'content' => ''
];

$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$id = $_GET['id'] ?? '';

if ($id) {

    $article = $articleDAO->getOne($id);

    if ($article['author'] !== $currentUser['id']) {
        header('Location: /');
    }

    $title = $article['title'];
    $image = $article['image'];
    $category = $article['category'];
    $content = $article['content'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $_POST = filter_input_array(INPUT_POST, [
        'title' => FILTER_SANITIZE_SPECIAL_CHARS,
        'image' => FILTER_SANITIZE_URL,
        'category' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'content' => [
            'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'flag' => FILTER_FLAG_NO_ENCODE_QUOTES
        ]
    ]);

    $title = $_POST['title'] ?? '';
    $image = $_POST['image'] ?? '';
    $category = $_POST['category'] ?? '';
    $content = $_POST['content'] ?? '';

    if (!$title) {
        $errors['title'] = ERROR_REQUIRED;
    } elseif (mb_strlen($title) < 5) {
        $errors['title'] = ERROR_TITLE_TOO_SHORT;
    }

    if (!$image) {
        $errors['image'] = ERROR_REQUIRED;
    } elseif (!filter_var($image, FILTER_VALIDATE_URL)) {
        $errors['image'] = ERROR_IMAGE_URL;
    }

    if (!$category) {
        $errors['category'] = ERROR_REQUIRED;
    }

    if (!$content) {
        $errors['content'] = ERROR_REQUIRED;
    } elseif (mb_strlen($content) < 20) {
        $errors['content'] = ERROR_CONTENT_TOO_SHORT;
    }

    if (empty(array_filter($errors, fn($e) => $e !== ''))) {

        if ($id) {

            $articleDAO->updateOne([
                'title' => $title,
                'category' => $category,
                'content' => $content,
                'image' => $image,
                'id' => $id,
                'author' => $currentUser['id']
            ]);
        } else {

            $articleDAO->createOne([
                'title' => $title,
                'category' => $category,
                'content' => $content,
                'image' => $image,
                'author' => $currentUser['id']
            ]);
        }

        header('Location: /');
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require_once 'includes/head.php' ?>
    <!-- <link rel="stylesheet" href="public/css/form-article.css"> -->
    <title><?= $id ? 'Editer' : 'Créer' ?> un article</title>
</head>
<body>
<div class="container">
    <?php require_once 'includes/header.php' ?>
    <div class="content">
        <div class="block p-20 form-container">
            <h1><?= $id ? 'Modifier ' : 'Créer ' ?>un article</h1>
            <form action="/form-article.php<?= $id ? "?id=$id" : '' ?>" method="POST">
                <div class="form-control">
                    <label for="title">Titre</label>
                    <input type="text" name="title" id="title" value="<?= $title ?? '' ?>">
                    <?php if ($errors['title']) : ?>
                        <p class="text-danger"><?= $errors['title'] ?></p>
                    <?php endif; ?>
                </div>
                <div class="form-control">
                    <label for="title">Image</label>
                    <input type="text" name="image" id="image" value="<?= $image ?? '' ?>"><label
                            for="image"><?php if ($errors['image']) : ?></label>
                    <p class="text-danger"><?= $errors['image'] ?></p>
                    <?php endif; ?>
                </div>
                <div class="form-control">
                    <label for="title">Catégorie</label>
                    <select name="category" id="category">
                        <option <?= !$category || $category === 'Technologie' ? 'selected' : '' ?> value="Technologie">
                            Technologie
                        </option>
                        <option <?= !$category || $category === 'Nature' ? 'selected' : '' ?> value="Nature">Nature
                        </option>
                        <option <?= !$category || $category === 'Politique' ? 'selected' : '' ?> value="Politique">
                            Politique
                        </option>
                    </select><label for="category"><?php if ($errors['category']) : ?></label>
                    <p class="text-danger"><?= $errors['category'] ?></p>
                    <?php endif; ?>
                </div>
                <div class="form-control">
                    <label for="title">Contenu</label>
                    <textarea name="content" id="content"><?= $content ?? '' ?></textarea><label
                            for="content"><?php if ($errors['content']) : ?></label>
                    <p class="text-danger"><?= $errors['content'] ?></p>
                    <?php endif; ?>
                </div>
                <div class="form-action">
                    <a href='/' class="btn" type="button">Annuler</a>
                    <button class="btn btn-primary" type="submit"><?= $id ? 'Sauvegarder ' : 'Publier ' ?></button>
                </div>
            </form>
        </div>
    </div>
    <?php require_once 'includes/footer.php' ?>
</div>
</body>
</html>