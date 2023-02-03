<?php
namespace Pinglet\Common\Handler;

use Psr\Http\Message\ResponseInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpNotImplementedException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Handlers\ErrorHandler;
use Exception;
use Throwable;

class HttpErrorHandler extends ErrorHandler
{
    public const BAD_REQUEST = 'BAD_REQUEST';
    public const INSUFFICIENT_PRIVILEGES = 'INSUFFICIENT_PRIVILEGES';
    public const NOT_ALLOWED = 'NOT_ALLOWED';
    public const NOT_IMPLEMENTED = 'NOT_IMPLEMENTED';
    public const RESOURCE_NOT_FOUND = 'RESOURCE_NOT_FOUND';
    public const SERVER_ERROR = 'SERVER_ERROR';
    public const UNAUTHENTICATED = 'UNAUTHENTICATED';
    
    protected function respond(): ResponseInterface
    {
        $exception = $this->exception;
        $statusCode = 500;
        $type = self::SERVER_ERROR;
        $description = 'An internal error has occurred while processing your request.';

        if ($exception instanceof HttpException) {
            $statusCode = $exception->getCode();
            $description = $exception->getMessage();

            if ($exception instanceof HttpNotFoundException) {
                $statusCode = 404;
                $type = self::RESOURCE_NOT_FOUND;
            } elseif ($exception instanceof HttpMethodNotAllowedException) {
                $statusCode = 405;
                $type = self::NOT_ALLOWED;
            } elseif ($exception instanceof HttpUnauthorizedException) {
                $statusCode = 403;
                $type = self::UNAUTHENTICATED;
            } elseif ($exception instanceof HttpForbiddenException) {
                $statusCode = 403;
                $type = self::UNAUTHENTICATED;
            } elseif ($exception instanceof HttpBadRequestException) {
                $statusCode = 400;
                $type = self::BAD_REQUEST;
            } elseif ($exception instanceof HttpNotImplementedException) {
                $type = self::NOT_IMPLEMENTED;
                $statusCode = 501;
            }
        }

        if(!($exception->getCode() >= 400 && $exception->getCode() <= 499)) {
            print("\n/**\n");
            print(date("Y-m-d H:i:s:m", time()) . "\t");
            print(json_encode([
                "scope"     => "global",
                "code"      => $exception->getCode(),
                "message"   => $exception->getMessage(),
                "file"      => $exception->getFile(),
                "line"      => $exception->getLine(),     
            ]));
            print("\n**/\n");
        } 

        if (
            !($exception instanceof HttpException)
            && ($exception instanceof Exception || $exception instanceof Throwable)
            && $this->displayErrorDetails
        ) {
            $description = $exception->getMessage();
        }

        $error = [
            'statusCode' => $statusCode,
            'error' => [
                'type' => $type,
                'description' => $description,
            ],
        ];
        
        $response = $this->responseFactory
            ->createResponse($statusCode);        
        $response->getBody()
            ->write(json_encode($error, JSON_PRETTY_PRINT));
        
        return $response;
    }
}
