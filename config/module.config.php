<?php
namespace Popov\ZfcCurrent;

return [
	'controller_plugins' => [
		'factories' => [
			'current' => Plugin\Factory\CurrentFactory::class,
		]
	],

	'view_helpers' => [
		'invokables' => [
			'current' => Helper\CurrentPlugin::class,
		],
	],
];