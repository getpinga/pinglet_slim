<?php
namespace Pinglet\Bootstrap;

use Imefisto\PsrSwoole\ServerRequest as PsrRequest;
use Imefisto\PsrSwoole\ResponseMerger;
use Nyholm\Psr7\Factory\Psr17Factory;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server as HttpServer;
use Swoole\Coroutine;

function SwooleServer() {
    global $server;
    if(!isset($server)) {
        __setupServer();
    }

    return $server;
}

function __setupServer() {
    global $server;
    
    // instance
    $app = App();

    // hook all I/O with swoole
    Coroutine::set(['hook_flags' => SWOOLE_HOOK_ALL]);

    // Http Server instance
    $server = new HttpServer("138.68.97.73", 8080);

    // http server setting
    $server->set([
        'worker_num' => 12,
        'max_request' => 1000000000,
    ]);

    // callback when the server is Start
    $server->on('Start', function ($server) {
        print date("Y-m-d H:i:s:m", time()) . " ~ " . " Server has started...\n";
    });

    $uriFactory = new Psr17Factory;
    $streamFactory = new Psr17Factory;
    $responseFactory = new Psr17Factory;
    $uploadedFileFactory = new Psr17Factory;
    $responseMerger = new ResponseMerger;

    // callback when request coming
    $server->on('request', function (Request $swooleRequest, Response $swooleResponse) use ($uriFactory, $streamFactory, $uploadedFileFactory, $responseFactory, $responseMerger, $app) {
        /**
         * create psr request from swoole request
         */
        $psrRequest = new PsrRequest($swooleRequest, $uriFactory, $streamFactory, $uploadedFileFactory);

        /**
         * process request (here is where slim handles the request)
         */
        $psrResponse = $app->handle($psrRequest);

        /**
         * merge your psr response with swoole response
         */
        $responseMerger->toSwoole($psrResponse, $swooleResponse)->end();
    });

    // callback when the server is Shutdown
    $server->on("Shutdown", function($server, $workerId) {
        print date("Y-m-d H:i:s:m", time()) . " ~ " . " server $workerId Shutdown...\n";
    }); 
}