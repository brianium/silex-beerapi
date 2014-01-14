<?php
use Silex\Application;
return function($path, Application $app, array $config) {
    $factory = include($path);
    $factory($app, $config);
};
