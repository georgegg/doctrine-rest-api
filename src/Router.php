<?php
namespace pmill\Doctrine\Rest;

use Doctrine\Common\Annotations\Reader;
use pmill\Doctrine\Rest\Annotation\Url;
use pmill\Doctrine\Rest\Controller\CollectionController;
use pmill\Doctrine\Rest\Controller\EntityController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class Router
{
    public static $DEFAULT_ENTITY_CONTROLLER = EntityController::class;
    public static $DEFAULT_ENTITY_METHODS = ['get', 'patch', 'delete'];

    public static $DEFAULT_COLLECTION_CONTROLLER = CollectionController::class;
    public static $DEFAULT_COLLECTION_METHODS = ['get', 'post'];
    
    /**
     * @var \Symfony\Component\Routing\Router
     */
    protected $symfonyRouter;

    /**
     * @var RouteCollection
     */
    protected $symfonyRouteCollection;

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

        $matchedRoute = $this->symfonyRouter->matchRequest($request);
        $routeName = $matchedRoute['_route'];
        unset($matchedRoute['_route']);

        $routeData = $this->routes[$routeName];
        $routeData['params'] = array_merge($matchedRoute, $routeData['params']);

        return $routeData;
    }

    /**
     * @param $method
     * @param $url
     * @param $controller
     * @param $action
     * @param array $params
     */
    public function addRoute($name, $method, $url, $controller, $action, array $params = [])
    {
        $this->routes[$name] = [
            'controller' => $controller,
            'action' => $action,
            'url' => $url,
            'params' => $params,
        ];
        $this->symfonyRouteCollection->add($name, new Route($url, [], [], [], '', [], [$method]));
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

        $this->generateRoutesFromEntities($doctrine->getAnnotationReader(), $doctrine->getEntityClasses());
        return new UrlMatcher($this->symfonyRouteCollection, $context);
    }

    /**
     * @param Reader $annotationReader
     * @param array $entityClasses
     */
    protected function generateRoutesFromEntities(Reader $annotationReader, array $entityClasses)
    {
        $this->symfonyRouteCollection = new RouteCollection();
        $this->routes = [];

        foreach ($entityClasses as $entityClass) {
            $this->generateRoutesFromEntity($annotationReader, $entityClass);
        }
    }

    /**
     * @param Reader $annotationReader
     * @param $entityClass
     */
    protected function generateRoutesFromEntity(Reader $annotationReader, $entityClass)
    {
        $entityMethods = self::$DEFAULT_ENTITY_METHODS;
        $collectionMethods = self::$DEFAULT_COLLECTION_METHODS;
        $reflectionClass = new \ReflectionClass($entityClass);

        /** @var Url $urlAnnotation */
        if ($urlAnnotation = $annotationReader->getClassAnnotation($reflectionClass, Url::class)) {
            foreach ($entityMethods as $method) {
                $routeName = $entityClass . '::entity::'.$method;
                $this->addRoute($routeName, $method, $urlAnnotation->entity, self::$DEFAULT_ENTITY_CONTROLLER, $method.'Action', [
                    'entityClass' => $entityClass,
                ]);
            }

            foreach ($collectionMethods as $method) {
                $routeName = $entityClass . '::collection::'.$method;
                $this->addRoute($routeName, $method, $urlAnnotation->collection, self::$DEFAULT_COLLECTION_CONTROLLER, $method.'Action', [
                    'entityClass' => $entityClass,
                ]);
            }
        }
    }
}
