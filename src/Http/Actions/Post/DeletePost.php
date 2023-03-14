<?php

namespace Geekbrains\Php2\Http\Actions\Post;

use Geekbrains\Php2\Blog\Exceptions\AuthException;
use Geekbrains\Php2\Blog\Exceptions\DeleteException;
use Geekbrains\Php2\Blog\Exceptions\PostNotFoundException;
use Geekbrains\Php2\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use Geekbrains\Php2\Blog\UUID;
use Geekbrains\Php2\Http\Actions\ActionInterface;
use Geekbrains\Php2\Http\Auth\TokenAuthenticationInterface;
use Geekbrains\Php2\Http\ErrorResponse;
use Geekbrains\Php2\Http\Request;
use Geekbrains\Php2\Http\Response;
use Geekbrains\Php2\Http\SuccessfulResponse;

class DeletePost implements ActionInterface
{
    public function __construct(
        private PostsRepositoryInterface     $postsRepository,
        private TokenAuthenticationInterface $authentication,
    ) {
    }

    public function handle(Request $request): Response
    {
        // Идентифицируем пользователя по токену
        try {
            $user = $this->authentication->user($request);
            $userUuidToken = (string)$user->uuid();
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $postUuid = $request->query('uuid'); // получаем из запроса значение параметра
            $this->postsRepository->get(new UUID($postUuid)); // проверяем есть ли такая статья в репозитории
            $userUuidPost = (string)$this->postsRepository->get(new UUID($postUuid))->getAuthor()->uuid();
        } catch (PostNotFoundException $error) {
            return new ErrorResponse($error->getMessage());
        }

        if ($userUuidToken === $userUuidPost) {
            $this->postsRepository->delete(new UUID($postUuid)); // удаляем статью из репозитория
        } else {
            throw new DeleteException("No allowed delete post user: $userUuidToken");
        }

        return new SuccessfulResponse([
            'uuid' => $postUuid,
        ]);
    }
}