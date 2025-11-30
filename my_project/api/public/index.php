<?php

use App\Kernel;
use App\Kernel\Container;
use App\Kernel\Component\Request;
use App\Kernel\Component\RequestStack;

require dirname(__DIR__).'/config/errors.php';
require dirname(__DIR__).'/config/bootstrap.php';


$services = require dirname(__DIR__).'/config/services.php';
$routes = require dirname(__DIR__).'/config/routes.php';
$config = require dirname(__DIR__).'/config/config.php';

$requestStack = new RequestStack();
$container = new Container();
$kernel = new Kernel($requestStack, $container);
$kernel->autowiring($services);
$kernel->loadRoutes($routes);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
