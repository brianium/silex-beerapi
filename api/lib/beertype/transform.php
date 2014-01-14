<?php
namespace beertype\transform;
use Symfony\Component\HttpFoundation\Request;

function fromRequest(Request $request) {
    $fields = [
        'name' => $request->request->get('name'),
    ];
    if ($request->request->has('menu'))
        $fields['menu'] = new \MongoId($request->request->get('menu'));
    return $fields;
};
