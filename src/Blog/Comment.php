<?php

namespace src\Blog;

class Comment
{
    public function __construct(
        private int $id,
        private int $authorId,
        private int $blogId,
        private string $text
    ) {
    }
}
