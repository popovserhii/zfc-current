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

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Expressive\Router\RouteResult;

class CurrentMiddleware implements MiddlewareInterface
{
    /**
     * @var CurrentHelper
     */
    protected $currentHelper;

    public function __construct(CurrentHelper $currentHelper)
    {
        $this->currentHelper = $currentHelper;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = $request->getAttribute(RouteResult::class);
        #$this->currentHelper->setDefaultContext($actionClass);
        #$this->currentHelper->setResource($request->getAttribute('resource', self::DEFAULT_RESOURCE));
        #$this->currentHelper->setAction($request->getAttribute('action', self::DEFAULT_ACTION));
        $this->currentHelper->setRequest($request);
        $this->currentHelper->setRoute($route->getMatchedRoute());
        $this->currentHelper->setRouteName($route->getMatchedRouteName());
        $this->currentHelper->setRouteParams($route->getMatchedParams());

        return $handler->handle($request);
    }
}