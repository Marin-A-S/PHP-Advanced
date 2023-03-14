<?php

namespace Geekbrains\Php2\Http\Actions\User;

use Geekbrains\Php2\Blog\Exceptions\PostNotFoundException;
use Geekbrains\Php2\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Geekbrains\Php2\Blog\UUID;
use Geekbrains\Php2\Http\Actions\ActionInterface;
use Geekbrains\Php2\Http\ErrorResponse;
use Geekbrains\Php2\Http\Request;
use Geekbrains\Php2\Http\Response;
use Geekbrains\Php2\Http\SuccessfulResponse;

class DeleteUser implements ActionInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $userUuid = $request->query('uuid'); // получаем из запроса значение параметра
            $this->usersRepository->get(new UUID($userUuid)); // проверяем есть ли такая статья в репозитории

        } catch (PostNotFoundException $error) {
            return new ErrorResponse($error->getMessage());
        }

        $this->usersRepository->delete(new UUID($userUuid)); // удаляем статью из репозитория

        return new SuccessfulResponse([
            'uuid' => $userUuid,
        ]);
    }
}