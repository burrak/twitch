<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;


#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: "`order`")]
class Order
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    /** @var Collection<int, OrderItem> */
    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderItem::class, orphanRemoval: true)]
    private Collection $items;

    #[ORM\Column(type: 'string', length: 255)]
    private string $status = self::STATUS_CART;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $updatedAt;

    #[ORM\Column(length: 255)]
    private string $firstName;

    #[ORM\Column(length: 255)]
    private string $surname;

    #[ORM\Column(length: 255)]
    private string $street;

    #[ORM\Column(length: 255)]
    private string $city;

    #[ORM\Column]
    private int $zip;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Column(length: 255)]
    private string $country;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $note;

    #[ORM\Column]
    private int $deliveryPrice;

    #[ORM\ManyToOne(inversedBy: 'streamerOrders')]
    #[ORM\JoinColumn(nullable: false)]
    private User $streamer;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Currency $currency;

    /**
     * An order that is in progress, not placed yet.
     *
     * @var string
     */
    public const STATUS_CART = 'cart';

    public function __construct(
        ?Uuid $id,
        ?Collection $items,
        string $status,
        string $firstName,
        string $surname,
        string $street,
        string $city,
        int $zip,
        string $country,
        ?string $note,
        int $deliveryPrice,
        User $user,
        User $streamer,
        Currency $currency,
        ?\DateTimeInterface $createdAt
    )
    {
        if ($id === null) {
            $id = Uuid::v4();
        }
        $this->id = $id;
        if ($items === null) {
            $items = new ArrayCollection();
        }
        $this->items = $items;
        $this->status = $status;
        $this->firstName = $firstName;
        $this->surname = $surname;
        $this->street = $street;
        $this->city = $city;
        $this->zip = $zip;
        $this->country = $country;
        $this->note = $note;
        $this->deliveryPrice = $deliveryPrice;
        $this->user = $user;
        $this->streamer = $streamer;
        $this->currency = $currency;
        if ($createdAt === null) {
            $createdAt = new \DateTimeImmutable();
        }
        $this->createdAt = $createdAt;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    /**
     * @return Collection|OrderItem[]
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * Calculates the order total.
     */
    public function getTotal(): string
    {
        $total = 0;

        foreach ($this->getItems() as $item) {
            $total += $item->getTotal();
        }

        return number_format($total/100, 2, ',', ' ') . ' ' . $this->currency->getDisplayName();
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getZip(): int
    {
        return $this->zip;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function getStreamer(): ?User
    {
        return $this->streamer;
    }

    public function getDeliveryPrice(): int
    {
        return $this->deliveryPrice;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }
}
