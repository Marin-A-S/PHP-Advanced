<?php

namespace Geekbrains\Php2\Http\Actions\LikeComment;

use Geekbrains\Php2\Blog\Exceptions\HttpException;
use Geekbrains\Php2\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\Php2\Blog\Exceptions\LikesCommentNotFoundException;
use Geekbrains\Php2\Blog\Repositories\LikesCommentsRepository\LikesCommentsRepositoryInterface;
use Geekbrains\Php2\Blog\UUID;
use Geekbrains\Php2\Http\Actions\ActionInterface;
use Geekbrains\Php2\Http\ErrorResponse;
use Geekbrains\Php2\Http\Request;
use Geekbrains\Php2\Http\Response;
use Geekbrains\Php2\Http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class FindLikeCommentByUuid implements ActionInterface
{
    public function __construct(
        private LikesCommentsRepositoryInterface $likesCommentsRepository,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function handle(Request $request): Response
    {
        try {
            // получаем uuid из запроса
            $uuid = $request->query('comment_uuid');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            // ищем статью в репозитории
            $likes = $this->likesCommentsRepository->getByCommentUuid(new UUID($uuid));
        } catch (LikesCommentNotFoundException $e) {
            $this->logger->warning("Cannot find like comment: $uuid");
            // Если статья не найден - возвращаем неуспешный ответ
            return new ErrorResponse($e->getMessage());
        }

        // Возвращаем успешный ответ
        $likesComments = [];
        foreach ($likes as $key => $value) {
            $likesComments [$key] = ([
                'uuid' => (string)new UUID($value->uuid()),
                'comment' => (string)new UUID($value->getCommentUuid()),
                'post' => (string)new UUID($value->getPostUuid()),
                'user' => (string)new UUID($value->getUserUuid()),
            ]);
        }
        return new SuccessfulResponse($likesComments);
    }
}