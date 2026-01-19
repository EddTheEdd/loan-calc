<?php
declare(strict_types=1);

namespace App;

final class LinearLoan implements LoanProductInterface
{
    public function calculate(float $principal, int $termMonths, float $apr): array
    {
        $mpr = $apr / 100 / 12;
    
        $schedule = $this->buildSchedule($principal, $termMonths, $mpr);
    
        $totalInterest = 0.0;
        $totalPayment = 0.0;
    
        foreach ($schedule as $row) {
            $totalInterest += $row['interest'];
            $totalPayment  += $row['payment'];
        }
    
        return [
            'payment' => null,
            'totalRepayment' => round($totalPayment, 2),
            'totalInterest' => round($totalInterest, 2),
            'schedule' => $schedule,
        ];
    }
    
    private function buildSchedule(float $principal, int $termMonths, float $mpr): array
    {
        $balance = $principal;
        $basePrincipalPart = $principal / $termMonths;
    
        $schedule = [];
    
        foreach (range(1, $termMonths) as $month) {
            $interest = $balance * $mpr;
    
            if ($month === $termMonths) {
                $principalPart = $balance;
            } else {
                $principalPart = $basePrincipalPart;
            }
    
            $monthlyPayment = $principalPart + $interest;
            $balance -= $principalPart;
        
            $schedule[$month] = [
                'payment'   => round($monthlyPayment, 2),
                'interest'  => round($interest, 2),
                'principal' => round($principalPart, 2),
                'balance'   => round(max($balance, 0.0), 2),
            ];
        }
    
        return $schedule;
    }
    
}
