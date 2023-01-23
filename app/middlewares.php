<?php

/**
 * Application and route middlewares.
 *
 * @link https://www.slimframework.com/docs/v4/middleware/routing.html
 */

declare(strict_types=1);

use Slim\App;
use function Compwright\PhpSession\Frameworks\Slim\registerSessionMiddleware;

return function (App $app): void {

    // Middleware
    registerSessionMiddleware($app);
    $app->addRoutingMiddleware(); // must come before ErrorMiddleware
    $app->addErrorMiddleware(
        displayErrorDetails: is_debug_enabled(),
        logErrors: (bool) env('LOG_ERRORS', false),
        logErrorDetails: (bool) env('LOG_ERROR_DETAILS', false),
        logger: logger($app),
    );

};
