<?php

namespace Matex;

class Exception extends \Exception {}

class Parser {

private $Pos;
private $Text;

public $Variables = [];
public $OnVariable;
public $Functions = []; // ARCTAN COS SIN TAN ABS EXP LN LOG SQRT SQR INT FRAC TRUNC ROUND ARCSIN ARCCOS SIGN NOT
public $OnFunction;

private function GetNumber(float &$Value = null): bool {
	if (!ctype_digit($Char = $this->Text[$this->Pos] ?? false))
		return false;
	$OldPos = $this->Pos;
	$WasDec = false;
	do {
		if ($Char == '.') {
			if ($WasDec) {
				$Result = false;
				break;
			}
			$WasDec = true;
		}
		$this->Pos++;
	} while (ctype_digit($Char = $this->Text[$this->Pos] ?? false) || ($Char == '.'));
	if (isset($Result) || !in_array($Char, [false, '+', '-', '/', '*', '^', ')'])) {
		$this->Pos = $OldPos;
		return false;
	}
	$Value = (float) substr($this->Text, $OldPos, $this->Pos - $OldPos);
	return true;
}

private function GetIdentity(bool &$Kind = null, string &$Name = null): bool {
	if (!(ctype_alpha($Char = $this->Text[$this->Pos] ?? false) || ($Char == '_')))
		return false;
	$OldPos = $this->Pos;
	do {
		$this->Pos++;
	} while (ctype_alnum($Char = $this->Text[$this->Pos] ?? false) || ($Char == '_'));
	if (!in_array($Char, [false, '+', '-', '/', '*', '^', '(', ')'])) {
		$this->Pos = $OldPos;
		return false;
	}
	$Kind = ($Char != '(');
	$Name = substr($this->Text, $OldPos, $this->Pos - $OldPos);
	return true;
}

private function ProVariable(string $Name, float &$Value = null): void {
	if (is_numeric($Value = $this->Variables[$Name] ?? false))
		return;
	if (isset($this->OnVariable))
		call_user_func_array($this->OnVariable, [$Name, &$Value]);
	if (!is_numeric($Value))
		throw new Exception('Unknown variable: '.$Name, 5);
	$this->Variables[$Name] = $Value;
}

private function AddArgument(&$Arguments, $Argument) {
	if ($Argument == '')
		throw new Exception('Empty argument', 4);
	$Arguments[] = $Argument;
}

private function GetArguments(&$Arguments = []): bool {
	$B = 1;
	$this->Pos++;
	$Mark = $this->Pos;
	while ((($Char = $this->Text[$this->Pos] ?? false) !== false) && ($B > 0)) {
		if (($Char == ',') && ($B == 1)) {
			$this->AddArgument($Arguments, substr($this->Text, $Mark, $this->Pos - $Mark));
			$Mark = $this->Pos + 1;
		}
		elseif ($Char == ')') $B--;
		elseif ($Char == '(') $B++;
		$this->Pos++;
	}
	if (!in_array($Char, [false, '+', '-', '/', '*', '^', ')']))
		return false;
	$this->AddArgument($Arguments, substr($this->Text, $Mark, $this->Pos - $Mark - 1));
	return true;
}

private function ProArgument($Arguments) {
	$OPos = $this->Pos;
	$OText = $this->Text;
	$Result = [];
	foreach ($Arguments as $Argument)
		$Result[] = $this->Perform($Argument);
	$this->Pos = $OPos;
	$this->Text = $OText;
	return $Result;
}

private function ProFunction(string $Name, float &$Value = null): void {
	if (!isset($this->Functions[$Name])) {
		if (isset($this->OnFunction))
			call_user_func_array($this->OnFunction, [$Name, &$Function]);
		if (!isset($Function))
			throw new Exception('Unknown function: '.$Name, 6);
		$this->Functions[$Name] = $Function;
	} else
		$Function = $this->Functions[$Name];
	if (!$this->GetArguments($Arguments))
		throw new Exception('Syntax error', 1);
	if (isset($Function['arc']) && ($Function['arc'] != count($Arguments)))
		throw new Exception('Invalid argument count', 3);
	$Value = call_user_func($Function['ref'], $this->ProArgument($Arguments));
}

private function Term(): float {
	if ($this->Text[$this->Pos] == '(') {
		$this->Pos++;
		$Value = $this->Calculate();
		$this->Pos++;
		if (!in_array($this->Text[$this->Pos] ?? false, [false, '+', '-', '/', '*', '^', ')']))
			throw new Exception('Syntax error', 1);
		return $Value;
	}
	if (!$this->GetNumber($Value))
		if ($this->GetIdentity($Kind, $Name)) {
			if ($Kind)
				$this->ProVariable($Name, $Value);
			else
				$this->ProFunction($Name, $Value);
		}
		else
			throw new Exception('Syntax error', 1);
	return $Value;
}

private function SubTerm(): float {
	$Value = $this->Term();
	while (in_array($Char = $this->Text[$this->Pos] ?? false, ['*', '^', '/'])) {
		$this->Pos++;
		$Term = $this->Term();
		switch ($Char) {
			case '*':
				$Value = $Value * $Term;
				break;
			case '/':
				if ($Term == 0)
					throw new Exception('Division by zero', 7);
				$Value = $Value / $Term;
				break;
			case '^':
				$Value = pow($Value, $Term);
				break;
		}
	}
	return $Value;
}

private function Calculate(): float {
	$Value = $this->SubTerm();
	while (in_array($Char = $this->Text[$this->Pos] ?? false, ['+', '-'])) {
		$this->Pos++;
		$SubTerm = $this->SubTerm();
		if ($Char == '-')
			$SubTerm = -$SubTerm;
		$Value += $SubTerm;
	}
	return $Value;
}

private function Perform(string $Formula): float {
	$this->Pos = 0;
	if (in_array($Formula[0], ['-', '+']))
		$Formula = '0'.$Formula;
	$this->Text = $Formula;
	return $this->Calculate();
}

public function Execute(string $Formula): float {
	$B = 0;
	for ($I = 0; $I < strlen($Formula); $I++) {
		switch ($Formula[$I]) {
			case '(': $B++; break;
			case ')': $B--; break;
		}
	}
	if ($B != 0)
		throw new Exception('Unmatched brackets', 2);
	return $this->Perform(str_replace(' ', '', strtolower($Formula)));
}

}
