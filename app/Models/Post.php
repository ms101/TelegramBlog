<?php

namespace Ms101\TelegramBlog\Models;

class Post
{
    public ?int $fileId;

    public ?string $filename;

    public ?string $filepath;

    public ?string $telegramFilepath;

    public ?string $text;

    public ?string $caption;

    public ?string $date;

    public static function fromArray(array $attrs): static
    {
        $post = new static;

        if (array_key_exists('fileId', $attrs)) {
            $post->fileId = $attrs['fileId'];
        }

        if (array_key_exists('filename', $attrs)) {
            $post->filename = $attrs['filename'];
        }

        if (array_key_exists('filepath', $attrs)) {
            $post->filepath = $attrs['filepath'];
        }

        if (array_key_exists('telegramFilepath', $attrs)) {
            $post->telegramFilepath = $attrs['telegramFilepath'];
        }

        if (array_key_exists('text', $attrs)) {
            $post->text = $attrs['text'];
        }

        if (array_key_exists('caption', $attrs)) {
            $post->caption = $attrs['caption'];
        }

        if (array_key_exists('date', $attrs)) {
            $post->date = $attrs['date'];
        }

        return $post;
    }

    public function toArray(): array
    {
        return [
            'fileId' => $this->fileId ?? null,
            'filename' => $this->filename ?? null,
            'filepath' => $this->filepath ?? null,
            'telegramFilepath' => $this->telegramFilepath ?? null,
            'text' => $this->text ?? null,
            'caption' => $this->caption ?? null,
            'date' => $this->date,
        ];
    }
}