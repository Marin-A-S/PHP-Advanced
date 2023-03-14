<?php

namespace Geekbrains\Php2\Blog;

class LikeComment
{
    /**
     * @param UUID $uuid
     * @param UUID $comment_uuid
     * @param UUID $post_uuid
     * @param UUID $user_uuid
     */
    public function __construct(
        private UUID $uuid,
        private UUID $comment_uuid,
        private UUID $post_uuid,
        private UUID $user_uuid,
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
     * @param UUID $uuid
     */
    public function setUuid(UUID $uuid): void
    {
        $this->uuid = $uuid;
    }

    /**
     * @return UUID
     */
    public function getCommentUuid(): UUID
    {
        return $this->comment_uuid;
    }

    /**
     * @param UUID $comment_uuid
     */
    public function setCommentUuid(UUID $comment_uuid): void
    {
        $this->comment_uuid = $comment_uuid;
    }

    /**
     * @return UUID
     */
    public function getPostUuid(): UUID
    {
        return $this->post_uuid;
    }

    /**
     * @param UUID $post_uuid
     */
    public function setPostUuid(UUID $post_uuid): void
    {
        $this->post_uuid = $post_uuid;
    }

    /**
     * @return UUID
     */
    public function getUserUuid(): UUID
    {
        return $this->user_uuid;
    }

    /**
     * @param UUID $user_uuid
     */
    public function setUserUuid(UUID $user_uuid): void
    {
        $this->user_uuid = $user_uuid;
    }
}