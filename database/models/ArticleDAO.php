<?php

$pdo = require './database/database.php';

class ArticleDAO
{
    private PDOStatement $statementCreateOne;
    private PDOStatement $statementReadOne;
    private PDOStatement $statementReadAll;
    private PDOStatement $statementUpdateOne;
    private PDOStatement $statementDeleteOne;

    /**
     * @param PDO $pdo
     */
    public function __construct(private PDO $pdo)
    {

        $this->statementCreateOne = $this->pdo->prepare(query: '
        INSERT INTO article (title, category, content,image) VALUES (:title, :category, :content, :image)');

        $this->statementUpdateOne = $this->pdo->prepare(query: '
        UPDATE article SET title = :title, category = :category, content = :content, image = :image WHERE id = :id');

        $this->statementReadOne = $this->pdo->prepare(query: 'SELECT * FROM article WHERE id=:id');
        $this->statementReadAll = $this->pdo->prepare(query: 'SELECT * FROM article');
        $this->statementDeleteOne = $this->pdo->prepare(query: 'DELETE FROM article WHERE id=:id');
    }

    /**
     * Methode qui permet de créer un new article
     * On lui laisse en paramètre un tableau
     * @param array $article
     * @return array
     */
    public function createOne(array $article): array
    {
        $this->statementCreateOne->bindValue('title', $article['title']);
        $this->statementCreateOne->bindValue('category', $article['category']);
        $this->statementCreateOne->bindValue('content', $article['content']);
        $this->statementCreateOne->bindValue('image', $article['image']);
        $this->statementCreateOne->execute();
        return $this->getOne($this->pdo->lastInsertId());

    }

    /**
     * @param int $id
     * @return array
     */
    public function getOne(int $id): array
    {
        $this->statementReadOne->bindValue(':id', $id);
        $this->statementReadOne->execute();
        return $this->statementReadOne->fetch();
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        $this->statementReadAll->execute();
        return $this->statementReadAll->fetchAll();
    }

    /**
     * @param int $id
     * @return int
     */
    public function deleteOne(int $id): int
    {
        $this->statementDeleteOne->bindValue(':id', $id);
        $this->statementDeleteOne->execute();
        return $id;
    }

    /**
     * @param array $article
     * @return array
     */
    public function updateOne(array $article): array
    {
        $this->statementUpdateOne->bindValue('title', $article['title']);
        $this->statementUpdateOne->bindValue('category', $article['category']);
        $this->statementUpdateOne->bindValue('content', $article['content']);
        $this->statementUpdateOne->bindValue('image', $article['image']);
        $this->statementUpdateOne->bindValue('id', $article['id']);
        $this->statementUpdateOne->execute();
        return $this->getOne($article['id']);
    }
}

return new ArticleDAO($pdo);