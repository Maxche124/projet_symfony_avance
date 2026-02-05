<?php

namespace App\Exceptions;

use Exception;

class InvalidAmountFormat extends Exception {

    private int $amount;

    public function __construct(int $amount, string $message = "La valeur fournie est invalide") {
        parent::__construct($message);
        $this->amount = $amount;
        $this->message = $message;
    }

    public function getAmount(): int {
        return $this->amount;
    }
}
