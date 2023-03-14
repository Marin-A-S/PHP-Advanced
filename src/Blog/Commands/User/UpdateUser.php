<?php

namespace Geekbrains\Php2\Blog\Commands\User;

use Geekbrains\Php2\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Geekbrains\Php2\Blog\UUID;
use Geekbrains\Php2\Person\Name;
use Geekbrains\Php2\Person\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateUser extends Command
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('users:update')
            ->setDescription('Updates a user')
            ->addArgument(
                'uuid',
                InputArgument::REQUIRED,
                'UUID of a user to update'
            )
            ->addOption(
            // Имя опции
                'first-name',
                // Сокращённое имя
                'f',
                // Опция имеет значения
                InputOption::VALUE_OPTIONAL,
                // Описание
                'First name',
            )
            ->addOption(
                'last-name',
                'l',
                InputOption::VALUE_OPTIONAL,
                'Last name',
            );
    }

    protected function execute(
        InputInterface  $input,
        OutputInterface $output,
    ): int
    {
        // Получаем значения опций
        $firstName = $input->getOption('first-name');
        $lastName = $input->getOption('last-name');
        // Выходим, если обе опции пусты
        if (empty($firstName) && empty($lastName)) {
            $output->writeln('Nothing to update');
            return Command::SUCCESS;
        }
        // Получаем UUID из аргумента
        $uuid = new UUID($input->getArgument('uuid'));
        // Получаем пользователя из репозитория
        $user = $this->usersRepository->get($uuid);
        // Создаём объект обновлённого имени
        $updatedName = new Name(
            // Берём сохранённое имя,
            // если опция имени пуста
            firstName: empty($firstName)
                ? $user->getName()->getFirstName() : $firstName,
            // Берём сохранённую фамилию,
            // если опция фамилии пуста
            lastName: empty($lastName)
                ? $user->getName()->getLastName() : $lastName,
        );
        // Создаём новый объект пользователя
        $updatedUser = new User(
            uuid: $uuid,
            // Обновлённое имя
            name: $updatedName,
            // Имя пользователя и пароль
            // оставляем без изменений
            username: $user->getUsername(),
            hashedPassword: $user->hashedPassword()
        );
        // Сохраняем обновлённого пользователя
        $this->usersRepository->save($updatedUser);
        $output->writeln("User updated: $uuid");

        return Command::SUCCESS;
    }
}