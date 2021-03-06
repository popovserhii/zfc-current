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

namespace Popov\ZfcCurrent\Plugin\Factory;

use Psr\Container\ContainerInterface;
use Popov\ZfcCurrent\CurrentHelper;
use Popov\ZfcCurrent\Plugin\CurrentPlugin;

class CurrentPluginFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $currentHelper = $container->get(CurrentHelper::class);

        return new CurrentPlugin($currentHelper);
    }
}