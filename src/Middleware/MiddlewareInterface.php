<?php
namespace pmill\Doctrine\Rest\Middleware;

use Symfony\Component\HttpFoundation\Request;

interface MiddlewareInterface
{
    /**
     * @param Request $request
     */
    public function handle(Request &$request);
}
