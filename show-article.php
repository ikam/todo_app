<?php

$articleDAO = require './database/models/ArticleDAO.php';

$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$id = $_GET['id'] ?? '';

if (!$id) {
    header('Location: /');
} else {
   $article = $articleDAO->getOne($id);
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require_once 'includes/head.php' ?>
    <link rel="stylesheet" href="public/css/show-article.css">
    <title>Affichage de l'article</title>
</head>
<body>
<div class="container">
    <?php require_once 'includes/header.php' ?>
    <div class="content">
        <div class="article-container">
            <a href="/" class="article-back">Retour à la liste des articles</a>
            <div class="article-cover-img" style="background-image: url(<?= $article['image'] ?>);"></div>
            <h1 class="article-title"><?= $article['title'] ?></h1>
            <div class="separator"></div>
            <p class="article-content"><?= $article['content'] ?></p>
            <div class="action">
                <a class="btn btn-danger" href="/delete-article.php?id=<?= $article['id'] ?>">Supprimer</a>
                <a class="btn btn-primary" href="/form-article.php?id=<?= $article['id'] ?>">Modifier l'article</a>
            </div>
        </div>
    </div>
    <?php require_once 'includes/footer.php' ?>
</div>
</body>
</html>