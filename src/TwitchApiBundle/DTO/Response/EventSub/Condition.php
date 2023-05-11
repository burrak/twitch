<?php

declare(strict_types = 1);

namespace App\TwitchApiBundle\DTO\Response\EventSub;

use Spatie\DataTransferObject\Attributes\Strict;
use Spatie\DataTransferObject\DataTransferObject;

#[Strict]
class Condition extends DataTransferObject
{
    /**
     * @param string $broadcasterUserId
     */
    public function __construct(private string $broadcasterUserId)
    {
    }

    /**
     * @return string
     */
    public function getBroadcasterUserId(): string
    {
        return $this->broadcasterUserId;
    }

}
