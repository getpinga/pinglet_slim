<?php

/**
 * Application routes list.
 *
 * @link https://www.slimframework.com/docs/v4/objects/routing.html
 */

declare(strict_types=1);

use Slim\App;
use Psr\Http\Message\ServerRequestInterface as PiRequest;
use Psr\Http\Message\ResponseInterface as PiResponse;

return function (App $app): void {

    $app->get('/', fn () => json(['status' => 'ok']));
	
	$app->get('/hello/{name}', function (PiRequest $request, PiResponse $response, array $args) {
    $name = $args['name'];
    $response->getBody()->write("Hello, $name");
    return $response;
});

};
