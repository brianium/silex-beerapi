<?php
namespace beer\transform;
use Symfony\Component\HttpFoundation\Request;

function fromRequest(Request $request) {
    $fields = [
        'name' => $request->request->get('name'),
        'abv' => $request->request->get('abv'),
        'brewer' => $request->request->get('brewer'),
        'price' => $request->request->get('price')
    ];
    if ($request->request->has('beertype'))
        $fields['beertype'] = new \MongoId($request->request->get('beertype'));
    return $fields;
};
