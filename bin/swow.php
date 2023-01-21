<?php

declare(strict_types=1);

use Swow\Coroutine;
use Swow\CoroutineException;
use Swow\Errno;
use Swow\Http\Protocol\ProtocolException as HttpProtocolException;
use Swow\Http\Status as HttpStatus;
use Swow\Psr7\Client\Client;
use Swow\Psr7\Message\UpgradeType;
use Swow\Psr7\Psr7;
use Swow\Psr7\Server\Server;
use Dotenv\Dotenv;
use Slim\Factory\AppFactory;
use Slim\App;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(ROOT_DIR);
$dotenv->safeLoad();

AppFactory::setContainer((function () {
    return (require APP_DIR . 'container.php')();
})());

$app = AppFactory::create();
$server = (require APP_DIR . 'swow.php')($app);

(function ($app) {
    (require APP_DIR . 'middlewares.php')($app);
})($app);

 (function ($app) {
     (require dirname(__DIR__) . '/routes/index.php')($app);
 })($app);

ini_set('memory_limit', '1G');
logger($app)->info(sprintf('Server is started on %s:%d', env('SERVER_ADDR', 'localhost'), (int) env('SERVER_PORT', 9501)));

while (true) {
    try {
        $connection = null;
        $connection = $server->acceptConnection();
        Coroutine::run(static function () use ($connection, $app): void {
            try {
                while (true) {
                    $request = null;
                    try {
                        $request = $connection->recvHttpRequest();
                        $response = $app->handle($request);
                        $body = (string)$response->getBody();
                        $connection->respond($body);
                    } catch (HttpProtocolException $exception) {
                        logger($app)->error($exception->getMessage());
                        $connection->error($exception->getCode(), $exception->getMessage(), close: true);
                        break;
                    }
                    if (!$connection->shouldKeepAlive()) {
                        break;
                    }
                }
            } catch (Exception) {
                //logger($app)->error($exception->getMessage());
            } finally {
                $connection->close();
            }
        });
    } catch (SocketException|CoroutineException $exception) {
        if (in_array($exception->getCode(), [Errno::EMFILE, Errno::ENFILE, Errno::ENOMEM], true)) {
            sleep(1);
        } else {
            break;
        }
    }
}
