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

use Zend\Stdlib\Exception;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Mvc\Router\RouteMatch;

class Current extends AbstractPlugin {

	const DEFAULT_NAME = 'module';

	protected $context;

	/**
	 * @param string $name
	 * @return string
	 * @throws Exception\RuntimeException
	 */
	public function run($name = self::DEFAULT_NAME) {
		/*if (!$name) {
			$name = self::DEFAULT_NAME;
		}*/

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
	 * @return string
	 * @throws Exception\RuntimeException
	 */
	public function currentModule() {
		static $cache;

		$context = $this->prepareContext();
		if (!isset($cache[$context])) {
			$delimeter = 'Controller\\'; // @todo can exclude to config
			$delimeterPos = strpos($context, $delimeter);

			if (false === $delimeterPos) {
				throw new Exception\RuntimeException(sprintf(
					'Cannot determine name for controller. Not found delimeter "%s" in class name: %s',
					$delimeter,
					$context
				));
			}

			$name = substr($context, 0, $delimeterPos - 1);
			$cache[$context] = $name;
		}

		return $cache[$context];
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
	 * @return RouteMatch
	 */
	public function currentRoute() {
		static $routeMatch;
		if (!$routeMatch) {
			//$routeMatch = $this->getSm()->get('Application')->getMvcEvent()->getRouteMatch();
			$routeMatch = $this->getController()->getEvent()->getRouteMatch();
		}
		return $routeMatch;
	}

	public function currentRouter() {
		static $router;
		if (!$router) {
			//$router = $this->getSm()->get('Application')->getMvcEvent()->getRouter();
			$router = $this->getController()->getEvent()->getRouter();
		}
		return $router;
	}

	public function currentRequest() {
		return $this->getController()->getRequest();
	}

	public function currentView() {
		static $viewRenderer;
		if (!$viewRenderer) {
			$viewRenderer = $this->getSm()->get('ViewRenderer');
		}
		return $viewRenderer;
	}

	/*public function __toString() {
		return $this->run();
	}*/

	public function __invoke() {
		if (!$args = func_get_args()) {
			return $this;
		}

		$name = isset($args[0]) ? $args[0] : self::DEFAULT_NAME;
		!isset($args[1]) || $this->setContext($args[1]);

		return $this->run($name);
	}

}