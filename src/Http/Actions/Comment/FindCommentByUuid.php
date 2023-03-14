<?php

namespace Geekbrains\Php2\Http\Actions\Comment;

use Geekbrains\Php2\Blog\Exceptions\AuthException;
use Geekbrains\Php2\Blog\Exceptions\CommentNotFoundException;
use Geekbrains\Php2\Blog\Exceptions\HttpException;
use Geekbrains\Php2\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use Geekbrains\Php2\Blog\UUID;
use Geekbrains\Php2\Http\Actions\ActionInterface;
use Geekbrains\Php2\Http\Auth\TokenAuthenticationInterface;
use Geekbrains\Php2\Http\ErrorResponse;
use Geekbrains\Php2\Http\Request;
use Geekbrains\Php2\Http\Response;
use Geekbrains\Php2\Http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class FindCommentByUuid implements ActionInterface
{
    public function __construct(
        private CommentsRepositoryInterface $commentsRepository,
        private TokenAuthenticationInterface $authentication,
        private LoggerInterface          $logger,
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
            $uuid = $request->query('comment_uuid');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $comment = $this->commentsRepository->get(new UUID($uuid));
        } catch (CommentNotFoundException $e) {
            $this->logger->warning("Cannot find comment: $uuid");
            return new ErrorResponse($e->getMessage());
        }

        // Возвращаем успешный ответ
        return new SuccessfulResponse([
            'user' => $comment->getUser()->getName()->getFirstName() . ' ' . $comment->getUser()->getName()->getLastName(),
            'post' => $comment->getPost()->getTitle(),
            'text' => $comment->getText(),
        ]);
    }
}