<?php

namespace Geekbrains\Php2;

use Geekbrains\Php2\Person\Name;
use PHPUnit\Framework\TestCase;

class NameTest extends TestCase
{
    public function testItChangeFirstName(): void
    {
        $name = new Name ('Ivan', 'Ivanov');

        $name->setFirstName('Peter');

        $this->assertSame('Peter', $name->getFirstName());
    }

    public function testItChangeLastName(): void
    {
        $name = new Name ('Ivan', 'Ivanov');

        $name->setLastName('Smirnov');

        $this->assertSame('Smirnov', $name->getLastName());
    }
}