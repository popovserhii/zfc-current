<?php

namespace Popov\ZfcCurrent\Helper;

use Zend\View\Helper\AbstractHelper;
use Popov\ZfcCurrent\Plugin\CurrentHelper;

/**
 * Using current plugin
 * All references point out Popov\ZfcCurrent\Helper\CurrentHelper
 *
 * @author Serhii Popov
 */
class CurrentPlugin extends AbstractHelper
{
    /**
     * @var CurrentHelper
     */
    protected $currentHelper;

    /**
     * @param CurrentHelper $currentHelper
     * @return $this
     */
    public function setCurrentHelper(CurrentHelper $currentHelper)
    {
        $this->currentHelper = $currentHelper;

        return $this;
    }

    /**
     * @return CurrentPlugin
     */
    public function getCurrentHelper()
    {
        if (null === $this->currentHelper) {
            $sm = $this->getView()->getHelperPluginManager()->getServiceLocator();
            $this->currentHelper = $sm->get('ControllerPluginManager')->get('current');
        }

        return $this->currentHelper;
    }

    public function __invoke()
    {
        $params = func_get_args();

        return call_user_func_array($this->getCurrentHelper(), $params);
    }
}