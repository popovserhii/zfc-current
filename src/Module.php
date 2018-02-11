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

use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements ConfigProviderInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}