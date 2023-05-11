<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private User $streamer;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private int $price;

    #[ORM\Column(nullable: true)]
    private ?int $cumulativeMonths = null;

    #[ORM\Column(nullable: true)]
    private ?int $currentStreak = null;

    #[ORM\Column(nullable: true)]
    private ?int $maxStreak = null;

    #[ORM\Column(nullable: true)]
    private ?int $giftedTotal = null;

    #[ORM\Column(nullable: true)]
    private ?int $tier = null;

    #[ORM\Column]
    private int $priceVat;

    #[ORM\Column]
    private int $vat;

    #[ORM\Column]
    private bool $subscriber;

    #[ORM\Column(nullable: true)]
    private ?int $orderLimit = null;

    /** @var Collection<int, ProductImage> */
    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ProductImage::class)]
    private Collection $productImages;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateFrom = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateTo = null;

    #[ORM\Column]
    private bool $active;

    #[ORM\Column(nullable: true)]
    private ?int $totalLimit = null;

    /**
     * @param Uuid|null $id
     * @param User $streamer
     * @param string $title
     * @param string|null $description
     * @param int $price
     */
    public function __construct(
        ?Uuid $id,
        User $streamer,
        string $title,
        int $price,
        int $priceVat,
        int $vat,
        ?string $description,
        bool $subscriber,
        ?int $cumulativeMonths,
        ?int $currentStreak,
        ?int $maxStreak,
        ?int $giftedTotal,
        ?int $tier,
        ?int $orderLimit,
        ?int $totalLimit,
        \DateTimeInterface|false|null $dateFrom,
        \DateTimeInterface|false|null $dateTo,
        bool $active,
        ?Collection $productImages
    )
    {
        if ($id === null) {
            $id = Uuid::v4();
        }
        $this->id = $id;
        $this->streamer = $streamer;
        $this->title = $title;
        $this->price = $price;
        $this->priceVat = $priceVat;
        $this->vat = $vat;
        $this->description = $description;
        $this->subscriber = $subscriber;
        $this->cumulativeMonths = $cumulativeMonths;
        $this->currentStreak = $currentStreak;
        $this->maxStreak = $maxStreak;
        $this->giftedTotal = $giftedTotal;
        $this->tier = $tier;
        $this->orderLimit = $orderLimit;
        $this->totalLimit = $totalLimit;
        if ($dateFrom === false) {
            $dateFrom = null;
        }
        $this->dateFrom = $dateFrom;
        if ($dateTo === false) {
            $dateTo = null;
        }
        $this->dateTo = $dateTo;
        $this->active = $active;

        if ($productImages === null) {
            $productImages = new ArrayCollection();
        }
        $this->productImages = $productImages;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getStreamer(): User
    {
        return $this->streamer;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getCumulativeMonths(): ?int
    {
        return $this->cumulativeMonths;
    }


    public function getCurrentStreak(): ?int
    {
        return $this->currentStreak;
    }

    public function getMaxStreak(): ?int
    {
        return $this->maxStreak;
    }

    public function getGiftedTotal(): ?int
    {
        return $this->giftedTotal;
    }

    public function getTier(): ?int
    {
        return $this->tier;
    }

    public function getPriceVat(): int
    {
        return $this->priceVat;
    }

    public function getVat(): int
    {
        return $this->vat;
    }

    public function isSubscriber(): bool
    {
        return $this->subscriber;
    }

    public function getOrderLimit(): ?int
    {
        return $this->orderLimit;
    }

    /**
     * @return Collection<int, ProductImage>
     */
    public function getProductImages(): Collection
    {
        return $this->productImages;
    }

    public function isEligible(?Subscriber $subscriber): bool
    {
        if ($subscriber === null) {
            return false;
        }

        return
            $subscriber->getTier() >= $this->getTier() &&
            $subscriber->getGiftedTotal() >= $this->getGiftedTotal() &&
            $subscriber->getMaxStreak() >= $this->getMaxStreak() &&
            $subscriber->getCurrentStreak() >= $this->getCurrentStreak() &&
            $subscriber->getCumulativeMonths() >= $this->getCumulativeMonths();
    }

    public function getDateFrom(): \DateTimeInterface|false|null
    {
        return $this->dateFrom;
    }

    public function getDateTo(): \DateTimeInterface|false|null
    {
        return $this->dateTo;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getTotalLimit(): ?int
    {
        return $this->totalLimit;
    }
}
