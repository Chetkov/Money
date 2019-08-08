<?php

use Chetkov\Money\Exception\RequiredParameterMissedException;
use Tests\Chetkov\Money\LibConfigurator;

require_once CHETKOV_MONEY_ROOT . '/vendor/autoload.php';

try {
    LibConfigurator::configureForTests();
} catch (RequiredParameterMissedException $e) {
    echo json_encode([
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString(),
    ]);
}