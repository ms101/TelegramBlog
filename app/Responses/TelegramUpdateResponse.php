<?php

namespace Ms101\TelegramBlog\Responses;

use Ms101\TelegramBlog\Models\Post;

class TelegramUpdateResponse
{
    public function __construct(
        public int $offset,
        /** @var Post[] $posts */
        public array $posts,
    )
    {
    }
}