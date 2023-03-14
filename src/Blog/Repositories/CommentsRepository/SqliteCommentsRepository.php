<?php

namespace Geekbrains\Php2\Blog\Repositories\CommentsRepository;

use Geekbrains\Php2\Blog\Comment;
use Geekbrains\Php2\Blog\Exceptions\CommentNotFoundException;
use Geekbrains\Php2\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\Php2\Blog\Exceptions\UserNotFoundException;
use Geekbrains\Php2\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use Geekbrains\Php2\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Geekbrains\Php2\Blog\UUID;
use PDO;
use PDOStatement;

class SqliteCommentsRepository implements CommentsRepositoryInterface
{
    /**
     * @param PDO $connection
     */
    public function __construct(
        private PDO $connection
    ) {
    }

    /**
     * @param Comment $comment
     * @return void
     */
    public function save(Comment $comment): void
    {
        // Подготавливаем запрос
        $statement = $this->connection->prepare(
            'INSERT INTO comments (uuid, post_uuid, user_uuid, text) VALUES (:uuid, :post_uuid, :user_uuid, :text)'
        );

        // Выполняем запрос с конкретными значениями
        $statement->execute([
            ':uuid' => (string)$comment->uuid(),
            ':post_uuid' => $comment->getPost()->uuid(),
            ':user_uuid' => $comment->getUser()->uuid(),
            ':text' => $comment->getText()
        ]);
    }

    /**
     * @param UUID $uuid
     * @return Comment
     * @throws CommentNotFoundException
     */
    public function get(UUID $uuid): Comment
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM comments WHERE uuid = ?'
        );
        $statement->execute([(string)$uuid]);
        return $this->getComment($statement, $uuid);
    }

    /**
     * @param bool|PDOStatement $statement
     * @param string $commentUuid
     * @return Comment
     * @throws CommentNotFoundException
     * @throws InvalidArgumentException
     * @throws UserNotFoundException
     */
    private function getComment(bool|PDOStatement $statement, string $commentUuid): Comment
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if ($result === false) {
            throw new CommentNotFoundException(
                "Cannot find comment: $commentUuid"
            );
        }

        // получаем пользователя по uuid
        $usersRepository = new SqliteUsersRepository($this->connection);
        $user = $usersRepository->get(new UUID($result['user_uuid']));

        // получаем статью по uuid
        $postsRepository = new SqlitePostsRepository($this->connection);
        $post = $postsRepository->get(new UUID($result['post_uuid']));

        // Создаём объект пользователя с полем username
        return new Comment(
            new UUID($result['uuid']),
            $user,
            $post,
            $result['text']
        );
    }

    public function delete(UUID $commentUuid): void
    {
        // Подготавливаем запрос
        $statement = $this->connection->prepare(
            'DELETE FROM comments WHERE uuid=:uuid'
        );

        // Выполняем запрос с конкретными значениями
        $statement->execute([
            ':uuid' => (string)$commentUuid,
        ]);
    }
}