<?php
require(__DIR__ . '/../vendor/autoload.php');

$load = include('lib/load.php');
$app = new Silex\Application();
$config = include __DIR__ . '/config/config.php';

//configuration
$app['debug'] = true;
$app->register(new \Silex\Provider\TwigServiceProvider(), ['twig.path' => __DIR__ . '/templates']);
$app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
    $twig->addGlobal('site', 'http://localhost:8080');
    return $twig;
}));

//services
$load('services/problem.php', $app, $config);

//middleware
$load('middleware/json.php', $app, $config);

//routes
$load('routes/menu.php', $app, $config);

$app->run();
