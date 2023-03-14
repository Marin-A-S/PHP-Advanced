<?php

namespace Geekbrains\Php2\Blog\Repositories\UsersRepository;

use Geekbrains\Php2\Blog\Exceptions\UserNotFoundException;
use Geekbrains\Php2\Blog\UUID;
use Geekbrains\Php2\Person\Name;
use Geekbrains\Php2\Person\User;

class DummyUsersRepository implements UsersRepositoryInterface
{

    public function save(User $user): void
    {
        // TODO: Implement save() method.
    }

    public function get(UUID $uuid): User
    {
        throw new UserNotFoundException("Not found");
    }

    public function getByUsername(string $username): User
    {
        // возвращаем совершенно произвольного пользователя
        return new User(UUID::random(),  new Name("first", "last"), "user123");
    }
}