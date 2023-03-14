<?php

namespace Geekbrains\Php2\Blog\Commands;

use Geekbrains\Php2\Blog\Exceptions\ArgumentsException;
use Geekbrains\Php2\Blog\Exceptions\CommandException;
use Geekbrains\Php2\Blog\Exceptions\UserNotFoundException;
use Geekbrains\Php2\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Geekbrains\Php2\Blog\UUID;
use Geekbrains\Php2\Person\{Name, User};
use Psr\Log\LoggerInterface;

class CreateUserCommand
{
    // Команда зависит от контракта репозитория пользователей,
    // а не от конкретной реализации
    /**
     * @param UsersRepositoryInterface $usersRepository
     */
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private LoggerInterface          $logger,
    ) {
    }

    /**
     * @param Arguments $arguments
     * @return void
     * @throws CommandException
     * @throws ArgumentsException
     */
    public function handle(Arguments $arguments): void
    {
        // Логируем информацию о том, что команда запущена
        // Уровень логирования – INFO
        $this->logger->info("Create user command started");

        $username = $arguments->get('username');

        // Проверяем, существует ли пользователь в репозитории
        if ($this->userExists($username)) {
            // Логируем сообщение с уровнем WARNING
            $this->logger->warning("User already exists: $username");

            // Бросаем исключение, если пользователь уже существует
            throw new CommandException("User already exists: $username");
        }
        // Сохраняем пользователя в репозиторий
        $uuid = UUID::random();
        $this->usersRepository->save(new User(
            $uuid,
            new Name(
                $arguments->get('first_name'),
                $arguments->get('last_name')),
            $username
        ));
        // Логируем информацию о новом пользователе
        $this->logger->info("User created: $uuid");
    }

    /**
     * @param string $username
     * @return bool
     */
    private function userExists(string $username): bool
    {
        try {
            // Пытаемся получить пользователя из репозитория
            $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException) {
            return false;
        }
        return true;
    }
}