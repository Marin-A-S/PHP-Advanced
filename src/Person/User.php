<?php

namespace Geekbrains\Php2\Person;

use Geekbrains\Php2\Blog\UUID;

class User
{

    /**
     * @param UUID $uuid
     * @param Name $name
     * @param string $username
     */
    public function __construct(
        private UUID   $uuid,
        private Name   $name, // был $username
        private string $username    // был login
    ) {
    }

    /**
     * @return UUID
     */
    public function uuid(): UUID
    {
        return $this->uuid;
    }

    /**
     * @return Name
     */
    public function getName(): Name
    {
        return $this->name;
    }

    /**
     * @param Name $name
     */
    public function setName(Name $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function __toString(): string
    {
        return "Пользователь: uuid $this->uuid с именем $this->name и логином $this->username.";
    }
}

