<?php
namespace pmill\Doctrine\Rest;

use pmill\Doctrine\Rest\Middleware\AuthenticationMiddleware;
use pmill\Doctrine\Rest\Middleware\MiddlewareInterface;
use Symfony\Component\HttpFoundation\Request;

class Dispatcher
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var array|MiddlewareInterface[]
     */
    protected $middleware = [];

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->registerMiddleware();
    }

    /**
     * @param Request $request
     * @param array $routeData
     * @return mixed
     * @throws \Exception
     */
    public function dispatchRoute(Request $request, array $routeData)
    {
        if (!class_exists($routeData['controller'])) {
            throw new \Exception($routeData['controller'].' cannot be found');
        }

        foreach ($this->middleware as $middleware) {
            $middleware->handle($request, $routeData);
        }

        return $this->container->call($routeData['controller'], $routeData['action'], $routeData['params']);
    }

    public function registerMiddleware()
    {
        $this->middleware[] = $this->container->make(AuthenticationMiddleware::class);
    }
}
