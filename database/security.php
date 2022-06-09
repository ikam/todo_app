<?php

$pdo = require './database/database.php';

class AuthDAO
{

    public const SECRET_KEY = 'formation dwwwm au top grace a rachid';
    private PDOStatement $statementRegistration;
    private PDOStatement $statementReadSession;
    private PDOStatement $statementReadUser;
    private PDOStatement $statementReadUserFromEmail;
    private PDOStatement $statementCreateSession;
    private PDOStatement $statementDeleteSession;

    /**
     * @param PDO $pdo
     */
    public function __construct(private PDO $pdo)
    {
        $this->statementRegistration = $this->pdo->prepare(
            query: 'INSERT INTO user (firstname, lastname, email, password) VALUES (:firstname, :lastname, :email, :password)'
        );
        $this->statementReadSession = $this->pdo->prepare(query: 'SELECT * FROM session WHERE id=:id');
        $this->statementReadUser = $this->pdo->prepare(query: 'SELECT * FROM user WHERE id=:id');
        $this->statementReadUserFromEmail = $this->pdo->prepare(query: 'SELECT * FROM user WHERE email=:email');
        $this->statementCreateSession = $this->pdo->prepare(query: 'INSERT INTO session VALUES (:sessionId, :userid)');
        $this->statementDeleteSession = $this->pdo->prepare(query: 'DELETE FROM session WHERE id=:id');
    }


    /**
     * @param string $email
     * @return array|false
     */
    public function getUserFromEmail(string $email): bool|array
    {
        $this->statementReadUserFromEmail->bindValue(':email', $email);
        $this->statementReadUserFromEmail->execute();
        return $this->statementReadUserFromEmail->fetch() ?? false;
    }


    /**
     * @param int $userId
     * @return void
     * @throws Exception
     */
    public function login(int $userId): void
    {

        $sessionId = bin2hex(random_bytes(32));

        $this->statementCreateSession->bindValue(':sessionId', $sessionId);
        $this->statementCreateSession->bindValue(':userid', $userId);
        $this->statementCreateSession->execute();

        $signature = hash_hmac('sha256', $sessionId, AuthDAO::SECRET_KEY);

        // Cookie
        setcookie('session', $sessionId, time() + 60 * 60 * 24 * 14, '', '', false, true);
        setcookie('signature', $signature, time() + 60 * 60 * 24 * 14, '', '', false, true);
    }

    /**
     * @param array $user
     * @return void
     */
    public function register(array $user): void
    {

        $hashPassword = password_hash($user['password'], PASSWORD_ARGON2I);
        $this->statementRegistration->bindValue(':firstname', $user['firstname']);
        $this->statementRegistration->bindValue(':lastname', $user['lastname']);
        $this->statementRegistration->bindValue(':email', $user['email']);
        $this->statementRegistration->bindValue(':password', $hashPassword);
        $this->statementRegistration->execute();
    }

    /**
     * @return array|false
     */
    public function isLoggedIn(): array|false
    {
        $sessionId = $_COOKIE['session'] ?? '';
        $signature = $_COOKIE['signature'] ?? '';

        if ($sessionId && $signature) {
            $hash = hash_hmac('sha256', $sessionId, AuthDAO::SECRET_KEY);

            if (hash_equals($hash, $signature)) {
                $this->statementReadSession->bindValue(':id', $sessionId);
                $this->statementReadSession->execute();

                $session = $this->statementReadSession->fetch();

                if ($session) {
                    $this->statementReadUser->bindValue(':id', $session['userid']);
                    $this->statementReadUser->execute();
                    $user = $this->statementReadUser->fetch();
                }
            }

        }

        return $user ?? false;
    }

    /**
     * @param string $sessionId
     * @return void
     */
    public function logout(string $sessionId): void
    {
        $this->statementDeleteSession->bindValue(':id', $sessionId);
        $this->statementDeleteSession->execute();

        setcookie('session', '', time() - 1);
        setcookie('signature', '', time() - 1);
    }
}

return new AuthDAO($pdo);