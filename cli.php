<?php

use Geekbrains\Php2\Blog\Commands\FakeData\PopulateDB;
use Geekbrains\Php2\Blog\Commands\Post\DeletePost;
use Geekbrains\Php2\Blog\Commands\User\CreateUser;
use Geekbrains\Php2\Blog\Commands\User\UpdateUser;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;

$container = require __DIR__ . '/bootstrap.php';

// Получаем объект логгера из контейнера
$logger = $container->get(LoggerInterface::class);

// Создаём объект приложения
$application = new Application();
// Перечисляем классы команд
$commandsClasses = [
    CreateUser::class,
    DeletePost::class,
    UpdateUser::class,
    PopulateDB::class,
];

foreach ($commandsClasses as $commandClass) {
    // Посредством контейнера
    // создаём объект команды
    $command = $container->get($commandClass);
    // Добавляем команду к приложению
    $application->add($command);
}

// При помощи контейнера создаём команду
//$command = $container->get(CreateUserCommand::class);

try {
//    $command->handle(Arguments::fromArgv($argv));

    // Запускаем приложение
    $application->run();
} catch (Exception $e) {
    $logger->error($e->getMessage(), ['exception' => $e]);
    echo $e->getMessage();
}
