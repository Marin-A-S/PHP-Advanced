<?php

namespace Geekbrains\Php2\Blog\Repositories\LikesCommentsRepository;

use Geekbrains\Php2\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\Php2\Blog\Exceptions\LikeAlreadyExists;
use Geekbrains\Php2\Blog\Exceptions\LikesCommentNotFoundException;
use Geekbrains\Php2\Blog\LikeComment;
use Geekbrains\Php2\Blog\UUID;
use PDO;

class SqliteLikesCommentsRepository implements LikesCommentsRepositoryInterface
{
    /**
     * @param PDO $connection
     */
    public function __construct(
        private PDO $connection
    ) {
    }

    // сохраняем комментарий в базе данных
    /**
     * @param LikeComment $likeComment
     * @return void
     */
    public function save(LikeComment $likeComment): void
    {
        // Подготавливаем запрос
        $statement = $this->connection->prepare('
                INSERT INTO likes_comments (uuid, comment_uuid, post_uuid, user_uuid) 
                VALUES (:uuid, :comment_uuid, :post_uuid, :user_uuid)
        ');

        // Выполняем запрос с конкретными значениями
        $statement->execute([
            ':uuid' => (string)$likeComment->uuid(),
            ':comment_uuid' => (string)$likeComment->getCommentUuid(),
            ':post_uuid' => (string)$likeComment->getPostUuid(),
            ':user_uuid' => (string)$likeComment->getUserUuid()
        ]);
    }

    // получить комментарий из базы данных
    /**
     * @param UUID $uuid
     * @return array
     * @throws LikesCommentNotFoundException
     * @throws InvalidArgumentException
     */
    public function getByCommentUuid(UUID $uuid): array
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM likes_comments WHERE comment_uuid = :uuid'
        );

        $statement->execute([
            'uuid' => (string)$uuid
        ]);

        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);

        if (!$result) {
            throw new LikesCommentNotFoundException(
                'No likes to post with uuid = : ' . $uuid
            );
        }

        $likesComments = [];
        foreach ($result as $likeComments) {
            $likesComments[] = new LikeComment(
                uuid: new UUID($likeComments['uuid']),
                comment_uuid: new UUID($likeComments['comment_uuid']),
                post_uuid: new UUID($likeComments['post_uuid']),
                user_uuid: new UUID($likeComments['user_uuid']),
            );
        }

        return $likesComments;
    }

    /**
     * @param $commentUuid
     * @param $userUuid
     * @return void
     * @throws LikeAlreadyExists
     */
    public function checkUserLikeForCommentExists($commentUuid, $userUuid): void
    {
        $statement = $this->connection->prepare(
            'SELECT *
            FROM likes_comments
            WHERE 
                comment_uuid = :commentUuid AND user_uuid = :userUuid'
        );

        $statement->execute(
            [
                ':commentUuid' => $commentUuid,
                ':userUuid' => $userUuid
            ]
        );

        $isExisted = $statement->fetch();

        if ($isExisted) {
            throw new LikeAlreadyExists(
                'The users like for this comment already exists'
            );
        }
    }
}