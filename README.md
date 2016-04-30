#ZF2 Current Module
This plugin allow get current namespace, module, controller, action, route, router and request through Controller Plugin or View Helper.

## Instalation
Add package to your composer.json
```json
{
    "require": {
        "popovsergiy/zfc-current": "dev-master"
    }
}
```

And update your vendors
```
$ php composer.phar update popovsergiy/zfc-current
```

## Usage

This library has Controller Plugin and View Helper which allow simple access to main ZF2 varialbes.

### Controller usage
```php
namespace YourModule\Controller

use Zend\Mvc\Controller\AbstractActionController;

class PostController extends AbstractActionController {

  public function indexAction() {
    $this->current('controller'); // post - controller name in module.config.php
    $this->current('action'); // index
    $this->current('module'); // YourModule  
    $this->current('route'); // RouteMatch object  
    $this->current('request'); // Request object
    
    $this->current()->currentModule(\Other\Module\Model\Entity::class); // Other\Module
  }
}
```

### View Usage
```php
// your-module/post/index.phtml

Curent controller : <?= $this->current('controller') ?>
Curent action : <?= $this->current('action') ?>
Curent module : <?= $this->current('module') ?>

<?php
$action = $this->url('default/id', [
	'controller' => $this->current('route')->getParam('controller'),
	'action' => $this->current('route')->getParam('action'),
	'id' => $this->current('route')->getParam('id'),
]);
// or
$current = $this->current();
$action = $this->url('default/id', [
	'controller' => $current('route')->getParam('controller'),
	'action' => $current('route')->getParam('action'),
	'id' => $current('route')->getParam('id'),
]);
?>
```
