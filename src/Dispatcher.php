<?php
namespace pmill\Doctrine\Rest;

class Dispatcher
{
    public function dispatchRoute(array $routeData)
    {
        $controller = $this->fetchController($routeData['controller']);
    }

    protected function fetchController($controllerClass)
    {
        if (!class_exists($controllerClass)) {

        }
    }
}
