<?php
declare(strict_types=1);

header('Content-Type: application/json');

require __DIR__ . '/../vendor/autoload.php';

use App\LoanCalculator;
use App\Validation;
use App\Exceptions\ValidationException;

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        exit;
    }

    $raw = file_get_contents('php://input');
    $payload = json_decode($raw ?: '', true);

    if (!is_array($payload)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON body']);
        exit;
    }

    $principal   = (float)($payload['principal'] ?? 0);
    $termMonths  = (int)($payload['termMonths'] ?? 0);
    $apr         = (float)($payload['apr'] ?? 0);
    $product     = (string)($payload['product'] ?? '');

    Validation::validate($principal, $termMonths, $apr, $product);

    $calculator = new LoanCalculator();
    $result = $calculator->calculate($principal, $termMonths, $apr, $product);

    echo json_encode($result);
} catch (ValidationException $e) {
    http_response_code(422);
    echo json_encode([
        'errors' => $e->getErrors()
    ]);
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
