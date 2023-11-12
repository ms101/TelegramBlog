<?php

namespace Ms101\TelegramBlog;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Ms101\TelegramBlog\Models\Post;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class App
{
    private Logger $logger;

    private Environment $twig;

    private TelegramService $telegramService;

    public function __construct()
    {
        $_ENV['APP_PATH'] = realpath(__DIR__ . '/..');

        $this->logger = $this->initLogger();

        $this->twig = $this->initTwigEnvironment();

        $this->telegramService = new TelegramService($this->logger);
    }

    private static function isDebug(): bool
    {
        static $debug;

        return $debug ??= $_ENV['APP_DEBUG'] === 'true';
    }

    private function initLogger(): Logger
    {
        $logger = new Logger('telegram-blog');
        $logger->pushHandler(
            new StreamHandler(
                self::isDebug() ? $_ENV['APP_PATH'] . '/logs/telegram-blog.log' : 'php://stdout',
                self::isDebug() ? Level::Debug : Level::Error
            )
        );

        return $logger;
    }

    private function initTwigEnvironment(): Environment
    {
        $loader = new FilesystemLoader($_ENV['APP_PATH'] . '/templates');

        return new Environment($loader);
    }

    public function run(): void
    {
        $this->logger->debug('[APP] run called');

        echo $this->twig->render('index.twig', [
            'title' => $_ENV['APP_TITLE'],
            'description' => $_ENV['APP_DESCRIPTION'],

            // @TODO THE REAL DEAL
            //'posts' => $this->telegramService->getPosts(),

            // @TODO Temporarily mocking some post content
            'posts' => [
                // Some image post with date and without caption
                Post::fromArray([
                    'fileId' => 1,
                    'filename' => '1.jpg',
                    'filepath' => '/assets/images/1.jpg',
                    'telegramFilepath' => '/abc/1.jpg',
                    'text' => null,
                    'caption' => null,
                    'date' => '15.02 20:30',
                ]),

                // Some text post
                Post::fromArray([
                    'fileId' => null,
                    'filename' => null,
                    'filepath' => null,
                    'telegramFilepath' => null,
                    'text' => '<p>Lorem Ipsum, <strong>Lorem</strong> Ipsum</p>',
                    'caption' => null,
                    'date' => '20.03 01:10',
                ]),

                // Some image post without date and with caption
                Post::fromArray([
                    'fileId' => 2,
                    'filename' => '2.jpg',
                    'filepath' => '/assets/images/2.jpg',
                    'telegramFilepath' => '/abc/2.jpg',
                    'text' => null,
                    'caption' => 'What a <strong>nice</strong> image',
                    'date' => '01.04 09:15',
                ]),
            ],
        ]);
    }
}