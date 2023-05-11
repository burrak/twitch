<?php

declare(strict_types = 1);

namespace App\WebhookBundle\Facade;

use App\Entity\Event;
use App\Entity\Subscriber;
use App\Repository\SubscriberRepository;
use App\Repository\UserRepository;
use App\Repository\WebhookEventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class EventFacade
{
    public function __construct(
        private SerializerInterface $serializer,
        private UserRepository $userRepository,
        private WebhookEventRepository $webhookEventRepository,
        private SubscriberRepository $subscriberRepository,
        private EntityManagerInterface $entityManager
    )
    {
    }

    public function createEvent(Request $request): void
    {
        /** @var \App\WebhookBundle\DTO\Subscription\Request $response */
        $response = $this->serializer->deserialize($request->getContent(), \App\WebhookBundle\DTO\Subscription\Request::class, 'json');
        if ($response->getEvent()->getIsAnonymous() === true) {
            return;
        }

        $broadcaster = $this->userRepository->findOneBy(['userId' => $response->getEvent()->getBroadcasterUserId()]);
        $webhookEvent = $this->webhookEventRepository->findOneBy(['twitchId' => $response->getSubscription()->getId()]);

        if ($webhookEvent === null || $broadcaster === null) {
            throw new \InvalidArgumentException();
        }

        $pepega = $this->subscriberRepository->findOneBy(['twitchId' => $response->getEvent()->getUserId(), 'streamer' => $broadcaster]);

        if ($response->getSubscription()->getType() === 'channel.subscribe') {
            $subscriber = new Subscriber(
                (int)$response->getEvent()->getUserId(),
                $broadcaster,
                (int)$response->getEvent()->getTier(),
                $pepega?->getCumulativeMonths(),
                1,
                $pepega?->getMaxStreak(),
                $pepega?->getGiftedTotal(),
                $pepega?->getId()
            );
            /** @phpstan-ignore-next-line */
            $this->entityManager->merge($subscriber);
            $duration = $response->getEvent()->getDurationMonths();
        }

        if ($response->getSubscription()->getType() === 'channel.subscription.gift') {
            $subscriber = new Subscriber(
                (int)$response->getEvent()->getUserId(),
                $broadcaster,
                (int)$response->getEvent()->getTier(),
                $pepega?->getCumulativeMonths(),
                $pepega?->getCurrentStreak(),
                $pepega?->getMaxStreak(),
                $response->getEvent()->getCumulativeTotal(),
                $pepega?->getId(),
            );
            /** @phpstan-ignore-next-line */
            $this->entityManager->merge($subscriber);
            $giftAmount = $response->getEvent()->getTotal();
        }

        if ($response->getSubscription()->getType() === 'channel.subscription.message' && $pepega instanceof Subscriber) {
            $subscriber = new Subscriber(
                (int)$response->getEvent()->getUserId(),
                $broadcaster,
                (int)$response->getEvent()->getTier(),
                $response->getEvent()->getCumulativeMonths(),
                $response->getEvent()->getStreakMonths(),
                $pepega->getMaxStreak() < $response->getEvent()->getStreakMonths() ? $response->getEvent()->getStreakMonths() : $pepega->getMaxStreak(),
                $response->getEvent()->getCumulativeTotal(),
                $pepega->getId(),
            );
            /** @phpstan-ignore-next-line */
            $this->entityManager->merge($subscriber);
            $duration = $response->getEvent()->getDurationMonths();
        }

        $event = new Event(
            (int) $response->getEvent()->getUserId(),
            $broadcaster,
            (int)$response->getEvent()->getTier(),
            $response->getSubscription()->getType(),
            $webhookEvent,
            $giftAmount ?? null,
            $duration ?? null
        );


        $this->entityManager->persist($event);
        $this->entityManager->flush();
    }

}
