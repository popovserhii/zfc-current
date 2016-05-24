<?php
namespace Agere\Current;

return [
	'controller_plugins' => [
		'factories' => [
			'current' => Plugin\Factory\CurrentFactory::class,
		]
	],

	'view_helpers' => [
		'invokables' => [
			'current' => Helper\Current::class,
		],
	],
];