<?php

declare(strict_types = 1);

namespace App\EshopBundle\DTO;

use Spatie\DataTransferObject\Attributes\Strict;
use Spatie\DataTransferObject\DataTransferObject;

#[Strict]
class NewProduct extends DataTransferObject
{
    public function __construct(
        private string $title,
        private float $price,
        private float $priceVat,
        private int $vat,
        private ?string $description,
        private bool $subscriber,
        private ?int $cumulativeMonths,
        private ?int $currentStreak,
        private ?int $maxStreak,
        private ?int $giftedTotal,
        private ?int $tier,
        private ?int $orderLimit,
        private ?int $totalLimit,
        private \DateTimeInterface|false|null $dateFrom,
        private \DateTimeInterface|false|null $dateTo,
        private bool $active
    )
    {
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @return float
     */
    public function getPriceVat(): float
    {
        return $this->priceVat;
    }

    /**
     * @return int
     */
    public function getVat(): int
    {
        return $this->vat;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return bool
     */
    public function isSubscriber(): bool
    {
        return $this->subscriber;
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
    public function getCurrentStreak(): ?int
    {
        return $this->currentStreak;
    }

    /**
     * @return int|null
     */
    public function getMaxStreak(): ?int
    {
        return $this->maxStreak;
    }

    /**
     * @return int|null
     */
    public function getGiftedTotal(): ?int
    {
        return $this->giftedTotal;
    }

    /**
     * @return int|null
     */
    public function getTier(): ?int
    {
        return $this->tier;
    }

    /**
     * @return int|null
     */
    public function getOrderLimit(): ?int
    {
        return $this->orderLimit;
    }

    /**
     * @return \DateTimeInterface|false|null
     */
    public function getDateFrom(): \DateTimeInterface|false|null
    {
        return $this->dateFrom;
    }

    /**
     * @return \DateTimeInterface|false|null
     */
    public function getDateTo(): \DateTimeInterface|false|null
    {
        return $this->dateTo;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @return int|null
     */
    public function getTotalLimit(): ?int
    {
        return $this->totalLimit;
    }
}

