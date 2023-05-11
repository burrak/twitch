<?php

declare(strict_types = 1);

namespace App\WebhookBundle\DTO\Subscription;

use Spatie\DataTransferObject\DataTransferObject;

class Emote extends DataTransferObject
{
    public function __construct(private int $begin, private int $end, private string $id)
    {
    }

    /**
     * @return int
     */
    public function getBegin(): int
    {
        return $this->begin;
    }

    /**
     * @return int
     */
    public function getEnd(): int
    {
        return $this->end;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

}
