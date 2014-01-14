<?php
namespace mongo\connection;

function db(array $config) {
  $mongo = new \MongoClient("mongodb://" . $config['host']);
  return $mongo->{$config['dbname']};
}
