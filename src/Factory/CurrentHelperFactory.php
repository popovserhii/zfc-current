<?php
/**
 * ZfcCurrent Plugin Factory
 *
 * @category Popov
 * @package Popov_ZfcCurrent
 * @author Serhii Popov <popow.serhii@gmail.com>
 * @datetime: 23.05.2016 15:44
 */

namespace Popov\ZfcCurrent\Factory;

use Popov\ZfcCurrent\ModuleManager;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Application;
use Zend\Expressive\Template\TemplateRendererInterface;
use Popov\ZfcCurrent\CurrentHelper;

class CurrentHelperFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $isExpressive = class_exists(Application::class);

        $loadedModules = $isExpressive
            ? $container->get(ModuleManager::class)->getLoadedModules()
            : $container->get('ModuleManager')->getLoadedModules();

        $renderer = $isExpressive
            ? $container->get(TemplateRendererInterface::class)
            : $container->get('ViewRenderer');

        return (new CurrentHelper())
            ->setLoadedModules($loadedModules)
            ->setRenderer($renderer);
    }
}