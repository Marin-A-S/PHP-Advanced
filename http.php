<?php

use Geekbrains\Php2\Http\Actions\Comment\CreateComment;
use Geekbrains\Php2\Http\Actions\Comment\DeleteComment;
use Geekbrains\Php2\Http\Actions\Comment\FindCommentByUuid;
use Geekbrains\Php2\Http\Actions\LikeComment\CreateLikeComment;
use Geekbrains\Php2\Http\Actions\LikeComment\FindLikeCommentByUuid;
use Geekbrains\Php2\Http\Actions\LikePost\CreateLikePost;
use Geekbrains\Php2\Http\Actions\LikePost\FindLikePostByUuid;
use Geekbrains\Php2\Http\Actions\Post\CreatePost;
use Geekbrains\Php2\Http\Actions\Post\DeletePost;
use Geekbrains\Php2\Http\Actions\Post\FindPostByUuid;
use Geekbrains\Php2\Http\Actions\User\CreateUser;
use Geekbrains\Php2\Http\Actions\User\DeleteUser;
use Geekbrains\Php2\Http\Actions\User\FindByUsername;
use Geekbrains\Php2\Http\ErrorResponse;
use Geekbrains\Php2\Http\Request;
use Psr\Log\LoggerInterface;

$container = require __DIR__ . '/bootstrap.php';

// Получаем объект логгера из контейнера
$logger = $container->get(LoggerInterface::class);

// Создаём объект запроса из суперглобальных переменных
$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input') // тело запроса
);

try {
    // получаем путь из запроса
    $path = $request->path();
} catch (HttpException $e) {
    $logger->warning($e->getMessage());
    //неудачный ответ, если не можем получить путь
    (new ErrorResponse)->send();
    return;
}

try {
    // получаем HTTP-метод запроса
    $method = $request->method();
} catch (HttpException $e) {
    $logger->warning($e->getMessage());
    // неудачный ответ,если не можем получить метод
    (new ErrorResponse)->send();
    return;
}

$routes = [
    'GET' => [
        '/users/show' => FindByUsername::class,
        '/posts/show' => FindPostByUuid::class,
        '/comments/show' => FindCommentByUuid::class,
        '/likePosts/show' => FindLikePostByUuid::class,
        '/likeComments/show' => FindLikeCommentByUuid::class,
    ],
    'POST' => [
        '/users/create' => CreateUser::class,
        '/posts/create' => CreatePost::class,
        '/comments/create' => CreateComment::class,
        '/likesPosts/create' => CreateLikePost::class,
        '/likesComments/create' => CreateLikeComment::class,
    ],
    'DELETE' => [
        '/users' => DeleteUser::class,
        '/posts' => DeletePost::class,
        '/comments' => DeleteComment::class,
    ],
];

// Если нет маршрутов для метода запроса - возвращаем неуспешный ответ
if (!array_key_exists($method, $routes) || !array_key_exists($path, $routes[$method])) {
    // Логируем сообщение с уровнем NOTICE
    $message = "Route not found: $method $path";
    $logger->notice($message);
    (new ErrorResponse("Route not found: $method $path"))->send();
    return;
}

// Получаем имя класса действия для маршрута
$actionClassName = $routes[$method][$path];

// С помощью контейнера создаём объект нужного действия
$action = $container->get($actionClassName);

try {
    $response = $action->handle($request);
    // Отправляем ответ
    $response->send();
} catch (Exception $e) {
    // Логируем сообщение с уровнем ERROR
    $logger->error($e->getMessage(), ['exception' => $e]);
    (new ErrorResponse($e->getMessage()))->send();
}
