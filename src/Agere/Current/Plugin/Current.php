<?php
/**
 * Plugin which allow get current names of something
 * as namespace, module, controller, action
 *
 * @category Agere
 * @package Agere_Current
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 17.05.15 18:12
 */

namespace Agere\Current\Plugin;

use Zend\Stdlib\Exception;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class Current extends AbstractPlugin {

	protected $defaultName = 'moduleNamespace';


	public function run($name = '', $context = null) {
		if (!$name) {
			$name = $this->defaultName;
		}

		if (method_exists($this, $name)) {
			return $this->{$name}($context);
		}

		return '';
	}

	protected function prepareContext($context) {
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
	 * @param null $context Controller object or class name
	 * @return mixed
	 * @throws Exception\RuntimeException
	 */
	public function moduleNamespace($context = null) {
		static $cache;

		$context = $this->prepareContext($context ?: get_class($this->getController()));
		if (!isset($cache[$context])) {
			$delimeter = 'Controller\\'; // @todo can exclude to config
			$delimeterPos = strpos($context, $delimeter);

			if (false === $delimeterPos) {
				throw new Exception\RuntimeException(sprintf('Cannot determine name for controller. Not found delimeter "%s" in class name: %s',
					$delimeter,
					$context
				));
			}

			$name = substr($context, 0, $delimeterPos - 1);
			$cache[$context] = $name;
		}

		return $cache[$context];
	}

	public function __toString() {
		return $this->run();
	}

	public function __invoke() {
		$args = func_get_args();

		if (!$args) {
			return $this;
		}

		$name = isset($args[0]) ? $args[0] : null;
		$context = isset($args[1]) ? $args[1] : null;

		return $this->run($name, $context);
	}

}