<?php
namespace mongo\collection;
use Symfony\Component\HttpFoundation\Request;

function collection(Request $request, $cursorOrCollection, $limit, $path) {
    $page = $request->query->has('page') ? $request->query->get('page') : 1;
    $cursor = ($cursorOrCollection instanceof \MongoCollection)
        ? $cursorOrCollection->find()
        : $cursorOrCollection; 
    $count = $cursor->count();
    $results = iterator_to_array($cursor->limit($limit)->skip((int)($limit * ($page - 1))));
    return array_merge(
        ['results' => $results, 'collection_count' => $count, 'path' => $path],
        paginate($path, $page, $count, $limit)
    );
}

function paginate($path, $page, $count, $limit) {
    $last = ceil($count / $limit);
    return [
        'current_page_href' => $path . (($page > 1) ? '?page=' . $page : ''),
        'next_page_href' => ($page + 1 > $last) ? '' : "$path?page=" . ($page + 1),
        'prev_page_href' => ($page > 1) ? "$path?page=" . ($page - 1) : '',
        'last_page_href' => "$path?page=" . $last,
        'per_page' => $limit,
        'current_page' => $page
    ];    
}
