<?php
namespace Popov\ZfcCurrent\Helper;

use Zend\View\Helper\AbstractHelper;
use Popov\ZfcCurrent\Plugin\ZfcCurrent as ZfcCurrentPlugin;
 
/**
 * Using current plugin
 * 
 * All references point out Popov\ZfcCurrent\Plugin\ZfcCurrent
 * 
 * @author Sergiy Popov
 */
class ZfcCurrent extends AbstractHelper {
	
	/**
	 * @var ZfcCurrentPlugin
	 */
    protected $currentPlugin;

    /**
     * @param ZfcCurrentPlugin $currentPlugin
     * @return $this
     */
    public function setZfcCurrentPlugin(ZfcCurrentPlugin $currentPlugin) {
        $this->currentPlugin = $currentPlugin;

        return $this;
    }

    /**
     * @return ZfcCurrentPlugin
     */
    public function getZfcCurrentPlugin() {
        if (null === $this->currentPlugin) {
            $sm = $this->getView()->getHelperPluginManager()->getServiceLocator();
            $this->currentPlugin = $sm->get('ControllerPluginManager')->get('current');
        }

        return $this->currentPlugin;
    }
 
    public function __invoke() {
		$params = func_get_args();
        return call_user_func_array($this->getZfcCurrentPlugin(), $params);
    }
}