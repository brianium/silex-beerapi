<?php
require_once(__DIR__ . '/../lib/beertype/transform.php');
require_once(__DIR__ . '/../lib/beer/transform.php');
require_once(__DIR__ . '/../lib/mongo/module.php');

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use mongo\collection as collection;
use beertype\transform as type;
use beer\transform as beer;

return function(Application $app, array $config) {
    $db = $app['db'];

    $app->get('/beertype/{id}', function(Application $app, $id) use ($db) {
        $type = $db->beertypes->findOne(['_id' => new \MongoId($id)]);
        if (! $type)
            return $app['problem'](404, ['title' => 'Beer type not found']);
        return $app['twig']->render('beertype/resource.json', $type);
    });

    $app->get('/beertype', function(Application $app, Request $request) use ($db) {
        $results = collection\collection($request, $db->beertypes, 30, '/beertype');
        return $app['twig']->render('beertype/collection.json', $results);
    });

    $app->post('/beertype', function(Application $app, Request $request) use ($db) {
        $type = type\fromRequest($request);
        $menu = $db->menus->findOne(['_id' => $type['menu']]);
        if (!$menu)
            return $app['problem'](404, ['title' => 'Menu not found']);
        $db->beertypes->insert($type);
        $db->menus->update(
            ['_id' => $type['menu']],
            ['$addToSet' => ['beertypes' => $type['_id']]]
        );
        return $app['twig']->render('beertype/resource.json', $type);
    });

    $app->delete('/beertype/{id}', function($id) use ($db) {
        $db->beertypes->remove(['_id' => new \MongoId($id)]);
        return new Response(null, 204);
    });

    $app->put('/beertype/{id}', function(Application $app, Request $request, $id) use ($db) {
        $typeId = new \MongoId($id);
        $status = $db->beertypes->update(
            ['_id' => $typeId],
            ['$set' => ['name' => $request->request->get('name')]]
        );
        return ($status['updatedExisting'])
            ? $app['twig']->render('beertype/resource.json', $db->beertypes->findOne(['_id' => $typeId]))
            : $app['problem'](404, ['title' => 'Beer type not found']);
    });

    $app->post('/beertype/{id}/beer', function(Application $app, Request $request, $id) use ($db) {
        $typeId = new \MongoId($id);
        $beer = beer\fromRequest($request);
        $beer['beertype'] = $typeId;
        $beer['_id'] = new \MongoId();
        $status = $db->beertypes->update(
            ['_id' => $typeId],
            ['$addToSet' => ['beers' => $beer]]
        );
        return ($status['updatedExisting'])
            ? $app['twig']->render('beertype/resource.json', $db->beertypes->findOne(['_id' => $typeId]))
            : $app['problem'](404, ['title' => 'Beer type not found']); 
    });

    $app->get('/beertype/{id}/beer', function(Application $app, Request $request, $id) use ($db) {
        $cursor = $db->beertypes->findOne(['_id' => new \MongoId($id)]);
        $beers = $cursor["beers"];
        return $app['twig']->render('beer/collection.json', ['beers' => $beers, 'beertype' => $id]);
    });
};
