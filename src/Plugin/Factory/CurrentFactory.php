<?php
/**
 * ZfcCurrent Plugin Factory
 *
 * @category Popov
 * @package Popov_ZfcCurrent
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 23.05.2016 15:44
 */
namespace Popov\ZfcCurrent\Plugin\Factory;

use Zend\ServiceManager\ServiceLocatorInterface;
use Popov\ZfcCurrent\Plugin\ZfcCurrent;

class ZfcCurrentFactory
{
    public function __invoke(ServiceLocatorInterface $cpm)
    {
        $sm = $cpm->getServiceLocator();
        $loadedModules = $sm->get('ModuleManager')->getLoadedModules();
        $route = $sm->get('Application')->getMvcEvent()->getRouteMatch();
        $viewRenderer = $sm->get('ViewRenderer');

        return new ZfcCurrent($loadedModules, $route, $viewRenderer);
    }
}