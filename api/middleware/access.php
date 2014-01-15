<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
return function(\Silex\Application $app, array $config) {
    $app->after(function(Request $request, Response $response) {
        $response->headers->set('Access-Control-Allow-Origin', '*');
    });
};
