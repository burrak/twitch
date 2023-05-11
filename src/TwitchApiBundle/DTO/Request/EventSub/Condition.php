<?php

declare(strict_types = 1);

namespace App\TwitchApiBundle\DTO\Request\EventSub;

use Spatie\DataTransferObject\Attributes\Strict;
use Spatie\DataTransferObject\DataTransferObject;

#[Strict]
class Condition extends DataTransferObject
{

    public function __construct(
        private string $broadcasterUserId
    )
    {
    }

    public function getBroadcasterUserId(): string
    {
        return $this->broadcasterUserId;
    }
}
