<?php
namespace pmill\Doctrine\Rest\Middleware;

use Symfony\Component\HttpFoundation\Request;

interface MiddlewareInterface
{
    /**
     * @param Request $request
     * @param array $routeData
     */
    public function handle(Request &$request, array $routeData);
}
