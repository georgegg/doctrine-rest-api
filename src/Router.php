<?php
namespace pmill\Doctrine\Rest;

use Doctrine\Common\Annotations\Reader;
use pmill\Doctrine\Rest\Controller\CollectionController;
use pmill\Doctrine\Rest\Controller\EntityController;
use pmill\Doctrine\Rest\Annotation\Method as MethodAnnotation;
use pmill\Doctrine\Rest\Annotation\Route as RouteAnnotation;
use pmill\Doctrine\Rest\Exception\RestException;
use pmill\Doctrine\Rest\Helper\FileHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
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
     * @var array
     */
    protected $config;

    /**
     * @param Doctrine $doctrine
     * @param array $routerConfig
     * @param Request $request
     */
    public function __construct(Doctrine $doctrine, array $routerConfig, Request $request = null)
    {
        $this->config = $routerConfig;
        $this->symfonyRouter = $this->createRouter($doctrine, $request);
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws RestException
     */
    public function match(Request $request = null)
    {
        if (is_null($request)) {
            $request = Request::createFromGlobals();
        }

        try {
            $matchedRoute = $this->symfonyRouter->matchRequest($request);
        } catch (ResourceNotFoundException $e) {
            throw new RestException('The requested url could not be handled', 404);
        } catch (MethodNotAllowedException $e) {
            throw new RestException('The requested url does not respond to the given HTTP method', 405);
        }

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
        if (!method_exists($controller, $action)) {
            return;
        }

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

        $this->symfonyRouteCollection = new RouteCollection();
        $this->routes = [];

        $this->generateRoutesFromEntities($doctrine->getAnnotationReader(), $doctrine->getEntityClasses());
        $this->generateRoutesFromControllers($doctrine->getAnnotationReader());

        return new UrlMatcher($this->symfonyRouteCollection, $context);
    }

    /**
     * @param Reader $annotationReader
     * @param array $entityClasses
     */
    protected function generateRoutesFromEntities(Reader $annotationReader, array $entityClasses)
    {
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

        /** @var RouteAnnotation $routeAnnotation */
        if ($routeAnnotation = $annotationReader->getClassAnnotation($reflectionClass, RouteAnnotation::class)) {
            foreach ($entityMethods as $method) {
                $routeName = $entityClass . '::entity::'.$method;
                $this->addRoute($routeName, $method, $routeAnnotation->entity, self::$DEFAULT_ENTITY_CONTROLLER, $method.'Action', [
                    'entityClass' => $entityClass,
                ]);
            }

            foreach ($collectionMethods as $method) {
                $routeName = $entityClass . '::collection::'.$method;
                $this->addRoute($routeName, $method, $routeAnnotation->collection, self::$DEFAULT_COLLECTION_CONTROLLER, $method.'Action', [
                    'entityClass' => $entityClass,
                ]);
            }
        }
    }

    /**
     * @param Reader $annotationReader
     */
    public function generateRoutesFromControllers(Reader $annotationReader)
    {
        if (!isset($this->config['controllerPaths'])) {
            return;
        }

        if (!is_array($this->config['controllerPaths'])) {
            $this->config['controllerPaths'] = [$this->config['controllerPaths']];
        }

        $fileHelper = new FileHelper();

        foreach ($this->config['controllerPaths'] as $path) {
            $files = $fileHelper->findFilesInFolder($path);
            foreach ($files as $filename) {
                $namespaces = $fileHelper->findClassesInFile($filename);
                foreach ($namespaces as $namespace => $classes) {
                    foreach ($classes as $className) {
                        $this->findRoutesInClass($annotationReader, $namespace . '\\' . $className);
                    }
                }
            }
        }
    }

    /**
     * @param Reader $annotationReader
     * @param $className
     */
    protected function findRoutesInClass(Reader $annotationReader, $className)
    {
        $reflectionClass = new \ReflectionClass($className);
        $methods = $reflectionClass->getMethods();

        foreach ($methods as $method) {
            if ($routeAnnotation = $annotationReader->getMethodAnnotation($method, RouteAnnotation::class)) {
                $httpMethodAnnotation = $annotationReader->getMethodAnnotation($method, MethodAnnotation::class);
                $httpMethod = is_null($httpMethodAnnotation) ? 'GET' : $httpMethodAnnotation->value;
                $routeName = $className . '::' . $method->name;

                $this->addRoute($routeName, $httpMethod, $routeAnnotation->value, $className, $method->name);
            }
        }
    }
}
