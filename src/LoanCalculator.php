<?php
declare(strict_types=1);

namespace App;

final class LoanCalculator
{
    public function calculate(float $principal, int $termMonths, float $apr, string $product): array
    {
        $loan = match ($product) {
            'annuity' => new AnnuityLoan(),
            'linear' => new LinearLoan(),
            default => throw new \InvalidArgumentException('Unknown product'),
        };

        return $loan->calculate($principal, $termMonths, $apr);
    }
}
