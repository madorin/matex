# Matex
PHP Mathematical formula parser and evaluator

## Features
* Fast
* Compact
* Operators: + - * / ^
* Brackets
* Variables
* Functions

## Examples

Basic:
```php
$Parser = new \Matex\Parser();
echo $Parser->Execute('1 + 2');
```

Variables:
```php
$Parser = new \Matex\Parser();
$Parser->Variables = [
	'a' => 1,
	'b' => 2
	];
echo $Parser->Execute('a + b');
```

Dynamic variables:
```php
public function DoVariable($Name, &$Value) {
	switch ($Name) {
		case 'b':
			$Value = 2;
			break;
	}
}

$Parser = new \Matex\Parser();
$Parser->Variables = [
	'a' => 1
	];
$Parser->OnVariable = [$this, 'DoVariable'];
echo $Parser->Execute('a + b');
```

Functions:
```php
static function Sum($Arguments) {
	$Result = 0;
	foreach ($Arguments as $Argument)
		$Result += $Argument;
	return $Result;
}

$Parser = new \Matex\Parser();
$Parser->Functions = [
	'sum' => ['ref' => '\\Space\\Class::Sum', 'arc' => null]
];
echo $Parser->Execute('sum(1, 2, 3)');
```

Extravaganza:
```php
/*
Dynamic variable resolver
Invoked when the variable is not found in the cache
Returns the value by name
*/
public function DoVariable($Name, &$Value) {
	switch ($Name) {
		case 'zen':
			// Here may be a database request, or a function call
			$Value = 999;
			break;
		case 'hit':
			$Value = 666;
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
public function DoFunction($Name, &$Value) {
	switch ($Name) {
		case 'cos':
			// Map to a system function
			$Value = ['ref' => 'cos', 'arc' => 1];
			break;
		case 'minadd':
			// Map to a public object instance function
			$Value = ['ref' => [$this, 'MinAdd'], 'arc' => 2];
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
static function Sum($Arguments) {
	$Result = 0;
	foreach ($Arguments as $Argument)
		$Result += $Argument;
	return $Result;
}
// Just a sample custom function
function MinAdd($A, $B) {
	$R = $A < 2 ? 2 : $A;
	return $R + $B;
}

// Let's do some calculations
$Parser = new \Matex\Parser();
$Parser->Variables = [
	'a' => 1,
	'bet' => -10.59,
	'pi' => 3.141592653589
	];
$Parser->OnVariable = [$this, 'DoVariable'];
$Parser->Functions = [
	'sin' => ['ref' => 'sin', 'arc' => 1],
	'max' => ['ref' => 'max', 'arc' => null],
	'sum' => ['ref' => '\\Space\\Class::Sum', 'arc' => null]
	];
$Parser->OnFunction = [$this, 'DoFunction'];
echo $Parser->Execute('a + MinAdd(PI * sin(zen), cos(-1.7 / pi)) / bet ^ ((A + 2) * 2) + sum(5, 4, max(6, hit))');
```