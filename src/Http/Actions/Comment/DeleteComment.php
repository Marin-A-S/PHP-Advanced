<?php

namespace Geekbrains\Php2\Http\Actions\Comment;

use Geekbrains\Php2\Blog\Exceptions\AuthException;
use Geekbrains\Php2\Blog\Exceptions\PostNotFoundException;
use Geekbrains\Php2\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use Geekbrains\Php2\Blog\UUID;
use Geekbrains\Php2\Http\Actions\ActionInterface;
use Geekbrains\Php2\Http\Auth\TokenAuthenticationInterface;
use Geekbrains\Php2\Http\ErrorResponse;
use Geekbrains\Php2\Http\Request;
use Geekbrains\Php2\Http\Response;
use Geekbrains\Php2\Http\SuccessfulResponse;

class DeleteComment implements ActionInterface
{
    // Внедряем репозитории комментариев
    public function __construct(
        private CommentsRepositoryInterface  $commentsRepository,
        private TokenAuthenticationInterface $authentication,
    ) {
    }

    public function handle(Request $request): Response
    {
        // Идентифицируем пользователя по токену
        try {
            $this->authentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $commentUuid = $request->query('uuid'); // получаем из запроса значение параметра
            $this->commentsRepository->get(new UUID($commentUuid)); // проверяем есть ли такая статья в репозитории

        } catch (PostNotFoundException $error) {
            return new ErrorResponse($error->getMessage());
        }

        $this->commentsRepository->delete(new UUID($commentUuid)); // удаляем комментарий из репозитория

        return new SuccessfulResponse([
            'uuid' => $commentUuid,
        ]);
    }
}