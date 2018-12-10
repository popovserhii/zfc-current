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
use Zend\Http\Request as HttpRequest;
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


        // MvcEvent::EVENT_DISPATCH arise immediately after Controller has been created
        // We must set Request before MvcEvent::EVENT_DISPATCH for compatibility with ControllerFactory.
        // This allows has prepared Request in Current, otherwise we get NULL on $current->getRequest().

        /** @var CurrentHelper $currentHelper */
        $currentHelper = $container->get(CurrentHelper::class);
        if (($request = $e->getRequest()) instanceof HttpRequest) {
            $request = Psr7ServerRequest::fromZend($request);
            $currentHelper->setHeaders($request->getHeaders());
        }
        $currentHelper->setRequest($request);

        // Register the event listener method.
        $sharedEvents->attach(AbstractController::class, MvcEvent::EVENT_DISPATCH, function(MvcEvent $mvcEvent) use ($currentHelper) {
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

            $currentHelper->setDefaultContext($controller);
            $currentHelper->setController($route->getParam('controller', 'index'));
            $currentHelper->setAction($route->getParam('action', 'index'));
            $currentHelper->setRoute($route);
            $currentHelper->setRouteName($route->getMatchedRouteName());
            $currentHelper->setRouteParams($route->getParams());
        }, 5000);
    }
}