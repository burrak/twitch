<?php

declare(strict_types = 1);

namespace App\EshopBundle\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class AddToCart extends DataTransferObject
{
    public function __construct(
        private int $quantity,
        private string $productId
    )
    {
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return string
     */
    public function getProductId(): string
    {
        return $this->productId;
    }
}
