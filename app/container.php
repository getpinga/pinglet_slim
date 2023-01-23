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
use Compwright\PhpSession\Config as SessionConfig;
use Compwright\PhpSession\Handlers\Psr16Handler as SessionSaveHandler;
use Kodus\Cache\FileCache;

use function DI\create;

return function (): ContainerInterface {

    $builder = new ContainerBuilder();

    $builder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $logger = new Logger(env('APP_NAME', 'pinglet'));
            $logger->pushProcessor(new ProcessIdProcessor());
            $logger->pushProcessor(new UidProcessor());
            $logger->pushHandler(new StreamHandler(env('LOG_FILE', 'php://stdout'), is_debug_enabled() ? Logger::DEBUG : Logger::INFO));
            return $logger;
        },
		SessionConfig::class => DI\factory(function () {
			$config = (new SessionConfig())
				->setRegenerateIdInterval(180)
				->setCookieLifetime(3600)
				->setCookiePath('/')
				->setCookieSecure(false)
				->setCookieHttpOnly(true)
				->setCookieSameSite('strict')
				->setCacheLimiter('nocache')
				->setGcProbability(1)
				->setGcDivisor(1)
				->setGcMaxLifetime(7200)
				->setSidLength(48)
				->setSidBitsPerCharacter(5)
				->setLazyWrite(true);

			$config->setSavePath(sys_get_temp_dir() . DIRECTORY_SEPARATOR . $config->getName());

			$config->setSaveHandler(
				new SessionSaveHandler($config, new FileCache(
					$config->getSavePath() ?? sys_get_temp_dir(),
					$config->getGcMaxLifetime()
				))
			);

			return $config;
		}),
    ]);

    return $builder->build();
};
