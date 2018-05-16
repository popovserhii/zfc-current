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
        'aliases' => [
            'current' => Plugin\CurrentPlugin::class,
        ],
		'factories' => [
            Plugin\CurrentPlugin::class => Plugin\Factory\CurrentPluginFactory::class,
		]
	],

	'view_helpers' => [
		'aliases' => [
			'current' => View\CurrentHelper::class,
		],
	],
];