<?php
use pmill\Doctrine\Rest\App;

$autoloader = require_once "../vendor/autoload.php";

$app = new App($autoloader, 'config');
$result = $app->run();

var_dump($result);
