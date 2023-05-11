<?php

declare(strict_types = 1);

namespace App\WebhookBundle\DTO\Subscription;

use Spatie\DataTransferObject\Attributes\Strict;
use Spatie\DataTransferObject\DataTransferObject;

#[Strict]
class Event extends DataTransferObject
{
    public function __construct(
        private string $userId,
        private string $userLogin,
        private string $userName,
        private string $broadcasterUserId,
        private string $broadcasterUserLogin,
        private string $broadcasterUserName,
        private string $tier,
        private ?bool $isGift,
        private ?int $total,
        private ?int $cumulativeTotal,
        private ?bool $isAnonymous,
        private ?int $streakMonths,
        private ?int $cumulativeMonths,
        private ?int $durationMonths,
        private ?Message $message
    )
    {
    }

    /**
     * @return int|null
     */
    public function getCumulativeTotal(): ?int
    {
        return $this->cumulativeTotal;
    }

    /**
     * @return bool|null
     */
    public function getIsAnonymous(): ?bool
    {
        return $this->isAnonymous;
    }

    /**
     * @return int|null
     */
    public function getStreakMonths(): ?int
    {
        return $this->streakMonths;
    }

    /**
     * @return int|null
     */
    public function getCumulativeMonths(): ?int
    {
        return $this->cumulativeMonths;
    }

    /**
     * @return int|null
     */
    public function getDurationMonths(): ?int
    {
        return $this->durationMonths;
    }

    /**
     * @return Message|null
     */
    public function getMessage(): ?Message
    {
        return $this->message;
    }

    /**
     * @return int|null
     */
    public function getTotal(): ?int
    {
        return $this->total;
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getUserLogin(): string
    {
        return $this->userLogin;
    }

    /**
     * @return string
     */
    public function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * @return string
     */
    public function getBroadcasterUserId(): string
    {
        return $this->broadcasterUserId;
    }

    /**
     * @return string
     */
    public function getBroadcasterUserLogin(): string
    {
        return $this->broadcasterUserLogin;
    }

    /**
     * @return string
     */
    public function getBroadcasterUserName(): string
    {
        return $this->broadcasterUserName;
    }

    /**
     * @return string
     */
    public function getTier(): string
    {
        return $this->tier;
    }

    /**
     * @return bool|null
     */
    public function isGift(): ?bool
    {
        return $this->isGift;
    }
}
