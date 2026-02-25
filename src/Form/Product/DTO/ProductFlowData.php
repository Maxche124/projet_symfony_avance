<?php

namespace App\Form\Product\DTO;

class ProductFlowData
{
    public ?string $type = null; // physical | digital

    public ?string $name = null;
    public ?string $description = null;
    public ?string $price = null;

    // Physical only
    public ?float $weight = null;
    public ?int $stock = null;

    // Digital only
    public ?string $licenseKey = null;

    public bool $confirmHighPrice = false;
}