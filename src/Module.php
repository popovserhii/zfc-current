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

use Zend\Psr7Bridge\Psr7ServerRequest;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Router\RouteMatch;
use Zend\Mvc\Controller\AbstractController;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

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
        $app = $e->getApplication();
        $container = $app->getServiceManager();
        $eventManager = $app->getEventManager();
        $sharedEvents = $eventManager->getSharedManager();

        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        // Register the event listener method.
        $sharedEvents->attach(AbstractController::class,MvcEvent::EVENT_DISPATCH, function(MvcEvent $mvcEvent) use ($container) {
            // The ZfcCurrent module is adapted for ZF3 MVC and ZF3 Expressive
            // such well as support ZF3+Middleware combination.
            // Keep in mind if you use ZF3+Middleware combination you will get Zend\Mvc\Controller\MiddlewareController
            // in "defaultContext" of Current object on MvcEvent::EVENT_DISPATCH such as it is initialized before
            // Middleware Action will be created.
            // After Middleware Action will be recognized and created in Stagem\ZfcAction\Page\ConnectivePage
            // the "defaultContext" will be replaced with relative Middleware Action object.

            /** @var RouteMatch $route */
            $controller = $mvcEvent->getTarget();
            $route = $mvcEvent->getRouteMatch();
            $request = $mvcEvent->getRequest();

            if ($request instanceof \Zend\Http\Request) {
                $request = Psr7ServerRequest::fromZend($request);
            }

            /** @var CurrentHelper $currentHelper */
            $currentHelper = $container->get(CurrentHelper::class);
            $currentHelper->setDefaultContext($controller);
            $currentHelper->setController($route->getParam('controller', 'index'));
            $currentHelper->setAction($route->getParam('action', 'index'));
            $currentHelper->setRequest($request);
            $currentHelper->setRoute($route);
            $currentHelper->setRouteName($route->getMatchedRouteName());
            $currentHelper->setRouteParams($route->getParams());
        }, 5000);
    }
}