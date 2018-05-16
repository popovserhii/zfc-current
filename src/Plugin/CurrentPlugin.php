<?php

namespace Popov\ZfcCurrent\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Popov\ZfcCurrent\CurrentHelper;

/**
 * Using current plugin
 * All references point out Popov\ZfcCurrent\CurrentHelper
 *
 * @author Serhii Popov
 */
class CurrentPlugin extends AbstractPlugin
{
    /**
     * @var CurrentHelper
     */
    protected $currentHelper;

    public function __construct(CurrentHelper $currentHelper)
    {
        $this->currentHelper = $currentHelper;
    }

    /**
     * @return CurrentHelper
     */
    public function getCurrentHelper()
    {
        #if (null === $this->currentHelper) {
        #    $sm = $this->getView()->getHelperPluginManager()->getServiceLocator();
        #    $this->currentHelper = $sm->get('ControllerPluginManager')->get('current');
        #}

        return $this->currentHelper;
    }

    public function __invoke()
    {
        $params = func_get_args();

        return call_user_func_array($this->getCurrentHelper(), $params);
    }
}