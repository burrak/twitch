<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: CartRepository::class)]
class Cart
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    #[ORM\ManyToOne(inversedBy: 'carts')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Product $product;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private User $streamer;

    #[ORM\Column]
    private int $quantity;

    public function __construct(
        ?Uuid $id,
        User $user,
        Product $product,
        User $streamer,
        int $quantity
    )
    {
        if ($id === null) {
            $id = Uuid::v4();
        }
        $this->id = $id;
        $this->user = $user;
        $this->product = $product;
        $this->streamer = $streamer;
        $this->quantity = $quantity;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getStreamer(): User
    {
        return $this->streamer;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}
