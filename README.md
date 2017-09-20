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

Simple:
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
  'sum' => ['ref' => '\\Class\\Space::Sum', 'arc' => null]
];
echo $Parser->Execute('sum(1, 2, 3)');
```
