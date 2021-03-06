<?php
/**
 * Helper which allow get current names
 * such as namespace, module, controller, action
 * All value is determined relative to current called controller
 *
 * @category Popov
 * @package Popov_ZfcCurrent
 * @author Serhii Popov <popow.serhii@gmail.com>
 * @datetime: 17.05.15 18:12
 */
namespace Popov\ZfcCurrent;

//use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Stdlib\Exception;

class CurrentHelper /*extends AbstractPlugin*/
{
    const DEFAULT_NAME = 'module';

    /**
     * @var string
     */
    protected $defaultContext;

    /**
     * @var string
     */
    protected $context;

    /**
     * @var string
     */
    protected $controller;

    /**
     * @var string
     */
    protected $action;

    protected $renderer;

    protected $request;

    protected $route;

    /** @var string */
    protected $routeName;

    /** @var array */
    protected $routeParams = [];
    
    /** @var array */
    protected $headers = [];

    protected $loadedModules;

    /**
     * Default context must be object (such as Controller or Action).
     *
     * @param object $defaultContext
     * @return $this
     */
    public function setDefaultContext($defaultContext)
    {
        $this->defaultContext = $defaultContext;

        return $this;
    }

    public function getDefaultContext()
    {
        return $this->defaultContext;
    }

    public function setController(string $controller)
    {
        $this->controller = $controller;

        return $this;
    }

    public function setAction(string $action)
    {
        $this->action = $action;

        return $this;
    }

    public function setRenderer($renderer)
    {
        $this->renderer = $renderer;

        return $this;
    }

    public function setRequest($request)
    {
        $this->request = $request;

        return $this;
    }

    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    public function setRouteParams($params)
    {
        $this->routeParams = $params;

        return $this;
    }

    public function setHeaders($headers)
    {
        $this->headers = $headers;

        return $this;
    }

    public function setRouteName($name)
    {
        $this->routeName = $name;

        return $this;
    }

    public function setLoadedModules(array $loadedModules)
    {
        $this->loadedModules = $loadedModules;

        return $this;
    }

    /**
     * @return array
     */
    public function getLoadedModules()
    {
        return $this->loadedModules;
    }

    /**
     * @param mixed $context Controller object or class name
     * @return $this
     */
    protected function setContext($context)
    {
        $this->context = $context;

        return $this;
    }

    public function resetContext()
    {
        $this->context = null;

        return $this;
    }


    /**
     * @param string $name
     * @return string
     * @throws Exception\RuntimeException
     */
    public function run($name = self::DEFAULT_NAME)
    {
        if (method_exists($this, $method = 'current' . ucfirst($name))) {
            return $this->{$method}();
        }
        throw new Exception\RuntimeException(sprintf(
            'Option with name %s is not supported. Allowed values: module, controller, action, router, route, request, view',
            $name
        ));
    }

    protected function prepareContext()
    {
        $context = $this->context ?: $this->defaultContext;
        if (is_object($context)) {
            $context = get_class($context);
        } elseif (is_array($context)) {
            $context = implode('\\', $context);
        }

        return $context;
    }

    /**
     * Get current module namespace
     *
     * @param mixed $context Object, namespace or array of exploded namespace
     * @return string
     * @throws Exception\RuntimeException
     */
    public function currentModule($context = null)
    {
        static $cache;
        if ($context) {
            $this->context = $context;
        }
        $context = $this->prepareContext();
        if ($this->context) {
            $this->context = null;
        }
        if (isset($cache[$context])) {
            return $cache[$context];
        }
        $modules = $this->getLoadedModules();
        $delimeter = '\\';
        $moduleName = '';
        $parts = explode($delimeter, $context);
        foreach ($parts as $part) {
            $moduleName = ltrim($moduleName . $delimeter . $part, $delimeter);
            if (isset($modules[$moduleName])) {
                $cache[$context] = $moduleName;
                break;
            }
        }
        if (!$moduleName) {
            throw new Exception\RuntimeException(sprintf('Not found appropriate module for context %s', $context));
        }

        return $moduleName;
    }

    /**
     * Get current route controller name
     *
     * @return string
     */
    public function currentController()
    {
        return $this->controller;
    }

    /**
     * Get current route action name
     *
     * @return string
     */
    public function currentAction()
    {
        return $this->action;
    }

    /**
     * Return current route match
     * Important: Cache is disabled for correct work of forward plugin
     *
     * @return object
     */
    public function currentRoute()
    {
        return $this->route;
    }

    /**
     * Get current route matched params
     *
     * @return array
     */
    public function currentRouteParams()
    {
        return $this->routeParams;
    }

    /**
     * Get current route matched name
     *
     * @return string
     */
    public function currentRouteName()
    {
        return $this->routeName;
    }

    /**
     * Ger current request's headers
     */
    public function currentHeaders()
    {
        if (!$this->headers) {
            $request = $this->currentRequest();
            if (method_exists($request, 'getHeaders')) {
                $this->headers = $request->getHeaders();
            }
        }

        return $this->headers;
    }

    public function currentRequest()
    {
        return $this->request;
    }

    public function currentRenderer()
    {
        return $this->renderer;
    }

    /**
     * @return $this|string
     */
    public function __invoke()
    {
        if (!$args = func_get_args()) {
            return $this;
        }
        $name = $args[0] ?? self::DEFAULT_NAME;
        !isset($args[1]) || $this->setContext($args[1]);

        return $this->run($name);
    }
}
