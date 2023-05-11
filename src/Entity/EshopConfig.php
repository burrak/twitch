<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Repository\EshopConfigRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: EshopConfigRepository::class)]
class EshopConfig
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    #[ORM\OneToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $streamer;

    #[ORM\ManyToOne(targetEntity: Currency::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Currency $currency;

    #[ORM\Column]
    private int $deliveryPrice;

    public function __construct(
        ?Uuid $id,
        User $user,
        Currency $currency,
        int $deliveryPrice
    )
    {
        if ($id === null) {
            $id = Uuid::v4();
        }
        $this->id = $id;
        $this->streamer = $user;
        $this->currency = $currency;
        $this->deliveryPrice = $deliveryPrice;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getStreamer(): User
    {
        return $this->streamer;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getDeliveryPrice(): int
    {
        return $this->deliveryPrice;
    }
}
