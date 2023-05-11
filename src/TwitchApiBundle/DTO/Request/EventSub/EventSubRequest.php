<?php

declare(strict_types = 1);

namespace App\TwitchApiBundle\DTO\Request\EventSub;

use Spatie\DataTransferObject\Attributes\Strict;
use Spatie\DataTransferObject\DataTransferObject;

#[Strict]
class EventSubRequest extends DataTransferObject
{
    private string $type;
    private string $version;
    private Condition $condition;
    private Transport $transport;

    public function __construct(
        string $type,
        string $version,
        string $broadcasterUserId,
        string $method,
        string $callback,
        string $secret
    )
    {
        $this->type = $type;
        $this->version = $version;
        $this->condition = new Condition($broadcasterUserId);
        $this->transport = new Transport($method, $callback, $secret);
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
     * @return Transport
     */
    public function getTransport(): Transport
    {
        return $this->transport;
    }
}
