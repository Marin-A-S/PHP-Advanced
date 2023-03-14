<?php

namespace Geekbrains\Php2\Http\Actions\Post;

use Geekbrains\Php2\Blog\Exceptions\PostNotFoundException;
use Geekbrains\Php2\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use Geekbrains\Php2\Blog\UUID;
use Geekbrains\Php2\Http\Actions\ActionInterface;
use Geekbrains\Php2\Http\ErrorResponse;
use Geekbrains\Php2\Http\Request;
use Geekbrains\Php2\Http\Response;
use Geekbrains\Php2\Http\SuccessfulResponse;

class DeletePost implements ActionInterface
{
    public function __construct(
        private PostsRepositoryInterface $postsRepository,
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $postUuid = $request->query('uuid'); // получаем из запроса значение параметра
            $this->postsRepository->get(new UUID($postUuid)); // проверяем есть ли такая статья в репозитории

        } catch (PostNotFoundException $error) {
            return new ErrorResponse($error->getMessage());
        }

        $this->postsRepository->delete(new UUID($postUuid)); // удаляем статью из репозитория

        return new SuccessfulResponse([
            'uuid' => $postUuid,
        ]);
    }
}