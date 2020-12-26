# Matex

PHP Mathematical expression parser and evaluator

## Installation

Install the latest version with

```bash
$ composer require monolog/monolog
```

## Features

* Fast evaluation
* Compact codebase
* Operators: + - * / ^
* Brackets, nested, unlimited levels
* Variables: predefined or estimated dynamically
* Functions: predefined or connected dynamically
* String arguments in functions, like field("name")
* String operations, currently concatenation is supported

## Examples

Basic:
```php
$evaluator = new \Matex\Evaluator();
echo $evaluator->execute('1 + 2');
```

String concatenation:
```php
$evaluator = new \Matex\Evaluator();
echo $evaluator->execute('"String" + " " + "concatenation"');
```

Variables:
```php
$evaluator = new \Matex\Evaluator();
$evaluator->variables = [
	'a' => 1,
	'b' => 2
	];
echo $evaluator->execute('a + b');
```

Dynamic variables:
```php
public function doVariable($name, &$value) {
	switch ($name) {
		case 'b':
			$value = 2;
			break;
	}
}

$evaluator = new \Matex\Evaluator();
$evaluator->variables = [
	'a' => 1
	];
$evaluator->onVariable = [$this, 'doVariable'];
echo $evaluator->execute('a + b');
```

Functions:
```php
static function sum($arguments) {
	$result = 0;
	foreach ($arguments as $argument)
		$result += $argument;
	return $result;
}

$evaluator = new \Matex\Evaluator();
$evaluator->functions = [
	'sum' => ['ref' => '\\Space\\Class::sum', 'arc' => null]
];
echo $evaluator->execute('sum(1, 2, 3)');
```

Extravaganza:
```php
/*
Dynamic variable resolver
Invoked when the variable is not found in the cache
Returns the value by name
*/
public function doVariable($name, &$value) {
	switch ($name) {
		case 'zen':
			// Here may be a database request, or a function call
			$value = 999;
			break;
		case 'hit':
			$value = 666;
			break;
	}
}

/*
Dynamic function resolver
Invoked when the function is not found in the cache
Returns an associative array array with:
	ref - Function reference
	arc - Expected argument count
*/
public function doFunction($name, &$value) {
	switch ($name) {
		case 'cos':
			// Map to a system function
			$value = ['ref' => 'cos', 'arc' => 1];
			break;
		case 'minadd':
			// Map to a public object instance function
			$value = ['ref' => [$this, 'minAdd'], 'arc' => 2];
			break;
	}
}

/*
Custom functions, may be a
	- Built-in function
	- Global defined function
	- Static class function
	- Object instance function
*/
static function sum($arguments) {
	$result = 0;
	foreach ($arguments as $argument)
		$result += $argument;
	return $result;
}
// Just a sample custom function
function minAdd($a, $b) {
	$r = $a < 2 ? 2 : $a;
	return $r + $b;
}

// Let's do some calculations
$evaluator = new \Matex\Evaluator();
$evaluator->variables = [
	'a' => 1,
	'bet' => -10.59,
	'pi' => 3.141592653589
	];
$evaluator->onVariable = [$this, 'doVariable'];
$evaluator->functions = [
	'sin' => ['ref' => 'sin', 'arc' => 1],
	'max' => ['ref' => 'max', 'arc' => null],
	'sum' => ['ref' => '\\Space\\Class::sum', 'arc' => null]
	];
$evaluator->onFunction = [$this, 'doFunction'];
echo $evaluator->execute('a + MinAdd(PI * sin(zen), cos(-1.7 / pi)) / bet ^ ((A + 2) * 2) + sum(5, 4, max(6, hit))');
```

## Author

Dorin Marcoci - <dorin.marcoci@gmail.com> - <https://www.marcodor.com>

## License

Matex is distributed under MIT license. See [LICENSE.md](LICENSE.md) for more details.
