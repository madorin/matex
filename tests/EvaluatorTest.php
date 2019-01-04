<?php
declare(strict_types=1);

require(__DIR__ . "/../Matex/Evaluator.php");

use PHPUnit\Framework\TestCase;
use Matex\Evaluator;

final class EvaluatorTest extends TestCase
{
    public function testFirstOrderOperators(): void
    {
        $evaluator = new \Matex\Evaluator();
        $this->assertEquals(
            0,
            $evaluator->execute("1 + 2 - 3")
        );
    }

    public function testBasicOperators(): void
    {
        $evaluator = new \Matex\Evaluator();
        $this->assertEquals(
            0,
            $evaluator->execute("1 + 2 - 3 * 2 / 2")
        );
    }

    public function testParenthesis(): void
    {
        $evaluator = new \Matex\Evaluator();
        $this->assertEquals(
            2,
            $evaluator->execute("6 / (1 + 2)")
        );
    }

    public function testNegativeNumbers(): void
    {
        $evaluator = new \Matex\Evaluator();
        $this->assertEquals(
            -1,
            $evaluator->execute("1 + -2")
        );
    }

    public function testVariables(): void
    {
        $evaluator = new \Matex\Evaluator();
        $evaluator->variables = ['x' => 1, 'y' => 2];
        $this->assertEquals(
            3,
            $evaluator->execute("x + y")
        );
    }
}
