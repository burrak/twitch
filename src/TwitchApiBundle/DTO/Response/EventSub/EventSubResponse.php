<?php

declare(strict_types = 1);

namespace App\TwitchApiBundle\DTO\Response\EventSub;

use Spatie\DataTransferObject\Attributes\Strict;
use Spatie\DataTransferObject\DataTransferObject;

#[Strict]
class EventSubResponse extends DataTransferObject
{
    public function __construct(
        private string $id,
        private string $status,
        private string $type,
        private string $version,
        private Condition $condition,
        private string $createdAt,
        private Transport $transport,
        private int $cost
    )
    {
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @return Condition
     */
    public function getCondition(): Condition
    {
        return $this->condition;
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    /**
     * @return Transport
     */
    public function getTransport(): Transport
    {
        return $this->transport;
    }

    /**
     * @return int
     */
    public function getCost(): int
    {
        return $this->cost;
    }

}
