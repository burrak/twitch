<?php

declare(strict_types = 1);

namespace App\TwitchApiBundle\DTO\Response\EventSub;

use Spatie\DataTransferObject\Attributes\Strict;
use Spatie\DataTransferObject\DataTransferObject;

#[Strict]
class Transport extends DataTransferObject
{
    public function __construct(
        private string $method,
        private string $callback
    )
    {
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getCallback(): string
    {
        return $this->callback;
    }

}
