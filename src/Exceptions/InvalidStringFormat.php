<?php

namespace App\Exceptions;

use Exception;

class InvalidStringFormat extends Exception {
    private string $string;

    public function __construct(string $string, string $message = "String invalide") {
        parent::__construct($message);
        $this->string = $string;
    }

    public function getString(): string {
        return $this->string;
    }
}
