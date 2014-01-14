<?php
namespace beertype\transform;
use Symfony\Component\HttpFoundation\Request;

function fromRequest(Request $request) {
    return [
        'name' => $request->request->get('name'),
    ];
};
