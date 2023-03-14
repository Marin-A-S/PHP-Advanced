<?php

namespace Geekbrains\Php2\Http\Actions\Post;

use Geekbrains\Php2\Blog\Exceptions\HttpException;
use Geekbrains\Php2\Blog\Exceptions\PostNotFoundException;
use Geekbrains\Php2\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use Geekbrains\Php2\Blog\UUID;
use Geekbrains\Php2\Http\Actions\ActionInterface;
use Geekbrains\Php2\Http\ErrorResponse;
use Geekbrains\Php2\Http\Request;
use Geekbrains\Php2\Http\Response;
use Geekbrains\Php2\Http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class FindPostByUuid implements ActionInterface
{
    public function __construct(
        private PostsRepositoryInterface $postsRepository,
        private LoggerInterface          $logger,
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            // получаем uuid из запроса
            $uuid = $request->query('post_uuid');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            // ищем статью в репозитории
            $post = $this->postsRepository->get(new UUID($uuid));
        } catch (PostNotFoundException $e) {
            $this->logger->warning("Cannot find post: $uuid");
            // Если статья не найден - возвращаем неуспешный ответ
            return new ErrorResponse($e->getMessage());
        }

        // Возвращаем успешный ответ
        return new SuccessfulResponse([
            'user' => $post->getAuthor()->getName()->getFirstName() . ' ' . $post->getAuthor()->getName()->getLastName(),
            'title' => $post->getTitle(),
            'text' => $post->getText(),
        ]);
    }
}