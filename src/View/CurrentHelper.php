<?php

namespace Popov\ZfcCurrent\View;

use Zend\View\Helper\AbstractHelper;
use Popov\ZfcCurrent\CurrentHelper as ZfcCurrentHelper;

/**
 * Using current plugin
 * All references point out Popov\ZfcCurrent\CurrentHelper
 *
 * @author Serhii Popov
 */
class CurrentHelper extends AbstractHelper
{
    /**
     * @var ZfcCurrentHelper
     */
    protected $currentHelper;

    /**
     * @param ZfcCurrentHelper $currentHelper
     */
    public function __construct(ZfcCurrentHelper $currentHelper)
    {
        $this->currentHelper = $currentHelper;
    }

    /**
     * @return CurrentHelper
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