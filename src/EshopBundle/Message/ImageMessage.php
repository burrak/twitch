<?php

declare(strict_types = 1);

namespace App\EshopBundle\Message;

use Symfony\Component\Uid\Uuid;

class ImageMessage
{
    public function __construct(
        private Uuid $productId,
        private string $file
    )
    {
    }

    /**
     * @return Uuid
     */
    public function getProductId(): Uuid
    {
        return $this->productId;
    }

    /**
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }
}
