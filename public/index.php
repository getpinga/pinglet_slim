<?php 
declare(strict_types=1);
ini_set('memory_limit', '1G');

use function Pinglet\Bootstrap\SwooleServer;
use function Pinglet\Bootstrap\SwowServer;

require __DIR__ . '/../vendor/autoload.php';

if( !session_id() ) session_start();

### Start Swoole
$server = SwooleServer();
$server->start();

### Start Swow
//$server = SwowServer();
