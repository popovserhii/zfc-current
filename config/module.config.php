<?php
namespace Popov\ZfcCurrent;

return [
	'controller_plugins' => [
		'factories' => [
			'current' => Plugin\Factory\ZfcCurrentFactory::class,
		]
	],

	'view_helpers' => [
		'invokables' => [
			'current' => Helper\ZfcCurrent::class,
		],
	],
];