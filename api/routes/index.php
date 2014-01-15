<?php
require_once(__DIR__ . '/../lib/mongo/module.php');

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

return function(Application $app, array $config) {

    $app->get('/', function(Application $app) {
        return $app['twig']->render('index.json');
    });

};
