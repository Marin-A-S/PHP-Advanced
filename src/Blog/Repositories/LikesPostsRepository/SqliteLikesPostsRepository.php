<?php

namespace Geekbrains\Php2\Blog\Repositories\LikesPostsRepository;

use Geekbrains\Php2\Blog\Exceptions\LikeAlreadyExists;
use Geekbrains\Php2\Blog\Exceptions\LikesPostNotFoundException;
use Geekbrains\Php2\Blog\LikePost;
use Geekbrains\Php2\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use Geekbrains\Php2\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Geekbrains\Php2\Blog\UUID;
use PDO;

class SqliteLikesPostsRepository implements LikesPostsRepositoryInterface
{
    /**
     * @param PDO $connection
     */
    public function __construct(
        private PDO $connection
    ) {
    }

    public function save(LikePost $likePost): void
    {
        // Подготавливаем запрос
        $statement = $this->connection->prepare('
            INSERT INTO likes_posts (uuid, post_uuid, user_uuid) 
            VALUES (:uuid, :post_uuid, :user_uuid)
        ');

        // Выполняем запрос с конкретными значениями
        $statement->execute([
            ':uuid' => (string)$likePost->uuid(),
            ':post_uuid' => (string)$likePost->getPost()->uuid(),
            ':user_uuid' => (string)$likePost->getUser()->uuid()
        ]);
    }

    public function getByPostUuid(UUID $uuid): array
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM likes_posts WHERE post_uuid = :uuid'
        );

        $statement->execute([
            'uuid' => (string)$uuid
        ]);

        $result = $statement->fetchAll(\PDO::FETCH_ASSOC); //массив объектов

        if (!$result) {
            throw new LikesPostNotFoundException(
                'No likes to post with uuid = : ' . $uuid
            );
        }

        // получаем статью по uuid
        $postsRepository = new SqlitePostsRepository($this->connection);
        // получаем пользователя по uuid
        $usersRepository = new SqliteUsersRepository($this->connection);

        $likesPosts = [];
        foreach ($result as $likeComment) {
            $likesPosts[] = new LikePost(
                uuid: new UUID($likeComment['uuid']),
                post: $postsRepository->get(new UUID($likeComment['post_uuid'])),
                user: $usersRepository->get(new UUID($likeComment['user_uuid'])),
            );
        }
        return $likesPosts;
    }

    public function checkUserLikeForPostExists($commentUuid, $userUuid): void
    {
        $statement = $this->connection->prepare(
            'SELECT *
            FROM likes_posts
            WHERE 
                post_uuid = :postUuid AND user_uuid = :userUuid'
        );

        $statement->execute(
            [
                ':postUuid' => $commentUuid,
                ':userUuid' => $userUuid
            ]
        );

        $isExisted = $statement->fetch();

        if ($isExisted) {
            throw new LikeAlreadyExists(
                'The users like for this post already exists'
            );
        }
    }
}