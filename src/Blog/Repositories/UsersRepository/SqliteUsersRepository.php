<?php

namespace Geekbrains\Php2\Blog\Repositories\UsersRepository;

use Geekbrains\Php2\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\Php2\Blog\Exceptions\UserNotFoundException;
use Geekbrains\Php2\Blog\UUID;
use Geekbrains\Php2\Person\{Name, User};
use PDO;
use PDOStatement;

class SqliteUsersRepository implements UsersRepositoryInterface
{
    /**
     * @param PDO $connection
     */
    public function __construct(
        private PDO $connection
    ) {
    }

    /**
     * @param User $user
     * @return void
     */
    public function save(User $user): void
    {
        // Подготавливаем запрос
        $statement = $this->connection->prepare(
            'INSERT INTO users (uuid, first_name, last_name, username) VALUES (:uuid, :first_name, :last_name, :username)'
        );

        // Выполняем запрос с конкретными значениями
        $statement->execute([
            ':uuid' => (string)$user->uuid(),
            ':first_name' => $user->getName()->getFirstName(),
            ':last_name' => $user->getName()->getLastName(),
            ':username' => $user->getUsername()
        ]);
    }

    // Метод для получения пользователя по его UUID
    /**
     * @param UUID $uuid
     * @return User
     * @throws UserNotFoundException
     * @throws InvalidArgumentException
     */
    public function get(UUID $uuid): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE uuid = ?'
        );

        $statement->execute([(string)$uuid]);
        return $this->getUser($statement, $uuid);
    }

    // Метод получения пользователя по username
    /**
     * @param string $username
     * @return User
     * @throws InvalidArgumentException
     * @throws UserNotFoundException
     */
    public function getByUsername(string $username): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE username = :username'
        );
        $statement->execute([
            ':username' => $username,
        ]);
        return $this->getUser($statement, $username);
    }

    // Вынесли общую логику в отдельный приватный метод
    /**
     * @throws InvalidArgumentException
     * @throws UserNotFoundException
     */
    private function getUser(bool|PDOStatement $statement, string $str): User
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if ($result === false) {
            throw new UserNotFoundException(
                "Cannot find user: $str"
            );
        }
        // Создаём объект пользователя с полем username
        return new User(
            new UUID($result['uuid']),
            new Name($result['first_name'], $result['last_name']),
            $result['username']
        );
    }

    public function delete(UUID $userUuid): void
    {
        // Подготавливаем запрос
        $statement = $this->connection->prepare(
            'DELETE FROM users WHERE uuid=:uuid'
        );

        // Выполняем запрос с конкретными значениями
        $statement->execute([
            ':uuid' => (string)$userUuid,
        ]);
    }
}
