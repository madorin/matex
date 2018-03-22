# Matex
PHP Mathematical expression parser and evaluator

## Features
* Fast evaluation
* Compact codebase
* Operators: + - * / ^
* Brackets, nested, unlimited levels
* Variables: predefined or estimated dynamically
* Functions: predefined or connected dynamically
* String arguments to functions

## Examples

Basic:
```php
$parser = new \Matex\Parser();
echo $parser->execute('1 + 2');
```

Variables:
```php
$parser = new \Matex\Parser();
$parser->variables = [
	'a' => 1,
	'b' => 2
	];
echo $parser->execute('a + b');
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

$parser = new \Matex\Parser();
$parser->variables = [
	'a' => 1
	];
$parser->onVariable = [$this, 'doVariable'];
echo $parser->execute('a + b');
```

Functions:
```php
static function sum($arguments) {
	$result = 0;
	foreach ($arguments as $argument)
		$result += $argument;
	return $result;
}

$parser = new \Matex\Parser();
$parser->functions = [
	'sum' => ['ref' => '\\Space\\Class::sum', 'arc' => null]
];
echo $parser->execute('sum(1, 2, 3)');
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
$parser = new \Matex\Parser();
$parser->variables = [
	'a' => 1,
	'bet' => -10.59,
	'pi' => 3.141592653589
	];
$parser->onVariable = [$this, 'doVariable'];
$parser->functions = [
	'sin' => ['ref' => 'sin', 'arc' => 1],
	'max' => ['ref' => 'max', 'arc' => null],
	'sum' => ['ref' => '\\Space\\Class::sum', 'arc' => null]
	];
$parser->onFunction = [$this, 'doFunction'];
echo $parser->execute('a + MinAdd(PI * sin(zen), cos(-1.7 / pi)) / bet ^ ((A + 2) * 2) + sum(5, 4, max(6, hit))');
```

## License

Matex is distributed under MIT license. See [LICENSE.md](LICENSE.md) for more details.
