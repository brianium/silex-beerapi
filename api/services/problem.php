<?php
use Symfony\Component\HttpFoundation\Response;
return function(\Silex\Application $app, array $config) {
    $app['problem'] = $app->protect(function($status, array $data = []) {
        $data['httpStatus'] = $status;
        $data = array_merge(
            $data,
            ['describedBy' => "http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html"]
        );
        return new Response(json_encode($data), $status, ['Content-Type' => 'application/api-problem+json']);
    });
};
