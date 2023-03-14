<?php

namespace Geekbrains\Php2\Http\Actions\Comment;

use Geekbrains\Php2\Blog\Exceptions\PostNotFoundException;
use Geekbrains\Php2\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use Geekbrains\Php2\Blog\UUID;
use Geekbrains\Php2\Http\Actions\ActionInterface;
use Geekbrains\Php2\Http\ErrorResponse;
use Geekbrains\Php2\Http\Request;
use Geekbrains\Php2\Http\Response;
use Geekbrains\Php2\Http\SuccessfulResponse;

class DeleteComment implements ActionInterface
{
    // Внедряем репозитории комментариев
    public function __construct(
        private CommentsRepositoryInterface $commentsRepository,
    )  {
    }

    public function handle(Request $request): Response
    {
        try {
            $commentUuid = $request->query('uuid'); // получаем из запроса значение параметра
            $this->commentsRepository->get(new UUID($commentUuid)); // проверяем есть ли такая статья в репозитории

        } catch (PostNotFoundException $error) {
            return new ErrorResponse($error->getMessage());
        }

        $this->commentsRepository->delete(new UUID($commentUuid)); // удаляем статью из репозитория

        return new SuccessfulResponse([
            'uuid' => $commentUuid,
        ]);
    }
}