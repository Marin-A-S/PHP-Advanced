<?php

namespace Geekbrains\Php2\Blog\Repositories\AuthTokensRepository;

use Geekbrains\Php2\Blog\AuthToken;

interface AuthTokensRepositoryInterface
{
    // Метод сохранения токена
    public function save(AuthToken $authToken): void;
    // Метод получения токена
    public function get(string $token): AuthToken;
}