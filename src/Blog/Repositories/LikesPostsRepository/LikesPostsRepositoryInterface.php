<?php

namespace Geekbrains\Php2\Blog\Repositories\LikesPostsRepository;

use Geekbrains\Php2\Blog\LikePost;
use Geekbrains\Php2\Blog\UUID;

interface LikesPostsRepositoryInterface
{
    public function save(LikePost $likePost): void;
    public function getByPostUuid(UUID $uuid): array;
}