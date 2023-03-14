<?php

namespace Geekbrains\Php2\Http\Auth;

use Geekbrains\Php2\Http\Request;
use Geekbrains\Php2\Person\User;

interface AuthenticationInterface
{
    // Получим пользователя из запроса
    public function user(Request $request): User;
}