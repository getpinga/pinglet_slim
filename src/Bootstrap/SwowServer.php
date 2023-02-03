<?php
namespace Pinglet\Bootstrap;

use Swow\Coroutine;
use Swow\CoroutineException;
use Swow\Errno;
use Swow\Socket;
use Swow\Http\Http;
use Swow\Http\Mime\MimeType;
use Swow\SocketException;
use Swow\Http\Protocol\ProtocolException as HttpProtocolException;
use Swow\Http\Status as HttpStatus;
use Swow\Psr7\Client\Client;
use Swow\Psr7\Message\UpgradeType;
use Swow\Psr7\Psr7;
use Swow\Psr7\Server\Server;

function SwowServer() {
    global $server;
    if(!isset($server)) {
        __setupSwowServer();
    }

    return $server;
}

function __setupSwowServer() {
    global $server;
    
    // instance
    $app = App();
	
	// Http Server instance
    $server = new \Swow\Psr7\Server\Server();
    $server->bind("138.68.97.73", 8080)->listen();

	ini_set('memory_limit', '1G');
	print date("Y-m-d H:i:s:m", time()) . " ~ " . " Server has started...\n";

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
					print date("Y-m-d H:i:s:m", time()) . " ~ " . " server Shutdown...\n";
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
}