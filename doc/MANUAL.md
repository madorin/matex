
# Matex

Matex is a PHP Mathematical expression parser and evaluator library.It allows safe execution of the arbitrary expressions.


## Installation

Matex can be installed using Composer package manager or manually connected to your project.

### Composer

Make sure the Composer is installed and your project is properly configured to use Composer tool.
Execute in console the following code in order to connect Matex library:

```bash
$ composer require madorin/matex
```

It will adjust the composer.json file of your project by adding Matex library as an requrement.
Check if Composer's `/vendor/autoload.php` is included/required in your project.

### Manual

Download the Matex zip package and extract it in your project libraries folder.
If you use a custom php autoloader, the classes located in `/src/` folder are PSR-4 compatible, so may adjust the autoloader configuration and/or move the folder according to your rules.

For complete manual linking way, include the `src/Evaluator.php` in your project:

```php
<?php
	require 'path/to/matex/src/Evaluator.php';
```

### Testing

Once everything is properly configured, the `\Matex\` namespace classes should be available for usage.
The following code should run without any errors and will output `3` as result:

```php
$evaluator = new \Matex\Evaluator();
echo $evaluator->execute('1 + 2');
```


## Author

Dorin Marcoci - <dorin.marcoci@gmail.com> - <https://www.marcodor.com>


## License

Matex is distributed under MIT license. See [LICENSE.md](../LICENSE.md) for more details.