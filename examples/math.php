<?php

/**
 * Math class
 * Math related functions
 */

class Math {

public const functions = [
	'sum' => ['ref' =>'Math::sum', 'arc' => null],
	'min' => ['ref' => 'min', 'arc' => null],
	'max' => ['ref' => 'max', 'arc' => null],
	'round' => ['ref' => 'round', 'arc' => 2]
];

static function sum(...$arguments) {
	return array_sum($arguments);
}

}
