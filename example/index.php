<?php
use pmill\Doctrine\Rest\App;

$autoloader = require_once "../vendor/autoload.php";

$app = new App($autoloader, 'config');
$result = $app->run();

echo json_encode($result);
die();
