# Pinglet
Pinglet: A lightweight and high-performance PHP microframework framework that combines the simplicity of Slim with the power of Swoole or Swow for lightning-fast web apps.

With Pinglet, you are free to create high-speed RESTful APIs, CRUD applications, microservices, and control panels.

Pinglet is developed in 2023 by Taras Kondratyuk and based on [slim-swoole-project](https://github.com/skoro/slim-swoole-project) by Oleksii Skorobogatko

## Requirements

* PHP 8.1
* Swoole or Swow

## Installation

Clone this repository, or use [composer](https://getcomposer.org/):
```
$ composer create-project pinga/pinglet <project-path>
```

## Configuration

Edit the file ```env-sample```, then rename it to ```.env```

## Starting the server

For Swoole:
```
phpenmod swoole
php bin/swoole.php
```

For Swow:
```
phpenmod swow
php bin/swow.php
```

## Benchmark

### Swoole

```
wrk -t16 -c 100 -d 30s http://-.-.-.-:3000/
Running 30s test @ http://-.-.-.-:3000/
  16 threads and 100 connections
  Thread Stats   Avg      Stdev     Max   +/- Stdev
    Latency     8.59ms    2.91ms  46.66ms   85.37%
    Req/Sec   705.79    123.21     0.97k    62.77%
  337394 requests in 30.02s, 58.88MB read
Requests/sec:  11237.30
Transfer/sec:      1.96MB
```

### Swow

```
wrk -t16 -c 100 -d 30s http://-.-.-.-:3000/
Running 30s test @ http://-.-.-.-:3000/
  16 threads and 100 connections
  Thread Stats   Avg      Stdev     Max   +/- Stdev
    Latency     6.16ms    7.46ms 321.37ms   99.51%
    Req/Sec     1.04k   116.36     2.27k    77.20%
  494930 requests in 30.04s, 35.40MB read
Requests/sec:  16476.79
Transfer/sec:      1.18MB
```
