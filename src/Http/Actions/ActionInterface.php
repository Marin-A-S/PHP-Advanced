<?php

namespace Geekbrains\Php2\Http\Actions;

use Geekbrains\Php2\Http\Request;
use Geekbrains\Php2\Http\Response;

interface ActionInterface
{
    public function handle(Request $request): Response;
}