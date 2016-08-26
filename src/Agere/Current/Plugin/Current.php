<?php
/**
 * Plugin which allow get current names
 * such as namespace, module, controller, action
 *
 * All value is determined relative to current called controller
 *
 * @category Agere
 * @package Agere_Current
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 17.05.15 18:12
 */
namespace Agere\Current\Plugin;

use Doctrine\ORM\Tools\Export\ExportException;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Mvc\Router\RouteMatch;
use Zend\Stdlib\Exception;

class Current extends AbstractPlugin {

	const DEFAULT_NAME = 'module';

	protected $context;

    public function __construct(RouteMatch $route)
    {
        $this->route = $route;
    }

	/**
	 * @param string $name
	 * @return string
	 * @throws Exception\RuntimeException
	 */
	public function run($name = self::DEFAULT_NAME) {
		if (method_exists($this, $method = 'current' . ucfirst($name))) {
			return $this->{$method}();
		}

		throw new Exception\RuntimeException(sprintf(
			'Option with name %s is not supported. Allowed values: module, controller, action, router, route, request, view',
			$name
		));
	}

	protected function getSm() {
		return $this->getController()->getServiceLocator();
	}

	/**
	 * @param mixed $context Controller object or class name
	 */
	protected function setContext($context) {
        $this->context = $context;
	}

	public function resetContext() {
		$this->context = null;
	}

	public function getModule($context = null) {
		$modules = $this->getSm()->get('ModuleManager')->getLoadedModules();

		return $modules[$this->currentModule($context)];
	}

    protected function prepareContext() {
        $context = $this->context ?: get_class($this->getController());

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
	public function currentModule($context = null) {
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

		$modules = $this->getSm()->get('ModuleManager')->getLoadedModules();


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
	public function currentController() {
		return $this->currentRoute()->getParam('controller');
	}

	/**
	 * Get current route action name
	 *
	 * @return string
	 */
	public function currentAction() {
		return $this->currentRoute()->getParam('action');

    }

	/**
	 * Return current route match
	 *
	 * Important: Cache is disabled for correct work of forward plugin
	 *
	 * @return RouteMatch
	 */
	public function currentRoute() {
		//return $this->getController()->getEvent()->getRouteMatch();
		return $this->route;
	}

	public function currentRouter() {
		return $this->getController()->getEvent()->getRouter();
	}

	public function currentRequest() {
		return $this->getController()->getRequest();
	}

	public function currentView() {
		return $this->getSm()->get('ViewRenderer');
	}

	public function __invoke() {
		if (!$args = func_get_args()) {
			return $this;
		}

		$name = isset($args[0]) ? $args[0] : self::DEFAULT_NAME;
		!isset($args[1]) || $this->setContext($args[1]);

		return $this->run($name);
	}

}
