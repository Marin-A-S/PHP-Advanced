<?php

namespace Geekbrains\Php2\Http\Actions\LikePost;

use Geekbrains\Php2\Blog\Exceptions\AuthException;
use Geekbrains\Php2\Blog\Exceptions\HttpException;
use Geekbrains\Php2\Blog\Exceptions\LikesPostNotFoundException;
use Geekbrains\Php2\Blog\Repositories\LikesPostsRepository\LikesPostsRepositoryInterface;
use Geekbrains\Php2\Blog\UUID;
use Geekbrains\Php2\Http\Actions\ActionInterface;
use Geekbrains\Php2\Http\Auth\TokenAuthenticationInterface;
use Geekbrains\Php2\Http\ErrorResponse;
use Geekbrains\Php2\Http\Request;
use Geekbrains\Php2\Http\Response;
use Geekbrains\Php2\Http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class FindLikePostByUuid implements ActionInterface
{
    public function __construct(
        private LikesPostsRepositoryInterface $likesPostsRepository,
        private TokenAuthenticationInterface  $authentication,
        private LoggerInterface               $logger,
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
            // получаем uuid из запроса
            $uuid = $request->query('post_uuid');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            // ищем статью в репозитории
            $likes = $this->likesPostsRepository->getByPostUuid(new UUID($uuid));
        } catch (LikesPostNotFoundException $e) {
            $this->logger->warning("Cannot find like post: $uuid");
            // Если статья не найден - возвращаем неуспешный ответ
            return new ErrorResponse($e->getMessage());
        }

        // Возвращаем успешный ответ
        $likesPosts = [];
        foreach ($likes as $key => $value) {
            $likesPosts [$key] = ([
                'user' => $value->getUser()->getName()->getFirstName() . " " . $value->getUser()->getName()->getLastName(),
                'title' => $value->getPost()->getTitle(),
                'post' => $value->getPost()->getAuthor()->getName()->getFirstName() . " " . $value->getPost()->getAuthor()->getName()->getLastName(),
            ]);
        }
        return new SuccessfulResponse($likesPosts);
    }
}