<?php

namespace Geekbrains\Php2\Blog\Repositories\CommentsRepository;

use Geekbrains\Php2\Blog\Comment;
use Geekbrains\Php2\Blog\UUID;

interface CommentsRepositoryInterface
{
    public function save(Comment $post): void;
    public function get(UUID $uuid): Comment;
    public function clear(): void;
}