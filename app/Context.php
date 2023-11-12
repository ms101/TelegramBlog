<?php

namespace Ms101\TelegramBlog;

use Exception;
use Monolog\Logger;
use Ms101\TelegramBlog\Models\Post;

class Context
{
    private string $postsDbPath;

    private Logger $logger;

    public int $offset;

    /** @var Post[] */
    public array $posts;

    public static function new(Logger $logger): static
    {
        $context = new static;

        $context->postsDbPath = $_ENV['APP_PATH'].'/config/posts.json';
        $context->logger = $logger;
        $context->loadState();

        return $context;
    }

    public function loadState(): void
    {
        $postsData = file_get_contents($this->postsDbPath);

        if ($postsData === false) {
            $errMessage = '[CONTEXT] Posts JSON could not be handled';
            $this->logger->error($errMessage);

            throw new Exception();
        }

        $postsData = json_decode($postsData, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $errMessage = sprintf('[CONTEXT] Posts JSON could not be parsed: %s', json_last_error_msg());
            $this->logger->error($errMessage);

            throw new Exception($errMessage);
        }

        if (! array_key_exists('offset', $postsData)) {
            $errMessage = '[CONTEXT] Posts JSON is missing offset attribute';
            $this->logger->error($errMessage);

            throw new Exception($errMessage);
        }

        $this->offset = (int) $postsData['offset'];

        if (! array_key_exists('posts', $postsData)) {
            $errMessage = '[CONTEXT] Posts JSON is missing posts attribute';
            $this->logger->error($errMessage);

            throw new Exception($errMessage);
        }

        $this->posts = array_map(fn(array $postData): Post => Post::fromArray($postData), $postsData);
    }

    public function saveState(): void
    {
        file_put_contents($this->postsDbPath, json_encode([
                    'offset' => $this->offset,
                    'posts' => array_map(fn(Post $post): array => $post->toArray(), $this->posts),
                ], JSON_UNESCAPED_UNICODE));
    }
}