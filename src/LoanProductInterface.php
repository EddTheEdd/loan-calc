<?php
declare(strict_types=1);

namespace App;

interface LoanProductInterface
{
    /**
     * @return array{
     *   payment: float,
     *   totalRepayment: float,
     *   totalInterest: float,
     *   schedule?: array<int, array{period:int,payment:float,interest:float,principal:float,balance:float}>
     * }
     */
    public function calculate(float $principal, int $termMonths, float $apr): array;
}
