<?php
/**
 * Current Plugin Factory
 *
 * @category Agere
 * @package Agere_Current
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 23.05.2016 15:44
 */
namespace Agere\Current\Plugin\Factory;

use Zend\ServiceManager\ServiceLocatorInterface;
use Agere\Current\Plugin\Current;

class CurrentFactory
{
    public function __invoke(ServiceLocatorInterface $cpm)
    {
        $sm = $cpm->getServiceLocator();
        $loadedModules = $sm->get('ModuleManager')->getLoadedModules();
        $route = $sm->get('Application')->getMvcEvent()->getRouteMatch();
        $viewRenderer = $sm->get('ViewRenderer');

        return new Current($loadedModules, $route, $viewRenderer);
    }
}