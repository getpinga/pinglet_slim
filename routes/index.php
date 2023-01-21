<?php

declare(strict_types=1);

use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Psr\Http\Message\ServerRequestInterface as PiRequest;
use Psr\Http\Message\ResponseInterface as PiResponse;
use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;

return function (App $app): void {
    $app->options('/{routes:.*}', function (PiRequest $request, PiResponse $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (PiRequest $request, PiResponse $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->get('/json', fn () => json(['status' => 'ok']));

    $app->get('/hello/{name}', function (PiRequest $request, PiResponse $response, array $args) {
        $name = $args['name'];
        $response->getBody()->write("Hello, $name");
        return $response;
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });
};
