<?php

namespace Ms101\TelegramBlog;

use Exception;
use Monolog\Logger;
use Ms101\TelegramBlog\Models\Post;
use Ms101\TelegramBlog\Responses\TelegramUpdateResponse;

class TelegramClient
{
    private const TELEGRAM_API_URL = 'https://api.telegram.org';

    private Logger $logger;

    private string $apiToken;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
        $this->apiToken = $_ENV['TELEGRAM_BOT_TOKEN'];
    }

    private function getTelegramApiUrl(string $path, array $queryParams = []): string
    {
        return self::TELEGRAM_API_URL . "/{$path}" . http_build_query($queryParams);
    }

    public function getPosts(int $offset): TelegramUpdateResponse
    {
        $messages = file_get_contents(
            $this->getTelegramApiUrl(
                "/bot{$this->apiToken}/getUpdates",
                ['offset' => $offset]
            )
        );

        if ($messages === false) {
            $errMessage = '[TELEGRAM CLIENT] Telegram response could not be handled';
            $this->logger->error($errMessage);

            throw new Exception($errMessage);
        }

        $messages = json_decode($messages, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $errMessage = sprintf('[TELEGRAM CLIENT] Response message body could not be parsed: %s', json_last_error_msg());
            $this->logger->error($errMessage);

            throw new Exception($errMessage);
        }

        if (!array_key_exists('result', $messages)) {
            $errMessage = '[TELEGRAM CLIENT] Response message body has no results';
            $this->logger->error($errMessage);

            throw new Exception($errMessage);
        }

        if ($messages['result'] === []) {
            $this->logger->debug('[TELEGRAM CLIENT] No new messages found');
        }

        $updateIds = array_unique(
            array_map(fn (array $entry): int => (int) $entry['update_id'], $messages['result'])
        );

        sort($updateIds);

        return new TelegramUpdateResponse(
            offset: array_pop($updateIds),
            posts: array_map(
                function (array $entry): Post {
                    $postData = [];

                    if (array_key_exists('photo', $entry) && count($entry['photo']) > 2) {
                        $postData['fileId'] = (int) $entry['photo'][3]['file_id'];
                    }

                    if (array_key_exists('caption', $entry)) {
                        $postData['caption'] = $entry['caption'];
                    }

                    if (array_key_exists('text', $entry)) {
                        $postData['text'] = $entry['text'];
                    }

                    if (array_key_exists('date', $entry)) {
                        $postData['date'] = $entry['date'];
                    }

                    return Post::fromArray($postData);
                },
                $messages['result']
            )
        );
    }

    /**
     * @param Post[] $posts
     * @return Post[]
     * @throws \Exception
     */
    public function downloadPostImages(array $posts): array
    {
        foreach ($posts as $post) {
            // Download only for post-entries with extern files
            if ($post->fileId === null) {
                continue;
            }

            $uploadResponse = file_get_contents(
                $this->getTelegramApiUrl(
                    "/bot{$this->apiToken}/getFile",
                    ['file_id' => $post->fileId]
                )
            );

            if ($uploadResponse === false) {
                $errMessage = '[TELEGRAM CLIENT] Telegram response could not be handled';
                $this->logger->error($errMessage);

                throw new Exception($errMessage);
            }

            $uploadResponse = json_decode($uploadResponse, true);

            $post->filename = "{$uploadResponse['result']['file_unique_id']}.jpg";

            $post->telegramFilepath = $uploadResponse['result']['file_path'];

            $fileContent = file_get_contents(
                $this->getTelegramApiUrl("/file/bot{$this->apiToken}/{$post->telegramFilepath}")
            );

            $post->filepath = $_ENV['APP_PATH'] . '/public/assets/images/' . $post->filename;

            file_put_contents($post->filepath, $fileContent);
        }

        return $posts;
    }
}