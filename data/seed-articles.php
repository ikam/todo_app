<?php

$articles = json_decode(json: file_get_contents('./data/articles.json'), associative: true);
$dns = 'mysql:host=localhost:3306;dbname=blog';
$user = 'root';
$pwd = '112233';

$pdo = new PDO($dns, $user, $pwd);
$statement = $pdo->prepare(query: '
    INSERT INTO article (title, category, content, image) VALUES (:title, :category, :content, :image)
');

foreach ($articles as $article) {
    $statement->bindValue(':title', $article['title']);
    $statement->bindValue(':category', $article['category']);
    $statement->bindValue(':content', $article['content']);
    $statement->bindValue(':image', $article['image']);
    $statement->execute();
}