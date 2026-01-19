<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\AnnuityLoan;
use App\Exceptions\ValidationException;
use App\Validation;

final class AnnuityLoanTest extends TestCase
{
  public function test_calculate_shape(): void
  {
    $loan = new AnnuityLoan();

    $result = $loan->calculate(
      principal: 100000,
      termMonths: 3,
      apr: 5
    );

    // Assert structure (adapt to your real return format)
    $this->assertIsArray($result);

    // Result table:
    $this->assertArrayHasKey('payment', $result);
    $this->assertArrayHasKey('totalInterest', $result);
    $this->assertArrayHasKey('totalRepayment', $result);

    // Test schedule:
    $this->assertArrayHasKey('schedule', $result);
    $this->assertIsArray($result['schedule']);

    $assertPeriod = 1;
    foreach ($result['schedule'] as $key => $row) {
      $this->assertSame($assertPeriod, $key);
      $assertPeriod++;

      $this->assertArrayHasKey('payment', $row);
      $this->assertArrayHasKey('interest', $row);
      $this->assertArrayHasKey('principal', $row);
      $this->assertArrayHasKey('balance', $row);
    }
  }

  public function test_calculate_logic_case_1(): void
  {
    $loan = new AnnuityLoan();

    $result = $loan->calculate(100000, 360, 5);

    $this->assertEquals(536.82, $result['payment']);
  }

  public function test_calculate_logic_case_2(): void
  {
    $loan = new AnnuityLoan();

    $result = $loan->calculate(12000, 24, 0);

    $this->assertEquals(500.00, $result['payment']);
  }

  public function test_validation_unknown_product(): void
  {
    try {
      Validation::validate(100000, 360, 5, 'unknown_product');
      $this->fail('Expected ValidationException was not thrown');
    } catch (ValidationException $e) {
      $this->assertSame(
        [
          'product' => 'Invalid loan product type.',
        ],
        $e->getErrors()
      );
    }
  }

  public function test_validation_multiple_errors_case_1(): void
  {
    try {
      Validation::validate(100000, 360, -1, 'unknown_product');
      $this->fail('Expected ValidationException was not thrown');
    } catch (ValidationException $e) {
      $this->assertSame(
        [
          'apr' => 'Interest rate must be greater than or equal to 0.',
          'product' => 'Invalid loan product type.',
        ],
        $e->getErrors()
      );
    }
  }

  public function test_validation_multiple_errors_case_2(): void
  {
    try {
      Validation::validate(0, 0, 5, 'annuity');
      $this->fail('Expected ValidationException was not thrown');
    } catch (ValidationException $e) {
      $this->assertSame(
        [
          'principal' => 'Principal must be greater than 0.',
          'termMonths' => 'Loan term must be greater than 0 months.',
        ],
        $e->getErrors()
      );
    }
  }
}
