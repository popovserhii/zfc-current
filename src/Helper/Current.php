<?php
namespace Popov\ZfcCurrent\Helper;

use Zend\View\Helper\AbstractHelper;
use Popov\ZfcCurrent\Plugin\Current as CurrentPlugin;
 
/**
 * Using current plugin
 * 
 * All references point out Popov\ZfcCurrent\Plugin\ZfcCurrent
 * 
 * @author Sergiy Popov
 */
class Current extends AbstractHelper {
	
	/**
	 * @var CurrentPlugin
	 */
    protected $currentPlugin;

    /**
     * @param CurrentPlugin $currentPlugin
     * @return $this
     */
    public function setCurrentPlugin(CurrentPlugin $currentPlugin) {
        $this->currentPlugin = $currentPlugin;

        return $this;
    }

    /**
     * @return CurrentPlugin
     */
    public function getCurrentPlugin() {
        if (null === $this->currentPlugin) {
            $sm = $this->getView()->getHelperPluginManager()->getServiceLocator();
            $this->currentPlugin = $sm->get('ControllerPluginManager')->get('current');
        }

        return $this->currentPlugin;
    }
 
    public function __invoke() {
		$params = func_get_args();
        return call_user_func_array($this->getCurrentPlugin(), $params);
    }
}