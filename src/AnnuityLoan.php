<?php
declare(strict_types=1);

namespace App;

final class AnnuityLoan implements LoanProductInterface
{
    public function calculate(float $principal, int $termMonths, float $apr): array
    {
        // Transform APR to monthly percentage rate
        $mpr = $apr / 100 / 12;

        // Calculate periodic payment using annuity formula
        if ($mpr == 0.0) {
            $periodicPayment = $principal / $termMonths;
        } else {
            $numerator = $mpr * pow(1 + $mpr, $termMonths);
            $denominator = pow(1 + $mpr, $termMonths) - 1;
            $periodicPayment = $principal * ($numerator / $denominator);
        }

        $totalPayment = $periodicPayment * $termMonths;
        $totalInterest = $totalPayment - $principal;
        
        $schedule = [];
        $schedule = $this->buildSchedule($totalPayment, $principal, $termMonths, $mpr, $periodicPayment);
    
        $result = [
            'payment' => $periodicPayment,
            'totalRepayment' => $totalPayment,
            'totalInterest' => $totalInterest,
            'schedule' => $schedule,
        ];

        foreach ($result as $key => $value) {
            if (is_float($value)) {
                $result[$key] = round($value, 2);
            }
        }

        return $result;
    }

    private function buildSchedule(float $totalPayment, float $principal, int $termMonths, float $mpr, float $periodicPayment): array
    {
        $balance = $principal;
        foreach (range(1, $termMonths) as $month) {
            $interest = $balance * $mpr;
        
            // Handle last month payment differently because of rounding.
            if ($month === $termMonths) {
                $principalPart = $balance;
                $monthlyPayment = $totalPayment;
                $balance = 0.0;
            } else {
                $principalPart = $periodicPayment - $interest;
                $monthlyPayment = $periodicPayment;
                $balance -= $principalPart;
                $totalPayment -= round($monthlyPayment, 2);
            }
        
            $schedule[$month] = [
                'payment'   => round($monthlyPayment, 2),
                'interest'  => round($interest, 2),
                'principal' => round($principalPart, 2),
                'balance'   => round($balance, 2),
            ];
        }
        return $schedule;
    }
}
