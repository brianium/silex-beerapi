<?php
require_once(__DIR__ . '/../lib/beer/transform.php');
require_once(__DIR__ . '/../lib/mongo/module.php');

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use beer\transform as beer;

return function(Application $app, array $config) {
    $db = $app['db'];

    $app->get('/beer/{id}', function(Application $app, $id) use ($db) {
        $ops = [
            ['$unwind' => '$beers'],
            ['$match' => ['beers._id' => new \MongoId($id)]]
        ];
        $type = $db->beertypes->aggregate($ops);
        $beer = $type['result'][0]['beers'];
        if (! $beer)
            return $app['problem'](404, ['title' => 'Beer not found']);
        return $app['twig']->render('beer/resource.json', $beer);
    });

    $app->post('/beer/{id}/order' , function(Application $app, Request $request, $id) {
        $resp = array_merge($request->request->all(), ['beer' => $id]);
        return $app->json($resp);
    });
};
