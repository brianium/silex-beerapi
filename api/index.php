<?php
require_once(__DIR__ . '/../vendor/autoload.php');
require_once('lib/mongo/connection.php');

use mongo\connection as mongo;

$load = include('lib/load.php');
$config = include __DIR__ . '/config/config.php';

//setup
$app = new Silex\Application();
$app->register(new \Silex\Provider\TwigServiceProvider(), ['twig.path' => __DIR__ . '/templates']);
$app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
    $twig->addGlobal('site', 'http://localhost:8080');
    return $twig;
}));
$app['db'] = $app->share(function() use ($config) {
    return mongo\db($config['mongo']);
});

//services
$load('services/problem.php', $app, $config);

//middleware
$load('middleware/json.php', $app, $config);

//routes
$load('routes/menu.php', $app, $config);
$load('routes/beertype.php', $app, $config);
$load('routes/beer.php', $app, $config);

$app->run();
