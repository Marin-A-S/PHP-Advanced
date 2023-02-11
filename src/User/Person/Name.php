<?php

namespace src\User;

class Person_Name
{
    public function __construct(
        private int $id,
        private string $firstName,
        private string $lastName
    ) {
    }
    public function __toString()
    {
        return $this->firstName . ' ' . $this->lastName;
    }
}
