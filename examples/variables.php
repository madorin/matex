<?php

/**
 * Matex variables example
 * Evaluation of a formula with variables
 */

require dirname(__DIR__).'\vendor\autoload.php';

use \Matex\Evaluator;

$evaluator = new Evaluator();
$evaluator->variables = [
	'a' => 3,
	'b' => 2
];
$result = $evaluator->execute('1 + a - b');

echo $result;
