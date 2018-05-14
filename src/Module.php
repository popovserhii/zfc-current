<?php
/**
 * ZfcCurrent module
 *
 * @category Popov
 * @package Popov_ZfcCurrent
 * @author Serhii Popov <popow.serhii@gmail.com>
 * @datetime: 25.07.14 15:04
 */
namespace Popov\ZfcCurrent;

use Zend\EventManager\EventInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Router\RouteMatch;

class Module implements ConfigProviderInterface
{
    public function getConfig()
    {
        $config = require __DIR__ . '/../config/module.config.php';
        $config['service_manager'] = $config['dependencies'];
        unset($config['dependencies']);

        return $config;
    }

    public function onBootstrap(MvcEvent $e)
    {
    $eventManager = $e->getApplication()->getEventManager();
    $moduleRouteListener = new ModuleRouteListener();
    $moduleRouteListener->attach($eventManager);
    $container = $e->getApplication()->getServiceManager();

    $sharedEventManager = $eventManager->getSharedManager();
    // Register the event listener method.
    //$sharedEventManager->attach(__NAMESPACE__, MvcEvent::EVENT_DISPATCH, function(MvcEvent $mvcEvent) use ($container) {
    $eventManager->attach( MvcEvent::EVENT_DISPATCH, function(MvcEvent $mvcEvent) use ($container) {
        $request = $mvcEvent->getRequest();
        /** @var RouteMatch $route */
        #$route = $container->get('Application')->getMvcEvent()->getRouteMatch();
        $route = $mvcEvent->getRouteMatch();

        /** @var CurrentHelper $currentHelper */
        $currentHelper = $container->get(CurrentHelper::class);

        $currentHelper->setResource($route->getParam('resource', 'index'));
        $currentHelper->setAction($route->getParam('action', 'index'));
        $currentHelper->setRequest($request);
        $currentHelper->setRoute($route);
        $currentHelper->setRouteName($route->getMatchedRouteName());
        $currentHelper->setRouteParams($route->getParams());


    }, 5000);
}

}