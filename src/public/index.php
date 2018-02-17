<?php
# Consul.conf
## (c) 2018 Łukasz Lach <llach@llach.pl>
## https://github.com/lukaszlach/consul-conf

use Phalcon\Loader;
use Phalcon\Di\FactoryDefault as Di;
use ConsulConf\Application;

require __DIR__.'/../../vendor/autoload.php';
$di     = new Di();
$loader = new Loader();
$loader->registerNamespaces([
    "ConsulConf" => __DIR__."/../ConsulConf/",
]);
$loader->register();

$application = new Application();
$application->setDI($di);
$application->bootstrap();
$application->handle();