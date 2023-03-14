<?php

namespace Geekbrains\Php2\Container;

class ClassDependingOnAnother
{
// Класс с двумя зависимостями
    public function __construct(
        private SomeClassWithoutDependencies $one,
        private SomeClassWithParameter $two,
    ) {
    }
}