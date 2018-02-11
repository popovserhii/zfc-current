<?php
namespace Popov\ZfcCurrent;


return [
    'dependencies' => [
        'invokables' => [
            ModuleManager::class => ModuleManager::class, // analog MVC ModuleManager for Expressive
        ],
        'factories' => [
            CurrentHelper::class => Factory\CurrentHelperFactory::class
        ],
    ],

	'controller_plugins' => [
		'factories' => [
			'current' => Plugin\Factory\CurrentFactory::class,
		]
	],

	'view_helpers' => [
		'aliases' => [
			'current' => View\CurrentHelper::class,
		],
	],
];