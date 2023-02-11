<?php

namespace src\Blog;

class Article_Post
{
    public function __construct(
        private int $id,
        private int $authorId,
        private string $author,
        private string $header,
        private string $text
    ) {
    }
}
