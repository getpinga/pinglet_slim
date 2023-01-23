<?php

declare(strict_types=1);

use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Psr\Http\Message\ServerRequestInterface as PiRequest;
use Psr\Http\Message\ResponseInterface as PiResponse;
use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Kodus\Cache\FileCache;
use function Compwright\PhpSession\Frameworks\Slim\registerSessionMiddleware;

return function (App $app): void {
	$sessionFactory = new Compwright\PhpSession\Factory();
	$manager = $sessionFactory->psr16Session(
	
	new FileCache(sys_get_temp_dir(),'3600'),
		[
			'name' => 'my_app',
			'sid_length' => 48,
			'sid_bits_per_character' => 5,
		]
	);

	$started = $manager->start();
	if ($started === false) {
		throw new RuntimeException("The session failed to start");
	}

     $app->options('/{routes:.*}', function (PiRequest $request, PiResponse $response) use ($manager) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    }); 

    $app->get('/', function (PiRequest $request, PiResponse $response) use ($manager) {
		
		$session = $manager->getCurrentSession();
		$session["foo"] = "bar";

        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->get('/json', fn () => json(['status' => 'ok']));

    $app->get('/hello/{name}', function (PiRequest $request, PiResponse $response, array $args) use ($manager) {
		$session = $manager->getCurrentSession();
		if (isset($session['foo'])) {     $response->getBody()->write($session['foo']); }
        $name = $args['name'];
        $response->getBody()->write("Hello, $name");
        return $response;
    });

    $app->group('/users', function (Group $group) use ($manager) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });
};
