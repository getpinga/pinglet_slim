<?php
namespace Pinglet\Bootstrap; 

use DI\Container; 
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;

use Pinglet\Common\Handler as Handler;
use function Pinglet\RegisterRoute;


function Container(): Container {
    global $container;
    if(!isset($container)) {
        $container = __setupContainer();
    }

    return $container;
}

function App(): App {
    global $app;
    if(!isset($app)) {
       $app = __setupApp();
    }
    return $app;
}

function __setupContainer(): Container {
    // global $container;
    // container IoC instance
    return new Container();
}

function __setupApp(): App {
    // global $app;
    $displayErrorDetails = true;
    $logError = false;
    $logErrorDetails = false;

    // Set container to create App with on AppFactory
    AppFactory::setContainer(Container());

    // App instance
    $app = AppFactory::create(); 
    
    $callableResolver = $app->getCallableResolver();
    $responseFactory = $app->getResponseFactory();

    $serverRequestCreator = ServerRequestCreatorFactory::create();
    $request = $serverRequestCreator->createServerRequestFromGlobals();

    // instance error handler
    $errorHandler = new Handler\HttpErrorHandler($callableResolver, $responseFactory);

    // instance shutdown handler
    $shutdownHandler = new Handler\ShutdownHandler($request, $errorHandler, $displayErrorDetails);

    // // register shutdown handler
    register_shutdown_function($shutdownHandler);

    // Add Routing Middleware
    $app->addRoutingMiddleware();

    // Add Error Middleware
    $errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, $logError, $logErrorDetails);

    // set error global error handler
    $errorMiddleware->setDefaultErrorHandler($errorHandler); 

    // register route
    RegisterRoute($app);

    return $app;
}