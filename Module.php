<?php
/**
 * Current Agere module
 *
 * @category Agere
 * @package Agere_Current
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 25.07.14 15:04
 */
namespace Agere\Current;

use Zend\Mvc\ModuleRouteListener;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface {

	public function onBootstrap($e) {
		$e->getApplication()->getServiceManager()->get('translator');
		$eventManager = $e->getApplication()->getEventManager();
		$moduleRouteListener = new ModuleRouteListener();
		$moduleRouteListener->attach($eventManager);
	}

	public function getConfig() {
		return include __DIR__ . '/config/module.config.php';
	}

	public function getAutoloaderConfig() {
		return array(
			'Zend\Loader\StandardAutoloader' => array(
				'namespaces' => array(
					__NAMESPACE__ => str_replace('\\', '/', __DIR__ . '/src/' . __NAMESPACE__),
				),
			),
		);
	}

}