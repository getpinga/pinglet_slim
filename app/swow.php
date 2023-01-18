<?php

/**
 * Swoole server initialization.
 *
 * This file provides a callback for create and setup Swoole Http server.
 * The server events will be attached later in `bin/server.php` file.
 *
 * @used-by ../bin/server.php
 * @link https://openswoole.com/docs/modules/swoole-http-server-doc
 * @link https://openswoole.com/docs/modules/swoole-server/configuration
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Slim\App;
use Swow\Coroutine;
use Swow\CoroutineException;
use Swow\Errno;
use Swow\Http\Protocol\ProtocolException as HttpProtocolException;
use Swow\Http\Status as HttpStatus;
use Swow\Psr7\Client\Client;
use Swow\Psr7\Message\UpgradeType;
use Swow\Psr7\Psr7;
use Swow\Psr7\Server\Server as HttpServer;
use Swow\Socket;
use Swow\SocketException;

return function (App $app): HttpServer {
    $server = new \Swow\Psr7\Server\Server();
    $server->bind(env('SERVER_ADDR', 'localhost'), (int) env('SERVER_PORT', 9501))->listen(Socket::DEFAULT_BACKLOG);

    return $server;
};
