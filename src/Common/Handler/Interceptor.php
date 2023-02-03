<?php

namespace Pinglet\Common\Helper; 

// use Psr\Http\Message\ResponseInterface;


class Interceptor {

    static function json($response, array $payload=[], int $httpStatus=200) {
        $response
            ->getBody()
            ->write(json_encode($payload));
            
        return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus($httpStatus);
    }
	
    static function html($response, string $payload, int $httpStatus=200) {
        $response
            ->getBody()
            ->write($payload);
            
        return $response
                ->withHeader('Content-Type', 'text/html')
                ->withStatus($httpStatus);
    }
}