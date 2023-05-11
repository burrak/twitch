<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Repository\OrderItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
class OrderItem
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Product $product;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank()]
    #[Assert\GreaterThanOrEqual(1)]
    private int $quantity;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private Order $order;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\Column]
    private int $price;

    #[ORM\Column]
    private int $priceVat;

    #[ORM\Column]
    private int $vat;

     public function __construct(
         ?Uuid $id,
         Product $product,
         int $quantity,
         Order $order
     )
     {
         if ($id === null) {
             $id = Uuid::v4();
         }
         $this->id = $id;
         $this->product = $product;
         $this->title = $product->getTitle();
         $this->price = $product->getPrice();
         $this->priceVat = $product->getPriceVat();
         $this->vat = $product->getVat();
         $this->quantity = $quantity;
         $this->order = $order;
     }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * Tests if the given item given corresponds to the same order item.
     */
    public function equals(OrderItem $item): bool
    {
        return $this->getProduct()->getId() === $item->getProduct()->getId();
    }

    /**
     * Calculates the item total.
     *
     * @return int
     */
    public function getTotal(): int
    {
        return $this->getProduct()->getPrice() * $this->getQuantity();
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getPriceVat(): int
    {
        return $this->priceVat;
    }

    public function getVat(): int
    {
        return $this->vat;
    }
}
