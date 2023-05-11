<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Repository\WebhookEventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: WebhookEventRepository::class)]
class WebhookEvent
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    #[ORM\Column(length: 255)]
    private string $twitchId;

    #[ORM\Column(length: 255)]
    private string $type;

    #[ORM\ManyToOne(inversedBy: 'webhookEvents')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    /** @var Collection<int, Event>  */
    #[ORM\OneToMany(mappedBy: 'webhookEvent', targetEntity: Event::class)]
    private Collection $events;

    public function __construct(string $twitchId, string $type, User $user, \DateTimeImmutable $createdAt, ?Uuid $id = null)
    {
        $this->twitchId = $twitchId;
        $this->type = $type;
        $this->user = $user;
        $this->createdAt = $createdAt;
        $this->events = new ArrayCollection();
        if ($id === null) {
            $id = Uuid::v4();
        }
        $this->id = $id;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getTwitchId(): string
    {
        return $this->twitchId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events->add(
                new Event(
                    $event->getSubscriber(),
                    $event->getBroadcaster(),
                    $event->getTier(),
                    $event->getType(),
                    $this,
                    $event->getGiftAmount(),
                    $event->getDuration(),
                    $event->getId()
                )
            );
        }

        return $this;
    }
}
