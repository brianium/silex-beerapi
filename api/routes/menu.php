<?php
require_once(__DIR__ . '/../lib/menu/transform.php');
require_once(__DIR__ . '/../lib/beertype/transform.php');
require_once(__DIR__ . '/../lib/mongo/module.php');

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use menu\transform as transform;
use mongo\collection as collection;
use beertype\transform as type;

return function(Application $app, array $config) {
    $db = $app['db'];
 
    $app->get('/menu', function(Request $request) use ($db, $app) {
        $menus = collection\collection($request, $db->menus, 30, '/menu');
        return $app['twig']->render('menu/collection.json', $menus);
    });

    $app->get('/menu/{id}', function(Application $app, $id) use ($db) {
        $menu = $db->menus->findOne(['_id' => new \MongoId($id)]);
        return (!$menu) 
            ? $app['problem'](404, ['title' => 'Menu not found'])
            : $app['twig']->render('menu/resource.json', $menu);        
    });

    $app->put('/menu/{id}', function(Application $app, Request $request, $id) use ($db) {
        $status = $db->menus->update(
            ['_id' => new \MongoId($id)],
            ['$set' => ['name' => $request->request->get('name')]]
        );
        return ($status['updatedExisting']) 
            ? $app['twig']->render('menu/resource.json', $db->menus->findOne(['_id' => new \MongoId($id)]))
            : $app['problem'](404, ['title' => 'Menu not found']);
    });

    $app->delete('/menu/{id}', function($id) use ($db) {
        $db->menus->remove(['_id' => new \MongoId($id)]);
        return new Response(null, 204);
    });

    $app->post('/menu', function(Request $request) use ($db, $app) {
        $menu = transform\fromRequest($request);
        $db->menus->insert($menu);
        return $app['twig']->render('menu/resource.json', $menu);        
    });

    $app->post('/menu/{id}/beertype', function(Application $app, Request $request, $id) use ($db) {
        $menuId = new \MongoId($id);
        $beertype = type\fromRequest($request);
        $beertype['menu'] = $menuId;
        $db->beertypes->insert($beertype);
        $status = $db->menus->update(
            ['_id' => $menuId],
            ['$addToSet' => ['beertypes' => $beertype['_id']]]
        );
        return ($status['updatedExisting'])
            ? $app['twig']->render('menu/resource.json', $db->menus->findOne(['_id' => $menuId]))
            : $app['problem'](404, ['title' => 'Menu not found']);
    });

    $app->get('/menu/{id}/beertype', function(Application $app, Request $request, $id) use ($db) {
        $cursor = $db->beertypes->find(['menu' => new \MongoId($id)]);
        $types = collection\collection($request, $cursor, 30, '/menu/' . $id . '/beertype');
        return $app['twig']->render('beertype/collection.json', $types); 
    });
};
