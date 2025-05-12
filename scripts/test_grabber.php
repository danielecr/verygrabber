<?php


require_once "../vendor/autoload.php";

use \smrtg\VeryGrabber\GrabFromSchema;

$doc = file_get_contents('file.html');
$grab = new GrabFromSchema($doc);

$schema = file_get_contents('schema.json');

$data = $grab->getStruct($schema);
print_r($data);

