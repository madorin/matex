<?php

/**
 * Matex functions example
 * Evaluation of a formula with functions
 */

require dirname(__DIR__).'\vendor\autoload.php';

// Connect math module
require __DIR__.'\math.php';

use \Matex\Evaluator;

$evaluator = new Evaluator();
$evaluator->functions = Math::functions;
$result = $evaluator->execute('sum(1, 2, 3) + round(4 / 6, 2)');

echo $result;
