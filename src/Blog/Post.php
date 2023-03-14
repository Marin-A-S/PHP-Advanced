<?php

namespace Geekbrains\Php2\Blog;

use Geekbrains\Php2\Person\User;

class Post
{

    /**
     * @param UUID $uuid
     * @param User $author
     * @param string $title
     * @param string $text
     */
    public function __construct(
        private UUID $uuid,
        private User $author,
        private string $title,
        private string $text
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
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @param User $author
     */
    public function setAuthor(User $author): void
    {
        $this->author = $author;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function __toString()
    {
        return $this->text;
    }
}