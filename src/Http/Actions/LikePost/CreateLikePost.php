<?php

namespace Geekbrains\Php2\Http\Actions\LikePost;

use Geekbrains\Php2\Blog\Exceptions\HttpException;
use Geekbrains\Php2\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\Php2\Blog\Exceptions\LikeAlreadyExists;
use Geekbrains\Php2\Blog\Exceptions\PostNotFoundException;
use Geekbrains\Php2\Blog\Exceptions\UserNotFoundException;
use Geekbrains\Php2\Blog\LikePost;
use Geekbrains\Php2\Blog\Repositories\LikesPostsRepository\LikesPostsRepositoryInterface;
use Geekbrains\Php2\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use Geekbrains\Php2\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Geekbrains\Php2\Blog\UUID;
use Geekbrains\Php2\Http\Actions\ActionInterface;
use Geekbrains\Php2\Http\ErrorResponse;
use Geekbrains\Php2\Http\Request;
use Geekbrains\Php2\Http\Response;
use Geekbrains\Php2\Http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class CreateLikePost implements ActionInterface
{
    public function __construct(
        private LikesPostsRepositoryInterface $likesPostsRepository,
        private PostsRepositoryInterface      $postsRepository,
        private UsersRepositoryInterface      $usersRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            // Из данных POST запроса заполняем переменные UUID
            $userUuid = new UUID($request->jsonBodyField('author_uuid'));
            $postUuid = new UUID($request->jsonBodyField('post_uuid'));
        } catch (HttpException|InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            // получаем пост из репозитория
            $post = $this->postsRepository->get($postUuid);
        } catch (PostNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            // получаем пользователя из репозитория
            $user = $this->usersRepository->get($userUuid);
        } catch (UserNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            // проверяем лайки поста и пользователя в репозитории
            $this->likesPostsRepository->checkUserLikeForPostExists($postUuid, $userUuid);
        } catch (LikeAlreadyExists $e) {
            return new ErrorResponse($e->getMessage());
        }

        // Генерируем UUID для нового лайка
        $newLikePostUuid = UUID::random();

        try {
            $likePost = new LikePost(
                uuid: $newLikePostUuid,
                post: $post,
                user: $user,
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->likesPostsRepository->save($likePost);

        // Логируем UUID новой статьи
        $this->logger->info("Saved likePost with uuid: $newLikePostUuid");

        return new SuccessFulResponse(
            ['uuid' => (string)$newLikePostUuid]
        );
    }
}