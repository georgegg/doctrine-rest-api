<?php
use pmill\Doctrine\Rest\App;
use Symfony\Component\HttpFoundation\Request;

date_default_timezone_set('Europe/London');

$autoloader = require_once "../vendor/autoload.php";

$app = new App($autoloader, 'config');

$request  = Request::createFromGlobals();
$response = $app->handle($request);

$response->send();

