<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
return function(\Silex\Application $app, array $config) {
    $app->before(function (Request $request) {
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
            $request->request->replace(is_array($data) ? $data : array());
        }
    });

    $app->after(function(Request $request, Response $response) {
        $contentType = $response->headers->get('Content-Type');
        if (! preg_match('/json/', $contentType))
            $response->headers->set('Content-Type', 'application/hal+json');
    });
};
