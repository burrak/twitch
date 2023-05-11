<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    #[ORM\Column]
    private int $subscriber;

    #[ORM\ManyToOne(inversedBy: 'broadcasterEvents')]
    #[ORM\JoinColumn(nullable: false)]
    private User $broadcaster;

    #[ORM\Column]
    private int $tier;

    #[ORM\Column(length: 255)]
    private string $type;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    private WebhookEvent $webhookEvent;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?int $giftAmount = null;

    #[ORM\Column(nullable: true)]
    private ?int $duration = null;

    public function __construct(
        int $subscriber,
        User $user,
        int $tier,
        string $type,
        WebhookEvent $webhookEvent,
        ?int $giftAmount = null,
        ?int $duration = null,
        ?Uuid $id = null
    )
    {
        $this->subscriber = $subscriber;
        $this->broadcaster = $user;
        $this->tier = $tier;
        $this->type = $type;
        $this->webhookEvent = $webhookEvent;
        $this->giftAmount = $giftAmount;
        $this->duration = $duration;
        $this->createdAt = new \DateTimeImmutable();
        if ($id === null) {
            $id = Uuid::v4();
        }
        $this->id = $id;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getSubscriber(): int
    {
        return $this->subscriber;
    }

    public function getBroadcaster(): User
    {
        return $this->broadcaster;
    }

    public function getTier(): int
    {
        return $this->tier;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getWebhookEvent(): WebhookEvent
    {
        return $this->webhookEvent;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getGiftAmount(): ?int
    {
        return $this->giftAmount;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

}
