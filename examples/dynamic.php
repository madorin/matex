<?php

/**
 * Matex dynamic binding example
 * Evaluation of a formula with functions
 */

require dirname(__DIR__).'\vendor\autoload.php';
require __DIR__.'\math.php';

use \Matex\Evaluator;

class Some {

/**
 * Some function samples
 */

static function field($name) {
	switch ($name) {
		case 'width':
			return 10;
		case 'height':
			return 20;
	}
}

function mix($a, $b) {
	return ($a + $b) / 2;
}

/**
 * Variable resolver
 * Invoked when variable is not found in the cache
 */

public function doVariable($name, &$value) {
	switch ($name) {
		case 'zen':
			$value = 99;
			break;
		case 'fox':
			$value = 66;
			break;
	}
}

/**
 * Dynamic function resolver
 * Invoked when the function is not found in the cache
 * Returns an associative array array with:
 *   ref: function reference
 *   arc: expected argument count
 */

public function doFunction($name, &$value) {
	switch ($name) {
		case 'sin': // Map to a system function
			$value = ['ref' => 'sin', 'arc' => 1];
			break;
		case 'mix':
			// Map to a public object instance function
			$value = ['ref' => [$this, 'mix'], 'arc' => 2];
			break;
	}
}

public function calculate() {
	$evaluator = new Evaluator();
	// Some predefined variables
	$evaluator->variables = [
		'dx' => 1.2,
		'dy' => 3.4
	];
	// Connect variable resolver
	$evaluator->onVariable = [$this, 'doVariable'];
	// Some predefined functions
	$evaluator->functions = Math::functions + [
		'field' => ['ref' => 'Some::field', 'arc' => 1]
	];
	// Connect function resolver
	$evaluator->onFunction = [$this, 'doFunction'];
	// Let's do some calculations
	$formula = 'round(field("height") * field("width") / (dx + dy), 2) - min(zen / mix(dx, sin(1.7)), 3, fox)';
	return $evaluator->execute($formula);
}

}

$some = new Some();
echo $some->calculate();
