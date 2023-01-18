<?php

/**
 * Gets the PSR-11 compatible DI container.
 *
 * This project uses PHP-DI container implementation,
 * but it's up to you to use any PSR-11 compatible container.
 */

declare(strict_types=1);

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\ProcessIdProcessor;

use function DI\create;

return function (): ContainerInterface {

    $builder = new ContainerBuilder();
// Your own definitions...
//    $builder->addDefinitions([
//        UserRepository::class => create(ArrayUserRepository::class),
//    ]);

    $builder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $logger = new Logger(env('APP_NAME', 'pinglet'));
            $logger->pushProcessor(new ProcessIdProcessor());
            $logger->pushProcessor(new UidProcessor());
            $logger->pushHandler(new StreamHandler(env('LOG_FILE', 'php://stdout'), is_debug_enabled() ? Logger::DEBUG : Logger::INFO));
            return $logger;
        },
    ]);

    return $builder->build();
};
