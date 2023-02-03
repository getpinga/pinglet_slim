<?php
namespace Pinglet\Feature;

use Pinglet\Common\Helper\Interceptor;
use Swoole\Coroutine\WaitGroup;
use Swoole\Coroutine\System;

class RootController {
    public function __construct() {
        // echo "__construct calling :" . getmypid() . "\n";
    }

    function __destruct() {
    //   echo "__destruct calling :" . getmypid() . "\n";
    }

    public function hello($request, $response, $args) {
        #$data = "API group Hello world!";
		$data = array('name' => 'Rob', 'age' => 40); 
		return Interceptor::json($response, $data);
    }
    
    public function longRunningProcess($request, $response, $args) {
        // go(function() {
        //     for ($i=0; $i < 2000_000; $i++) { 
        //         \Swoole\Coroutine\System::sleep(1);
        //         echo "Coroutine $i is done.\n";
        //     }
        // });
        
        $results = [];
        // $wg = new WaitGroup();
        // go(function () use ($wg, &$results) {
        //     $wg->add();
        //     for ($i=0; $i < 200_000; $i++) { 
        //         System::sleep(0.001);
        //         // echo "Coroutine $i is done.\n";
        //     }
        //     // System::sleep(12);
        //     $results[] = 'a';
        //     $wg->done();
        // });

        // // // go(function () use ($wg, &$results) {
        // // //     $wg->add();
        // // //     System::sleep(1);
        // // //     $results[] = 'b';
        // // //     $wg->done();
        // // // });

        // $wg->wait();
        
        for ($i=0; $i < 200_000; $i++) { 
            // System::sleep(0.001);
            echo "Coroutine $i is done.\n";
            sleep(0);
        }

        $response
            ->getBody()
            ->write(json_encode($results));

        return $response;
    }
}