<?php
namespace pmill\Doctrine\Rest;

use Doctrine\Common\Annotations\Reader;
use pmill\Doctrine\Rest\Annotation\Url;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class Router
{
    /**
     * @var \Symfony\Component\Routing\Router
     */
    protected $symfonyRouter;

    /**
     * @var array
     */
    protected $routes = [];

    /**
     * @param Doctrine $doctrine
     * @param Request $request
     */
    public function __construct(Doctrine $doctrine, Request $request = null)
    {
        $this->symfonyRouter = $this->createRouter($doctrine, $request);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function match(Request $request = null)
    {
        if (is_null($request)) {
            $request = Request::createFromGlobals();
        }

        return $this->symfonyRouter->matchRequest($request);
    }

    /**
     * @param Doctrine $doctrine
     * @param Request $request
     * @return UrlMatcher
     */
    protected function createRouter(Doctrine $doctrine, Request $request = null)
    {
        if (is_null($request)) {
            $request = Request::createFromGlobals();
        }

        $context = new RequestContext();
        $context->fromRequest($request);

        $routes = $this->generateRoutesFromEntities($doctrine->getAnnotationReader(), $doctrine->getEntityClasses());
        return new UrlMatcher($routes, $context);
    }

    /**
     * @param Reader $annotationReader
     * @param array $entityClasses
     * @return RouteCollection
     */
    protected function generateRoutesFromEntities(Reader $annotationReader, array $entityClasses)
    {
        $routes = new RouteCollection();
        $this->routes = [];

        foreach ($entityClasses as $entityClass) {
            $reflectionClass = new \ReflectionClass($entityClass);

            /** @var Url $urlAnnotation */
            if ($urlAnnotation = $annotationReader->getClassAnnotation($reflectionClass, Url::class)) {
                $this->routes[$entityClass.'::entity'] = [
                    'entity' => $entityClass,
                    'controller' => '',
                    $urlAnnotation->entity
                ];
                $routes->add($entityClass.'::entity', new Route($urlAnnotation->entity));

                $this->routes[$entityClass.'::collection'] = [
                    'entity' => $entityClass,
                    'controller' => '',
                    $urlAnnotation->collection
                ];
                $routes->add($entityClass.'::collection', new Route($urlAnnotation->collection));
            }
        }

        return $routes;
    }
}
