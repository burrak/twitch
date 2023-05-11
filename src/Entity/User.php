<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'email_idx', columns: ['email'])]
class User implements UserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    #[ORM\Column]
    private int $userId;

    #[ORM\Column(length: 255)]
    private string $userName;

    #[ORM\Column(length: 255)]
    private string $accessToken;

    #[ORM\Column(length: 255)]
    private string $refreshToken;

    /** @var Collection<int, UserScope> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserScope::class, cascade: ['persist', 'remove'])]
    private Collection $userScopes;

    #[ORM\Column(length: 255)]
    private string $email;

    /** @var Collection<int, WebhookEvent> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: WebhookEvent::class)]
    private Collection $webhookEvents;

    /** @var Collection<int, Event> */
    #[ORM\OneToMany(mappedBy: 'broadcaster', targetEntity: Event::class)]
    private Collection $broadcasterEvents;

    #[ORM\Column]
    private bool $streamer;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $image = null;

    #[ORM\Column]
    private bool $twitchAuthorized;

    /** @var Collection<int, Subscriber> */
    #[ORM\OneToMany(mappedBy: 'streamer', targetEntity: Subscriber::class)]
    private Collection $subscribers;

    /** @var Collection<int, Product> */
    #[ORM\OneToMany(mappedBy: 'streamer', targetEntity: Product::class)]
    private Collection $products;

    /** @var Collection<int, Cart> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Cart::class)]
    private Collection $carts;

    /** @var Collection<int, Order> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Order::class)]
    private Collection $orders;

    /** @var Collection<int, Order> */
    #[ORM\OneToMany(mappedBy: 'streamer', targetEntity: Order::class)]
    private Collection $streamerOrders;

    public function __construct(
        int $userId,
        string $userName,
        string $accessToken,
        string $refreshToken,
        string $email,
        ?string $image,
        bool $streamer,
        bool $twitchAuthorized,
        ?Collection $userScopes = null,
        ?Collection $webhookEvents = null,
        ?Collection $broadcasterEvents = null,
        ?Uuid $id = null,
        ?Collection $carts = null,
        ?Collection $orders = null,
        ?Collection $streamerOrders = null,
    )
    {
        $this->userId  = $userId;
        $this->userName = $userName;
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->email = $email;
        $this->image = $image;
        if ($userScopes === null) {
            $userScopes = new ArrayCollection();
        }
        $this->userScopes = $userScopes;

        if ($webhookEvents === null) {
            $webhookEvents = new ArrayCollection();
        }
        $this->webhookEvents = $webhookEvents;

        if ($broadcasterEvents === null) {
            $broadcasterEvents = new ArrayCollection();
        }
        $this->broadcasterEvents = $broadcasterEvents;
        $this->streamer = $streamer;
        $this->twitchAuthorized = $twitchAuthorized;
        if ($id === null) {
            $id = Uuid::v4();
        }
        $this->id = $id;
        $this->subscribers = new ArrayCollection();
        $this->products = new ArrayCollection();
        if ($carts === null) {
            $carts = new ArrayCollection();
        }
        $this->carts = $carts;

        if ($orders === null) {
            $orders = new ArrayCollection();
        }
        $this->orders = $orders;

        if ($streamerOrders === null) {
            $streamerOrders = new ArrayCollection();
        }
        $this->streamerOrders = $streamerOrders;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    /**
     * @return Collection<int, UserScope>
     */
    public function getUserScopes(): Collection
    {
        return $this->userScopes;
    }

    public function addUserScope(UserScope $userScope): self
    {
        if (!$this->userScopes->contains($userScope)) {
            $this->userScopes->add(new UserScope($this, $userScope->getScope(), $userScope->getId()));
        }

        return $this;
    }

    public function getRoles(): array
    {
        if ($this->isStreamer()) {
            return ['ROLE_USER', 'ROLE_STREAMER', 'ROLE_' . $this->userName];
        }

        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return $this->getUserName();
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return Collection<int, WebhookEvent>
     */
    public function getWebhookEvents(): Collection
    {
        return $this->webhookEvents;
    }

    public function addEvent(WebhookEvent $event): self
    {
        if (!$this->webhookEvents->contains($event)) {
            $this->webhookEvents->add(new WebhookEvent($event->getTwitchId(), $event->getType(), $this, $event->getCreatedAt(), $event->getId()));
        }

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getBroadcasterEvents(): Collection
    {
        return $this->broadcasterEvents;
    }

    public function addBroadcasterEvent(Event $broadcasterEvent): self
    {
        if (!$this->broadcasterEvents->contains($broadcasterEvent)) {
            $this->broadcasterEvents->add(
                new Event(
                    $broadcasterEvent->getSubscriber(),
                    $this,
                    $broadcasterEvent->getTier(),
                    $broadcasterEvent->getType(),
                    $broadcasterEvent->getWebhookEvent(),
                    $broadcasterEvent->getGiftAmount(),
                    $broadcasterEvent->getDuration(),
                    $broadcasterEvent->getId()
                )
            );
        }

        return $this;
    }

    public function isStreamer(): bool
    {
        return $this->streamer;
    }

    public function isTwitchAuthorized(): bool
    {
        return $this->twitchAuthorized;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * @return Collection<int, Subscriber>
     */
    public function getSubscribers(): Collection
    {
        return $this->subscribers;
    }

    public function addSubscriber(Subscriber $subscriber): self
    {
        if (!$this->subscribers->contains($subscriber)) {
            $this->subscribers->add(
                new Subscriber(
                    $subscriber->getTwitchId(),
                    $this,
                    $subscriber->getTier(),
                    $subscriber->getCumulativeMonths(),
                    $subscriber->getCurrentStreak(),
                    $subscriber->getMaxStreak(),
                    $subscriber->getGiftedTotal(),
                    $subscriber->getId()
                )
            );
        }

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function getPassword(): string
    {
        return '';
    }

    /**
     * @return Collection<int, Cart>
     */
    public function getCarts(): Collection
    {
        return $this->carts;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getStreamerOrders(): Collection
    {
        return $this->streamerOrders;
    }
}
