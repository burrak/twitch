<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Repository\SubscriberRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: SubscriberRepository::class)]
class Subscriber
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    #[ORM\Column]
    private int $twitchId;

    #[ORM\Column(nullable: true)]
    private ?int $cumulativeMonths = null;

    #[ORM\Column(nullable: true)]
    private ?int $currentStreak = null;

    #[ORM\Column(nullable: true)]
    private ?int $maxStreak = null;

    #[ORM\Column(nullable: true)]
    private ?int $giftedTotal = null;

    #[ORM\ManyToOne(inversedBy: 'subscribers')]
    #[ORM\JoinColumn(nullable: false)]
    private User $streamer;

    #[ORM\Column]
    private int $tier;

    public function __construct(
        int $twitchId,
        User $streamer,
        int $tier,
        ?int $cumulativeMonths,
        ?int $currentStreak,
        ?int $maxStreak,
        ?int $giftedTotal,
        ?Uuid $id = null
    )
    {
        $this->twitchId = $twitchId;
        $this->streamer = $streamer;
        $this->tier = $tier;
        $this->cumulativeMonths = $cumulativeMonths;
        $this->currentStreak = $currentStreak;
        $this->maxStreak = $maxStreak;
        $this->giftedTotal = $giftedTotal;
        if ($id === null) {
            $id = Uuid::v4();
        }
        $this->id = $id;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getTwitchId(): int
    {
        return $this->twitchId;
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

    public function getStreamer(): User
    {
        return $this->streamer;
    }

    public function getTier(): int
    {
        return $this->tier;
    }
}
