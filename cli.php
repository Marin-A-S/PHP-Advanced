<?php

use Geekbrains\Php2\Blog\Commands\Arguments;
use Geekbrains\Php2\Blog\Commands\CreateUserCommand;
use Psr\Log\LoggerInterface;

$container = require __DIR__ . '/bootstrap.php';

// Получаем объект логгера из контейнера
$logger = $container->get(LoggerInterface::class);

// При помощи контейнера создаём команду
$command = $container->get(CreateUserCommand::class);

try {
    $command->handle(Arguments::fromArgv($argv));
} catch (Exception $e) {
    $logger->error($e->getMessage(), ['exception' => $e]);
    echo $e->getMessage();
}
