<?php

namespace Geekbrains\Php2\Blog;

use Geekbrains\Php2\Blog\Exceptions\InvalidArgumentException;

class UUID
{
    // Внутри объекта мы храним UUID как строку
    public function __construct(
        private string $uuidString
    ) {
        // Если входная строка не подходит по формату -
        // бросаем исключение InvalidArgumentException
        // Таким образом, мы гарантируем, что если объект
        // был создан, то он точно содержит правильный UUID
        if (!uuid_is_valid($uuidString)) {
            print_r("Malformed UUID: " . $uuidString);
            throw new InvalidArgumentException(
                "Malformed UUID: $this->uuidString"
            );
        }
    }

    // Генерируем новый случайный UUID и
    // получаем его в качестве объекта нашего класса
    public static function random(): self
    {
        return new self(uuid_create(UUID_TYPE_RANDOM));
    }

    public function __toString(): string
    {
        return $this->uuidString;
    }

}