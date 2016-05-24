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
```sh
$ php composer.phar update popovsergiy/zfc-current
```

Don't forget Add `'Agere\Current'` to `config/application.config.php`
```php
return [
    'modules' => [
    	// ...
    	'Agere\Current'
    ]
]    
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

Current controller : <?= $this->current('controller') ?>
Current action : <?= $this->current('action') ?>
Current module : <?= $this->current('module') ?>

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

### Tricks and tips
If you use forward plugin and need current (forward) param, next call return expected value
```php
$this->current()->getController()->getEvent()->getRouteMatch()->getParam('action');
```
In all other case you get real params that is in URL
