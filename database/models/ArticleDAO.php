<?php

$pdo = require __DIR__ . '/../database.php';

class ArticleDAO
{
    private PDOStatement $statementCreateOne;
    private PDOStatement $statementReadOne;
    private PDOStatement $statementReadAll;
    private PDOStatement $statementDeleteOne;
    private PDOStatement $statementUpdateOne;

    /**
     * @param PDO $pdo
     */
    public function __construct(private PDO $pdo) // $this->pdo = $pdo;
    {
        $this->statementCreateOne = $this->pdo->prepare('
            INSERT INTO article (title, category, content, image, author) VALUES (:title, :category, :content, :image, :author)
        ');
        $this->statementUpdateOne = $this->pdo->prepare('
            UPDATE article SET title=:title, category=:category, content=:content, image=:image, author=:author WHERE id=:id
        ');
        $this->statementReadOne = $this->pdo->prepare('
            SELECT article.*, user.firstname, user.lastname FROM article LEFT JOIN user ON article.author = user.id WHERE article.id=:id
        ');
        $this->statementReadAll = $this->pdo->prepare('
            SELECT article.*, user.firstname, user.lastname FROM article LEFT JOIN user ON article.author = user.id
        ');
        $this->statementDeleteOne = $this->pdo->prepare('
            DELETE FROM article WHERE id=:id
        ');
    }


    /**
     * @param array $article
     * @return array
     */
    public function createOne(array $article): array
    {
        $this->statementCreateOne->bindValue(':title', $article['title']);
        $this->statementCreateOne->bindValue(':category', $article['category']);
        $this->statementCreateOne->bindValue(':content', $article['content']);
        $this->statementCreateOne->bindValue(':image', $article['image']);
        $this->statementCreateOne->bindValue(':author', $article['author']);
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
        $this->statementUpdateOne->bindValue(':title', $article['title']);
        $this->statementUpdateOne->bindValue(':category', $article['category']);
        $this->statementUpdateOne->bindValue(':content', $article['content']);
        $this->statementUpdateOne->bindValue(':image', $article['image']);
        $this->statementUpdateOne->bindValue(':author', $article['author']);
        $this->statementUpdateOne->bindValue(':id', $article['id']);
        $this->statementUpdateOne->execute();
        return $this->getOne($article['id']);
    }
}

return new ArticleDAO($pdo);