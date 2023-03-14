<?php

namespace Geekbrains\Php2\Blog\Repositories\LikesCommentsRepository;

use Geekbrains\Php2\Blog\LikeComment;
use Geekbrains\Php2\Blog\UUID;

interface LikesCommentsRepositoryInterface
{
    public function save(LikeComment $like): void;
    public function getByCommentUuid(UUID $uuid): array;
}