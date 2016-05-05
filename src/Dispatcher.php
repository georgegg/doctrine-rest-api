<?php
namespace pmill\Doctrine\Rest;

class Dispatcher
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param array $routeData
     * @return mixed
     * @throws \Exception
     */
    public function dispatchRoute(array $routeData)
    {
        if (!class_exists($routeData['controller'])) {
            throw new \Exception($routeData['controller'].' cannot be found');
        }

        return $this->container->call($routeData['controller'], $routeData['action'], $routeData['params']);
    }
}
