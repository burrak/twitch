<?php

declare(strict_types = 1);

namespace App\EshopBundle\DTO;

use App\Entity\Currency;
use Spatie\DataTransferObject\DataTransferObject;

class EshopConfig extends DataTransferObject
{
    public function __construct(
        private Currency $currency,
        private float $deliveryPrice
    )
    {
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getDeliveryPrice(): float
    {
        return $this->deliveryPrice;
    }
}
