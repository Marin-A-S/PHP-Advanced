<?php

namespace Geekbrains\Php2;

use Geekbrains\Php2\Blog\Commands\Arguments;
use Geekbrains\Php2\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\Php2\Blog\UUID;
use PHPUnit\Framework\TestCase;

class UUIDTest extends TestCase
{
    public function testItThrowsAnExceptionMalformedUUID(): void
    {
        // uuid
        $uuid = "xx6d61d2d0-6bf8-426f-855a-dc9de949594b";

        // Описываем тип ожидаемого исключения
        $this->expectException(InvalidArgumentException::class);

        // и его сообщение
        $this->expectExceptionMessage("Malformed UUID: $uuid");

        // Выполняем действие, приводящее к выбрасыванию исключения
        new UUID("xx6d61d2d0-6bf8-426f-855a-dc9de949594b");
    }
}
