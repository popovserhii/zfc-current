<?php
/**
 * The MIT License (MIT)
 * Copyright (c) 2018 Serhii Popov
 * This source file is subject to The MIT License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/MIT
 *
 * @category Popov
 * @package Popov_ZfcCurrent
 * @author Serhii Popov <popow.serhii@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Popov\ZfcCurrent;

use Popov\ZfcEntity\Helper\ModuleHelper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Stagem\ZfcAction\Page\RendererMiddleware;
use Zend\Expressive\Router\RouteResult;
use Zend\Filter\Word\DashToCamelCase;
use Zend\Stdlib\Exception\RuntimeException;
use Stagem\ZfcAction\Page\ConnectivePage;

class CurrentMiddleware implements MiddlewareInterface
{
    /**
     * @var CurrentHelper
     */
    protected $currentHelper;

    /**
     * @var ModuleHelper
     */
    protected $moduleHelper;

    /**
     * @var array
     */
    protected $config;

    public function __construct(
        CurrentHelper $currentHelper,
        ModuleHelper $moduleHelper,
        array $config = []
    )
    {
        $this->config = $config;
        $this->currentHelper = $currentHelper;
        $this->moduleHelper = $moduleHelper;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->configureCurrentPlugin($request);

        //$filter = new DashToCamelCase();




        return $handler->handle($request);
    }

    protected function getActionClass($request)
    {
        $name = [];
        //$name['resource'] = lcfirst($this->currentHelper->currentResource());
        $name['namespace'] = $this->getNamespace(lcfirst($this->currentHelper->currentResource()));
        $name['dir'] = 'Action';
        //$area = $route->getOptions()['area'] ?? RendererMiddleware::AREA_DEFAULT;
        $area = $request->getAttribute('area', RendererMiddleware::AREA_DEFAULT);
        if ($area !== RendererMiddleware::AREA_DEFAULT) {
            $name['area'] = ucfirst($area);
        }
        $name['action'] = ucfirst($this->currentHelper->currentAction());

        //unset($name['resource']);

        return implode('\\', $name) . 'Action';
    }

    protected function getNamespace($mnemo)
    {
        $namespace = null;
        if ($this->moduleHelper && ($module = $this->moduleHelper->getBy($mnemo, 'mnemo'))) {
            $namespace = $module->getName();
        } elseif (isset($this->config['middleware'][$mnemo])) {
            $namespace = $this->config['middleware'][$mnemo];
        } else {
            throw new RuntimeException(sprintf(
                'Module for "%s" in not registered in configuration or database',
                $mnemo
            ));
        }

        return $namespace;
    }

    protected function configureCurrentPlugin(ServerRequestInterface $request)
    {
        $filter = new DashToCamelCase();


        $route = $request->getAttribute(RouteResult::class);

        $this->currentHelper->setResource(
            lcfirst($filter->filter($request->getAttribute('resource', ConnectivePage::DEFAULT_RESOURCE)))
        );
        $this->currentHelper->setAction($request->getAttribute('action', ConnectivePage::DEFAULT_ACTION));
        $this->currentHelper->setRequest($request);
        $this->currentHelper->setRoute($route->getMatchedRoute());
        $this->currentHelper->setRouteName($route->getMatchedRouteName());
        $this->currentHelper->setRouteParams($route->getMatchedParams());


        $actionClass = $this->getActionClass($request);
        $this->currentHelper->setDefaultContext($actionClass);
    }
}