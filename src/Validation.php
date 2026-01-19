<?php
declare(strict_types=1);

namespace App;

final class Validation
{
    public static function validate(
        float $principal,
        int $termMonths,
        float $apr,
        string $product
    ): void {
        $errors = [];

        if ($principal <= 0) {
            $errors['principal'] = 'Principal must be greater than 0.';
        }

        if ($termMonths <= 0) {
            $errors['termMonths'] = 'Loan term must be greater than 0 months.';
        }

        if ($apr < 0) {
            $errors['apr'] = 'Interest rate must be greater than or equal to 0.';
        }

        if (!in_array($product, ['annuity', 'linear'], true)) {
            $errors['product'] = 'Invalid loan product type.';
        }

        if ($errors) {
            throw new Exceptions\ValidationException($errors);
        }
    }
}
