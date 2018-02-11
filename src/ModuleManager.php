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

class ModuleManager
{
    protected $pattern = "/\s+([^\/\/]?[\\\\]?[\\\\\w]+)::class/";

    protected $configPath = 'config/config.php';

    public function getLoadedModules()
    {
        $content = file_get_contents($this->configPath);

        preg_match_all($this->pattern, $content, $matches);

        array_shift($matches);
        $modules = [];
        foreach ($matches[0] as $class) {
            $parts = explode('\\', trim($class, '\\'));
            array_pop($parts); // remove ConfigProvider part
            $name = implode('\\', $parts);
            $modules[$name] = $name;
        }

        return $modules;
    }
}