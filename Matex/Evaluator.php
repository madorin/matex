<?php

namespace Matex;

class Exception extends \Exception {}

class Evaluator {

private $pos;
private $text;

public $variables = [];
public $onVariable;
public $functions = []; // ARCTAN COS SIN TAN ABS EXP LN LOG SQRT SQR INT FRAC TRUNC ROUND ARCSIN ARCCOS SIGN NOT
public $onFunction;

private function getIdentity(bool &$kind = null, string &$value = null): bool {
	$ops = $this->pos;
	$ist = ($this->text[$this->pos] ?? false) == '\'';
	if ($ist) $this->pos++;
	while (($char = $this->text[$this->pos] ?? false) && (($ist && ($char != '\'')) || ctype_alnum($char) || in_array($char, ['_', '.'])))
		$this->pos++;
	if (!$len = $this->pos - $ops) return false;
	$str = substr($this->text, $ops, $len);
	if ($ist) {
		if ($char != '\'') return false;
		$this->pos++;
		$value = substr($str, 1);
		$kind = 4;
		return true;
	}
	if (is_numeric($str)) $kind = 1;
	else {
		if (ctype_digit($str[0]) || (strpos($str, '.') !== false)) return false;
		$kind = $char == '(' ? 3 : 2;
	}
	$value = $str;
	return true;
}

private function getVariable(string $name): float {
	$value = $this->variables[$name] ?? null;
	if (!isset($value) && isset($this->onVariable)) {
		call_user_func_array($this->onVariable, [$name, &$value]);
		$this->variables[$name] = $value;
	}
	if (!isset($value))
		throw new Exception('Unknown variable: '.$name, 5);
	return $value;
}

private function addArgument(&$arguments, $argument) {
	if ($argument == '')
		throw new Exception('Empty argument', 4);
	$arguments[] = $argument;
}

private function getArguments(&$arguments = []): bool {
	$b = 1;
	$this->pos++;
	$mark = $this->pos;
	while ((($char = $this->text[$this->pos] ?? false) !== false) && ($b > 0)) {
		if (($char == ',') && ($b == 1)) {
			$this->addArgument($arguments, substr($this->text, $mark, $this->pos - $mark));
			$mark = $this->pos + 1;
		}
		elseif ($char == ')') $b--;
		elseif ($char == '(') $b++;
		$this->pos++;
	}
	if (!in_array($char, [false, '+', '-', '/', '*', '^', ')']))
		return false;
	$this->addArgument($arguments, substr($this->text, $mark, $this->pos - $mark - 1));
	return true;
}

private function proArguments($arguments) {
	$ops = $this->pos;
	$otx = $this->text;
	$result = [];
	foreach ($arguments as $argument)
		$result[] = $this->perform($argument);
	$this->pos = $ops;
	$this->text = $otx;
	return $result;
}

private function getFunction(string $name): float {
	$routine = $this->functions[$name] ?? null;
	if (!isset($routine) && isset($this->onFunction)) {
		call_user_func_array($this->onFunction, [$name, &$routine]);
		$this->functions[$name] = $routine;
	}
	if (!isset($routine))
		throw new Exception('Unknown function: '.$name, 6);
	if (!$this->getArguments($arguments))
		throw new Exception('Syntax error', 1);
	if (isset($routine['arc']) && ($routine['arc'] != count($arguments)))
		throw new Exception('Invalid argument count', 3);
	return call_user_func_array($routine['ref'], $this->proArguments($arguments));
}

private function term() {
	if ($this->text[$this->pos] == '(') {
		$this->pos++;
		$value = $this->calculate();
		$this->pos++;
		if (!in_array($this->text[$this->pos] ?? false, [false, '+', '-', '/', '*', '^', ')']))
			throw new Exception('Syntax error', 1);
		return $value;
	}
	if (!$this->getIdentity($kind, $name))
		throw new Exception('Syntax error', 1);
	switch ($kind) {
		case 1: return (float) $name;
		case 2: return $this->getVariable($name);
		case 3: return $this->getFunction($name);
		case 4: return $name; // string
	}
}

private function subTerm() {
	$value = $this->term();
	while (in_array($char = $this->text[$this->pos] ?? false, ['*', '^', '/'])) {
		$this->pos++;
		$term = $this->term();
		switch ($char) {
			case '*':
				$value = $value * $term;
				break;
			case '/':
				if ($term == 0)
					throw new Exception('Division by zero', 7);
				$value = $value / $term;
				break;
			case '^':
				$value = pow($value, $term);
				break;
		}
	}
	return $value;
}

private function calculate() {
	$value = $this->subTerm();
	while (in_array($char = $this->text[$this->pos] ?? false, ['+', '-'])) {
		$this->pos++;
		$subTerm = $this->subTerm();
		if ($char == '-')
			$subTerm = -$subTerm;
		$value += $subTerm;
	}
	return $value;
}

private function perform(string $formula) {
	$this->pos = 0;
	if (in_array($formula[0], ['-', '+']))
		$formula = '0'.$formula;
	$this->text = $formula;
	return $this->calculate();
}

public function execute(string $formula): float {
	$b = 0;
	for ($i = 0; $i < strlen($formula); $i++) {
		switch ($formula[$i]) {
			case '(': $b++; break;
			case ')': $b--; break;
		}
	}
	if ($b != 0)
		throw new Exception('Unmatched brackets', 2);
	return $this->perform(str_replace(' ', '', strtolower($formula)));
}

}
