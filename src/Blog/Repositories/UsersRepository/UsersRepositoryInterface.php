<?php

namespace Geekbrains\Php2\Blog\Repositories\UsersRepository;

use Geekbrains\Php2\Blog\UUID;
use Geekbrains\Php2\Person\User;

interface UsersRepositoryInterface
{
    public function save(User $user): void;
    public function get(UUID $uuid): User;
    public function getByUsername(string $username): User;
}