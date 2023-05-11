<?php

declare(strict_types = 1);

namespace App\OAuthBundle\Facade;

use App\Entity\EshopConfig;
use App\Entity\User;
use App\OAuthBundle\Service\OAuthRegisterService;
use App\Repository\CurrencyRepository;
use App\Repository\EshopConfigRepository;
use App\Repository\WebhookEventRepository;
use App\TwitchApiBundle\DTO\Response\OAuthToken;
use App\TwitchApiBundle\DTO\Response\OAuthValidToken;
use App\TwitchApiBundle\DTO\Response\TwitchUser\TwitchUser;
use App\TwitchApiBundle\Service\ApiService;

class CreateUserFacade
{
    private const CHANNEL_READ_SUBSCRIPTIONS = "channel:read:subscriptions";
    private const CHANNEL_SUBSCRIBE = "channel.subscribe";
    private const CHANNEL_SUBSCRIBTION_END = "channel.subscription.end";
    private const CHANNEL_SUBSCRIBTION_GIFT = "channel.subscription.gift";
    private const CHANNEL_SUBSCRIBTION_MESSAGE = "channel.subscription.message";

    //private const EVENT_STATUS_ENABLED = 'enabled';
    //private const EVENT_STATUS_WEBHOOK_CALLBACK_VERIFICATION_PENDING = 'webhook_callback_verification_pending';
    private const EVEN_STATUS_WEBHOOK_CALLBACK_VERIFICATION_FAILED = 'webhook_callback_verification_failed';
    private const EVENT_STATUS_NOTIFICATION_FAILURES_EXCEEDED = 'notification_failures_exceeded';
    private const EVENT_STATUS_AUTHORIZATION_REVOKED = 'authorization_revoked';
    private const EVENT_STATUS_USER_REMOVED = 'user_removed';

    public function __construct(
        private OAuthRegisterService $OAuthRegisterService,
        private ApiService $apiService,
        private WebhookEventRepository $webhookEventRepository,
        private EshopConfigRepository $eshopConfigRepository,
        private CurrencyRepository $currencyRepository
    )
    {
    }

    public function registerUser(OAuthToken $OAuthToken, OAuthValidToken $OAuthValidToken, TwitchUser $twitchUser): User
    {
        $user = $this->OAuthRegisterService->user($OAuthToken, $OAuthValidToken, $twitchUser);

        if ($user->getUserScopes()->exists(function ($key, $value) { return $value->getScope()->getScope() === self::CHANNEL_READ_SUBSCRIPTIONS; })) {
            $this->subscribeIfNotExists($user, self::CHANNEL_SUBSCRIBE);
            $this->subscribeIfNotExists($user, self::CHANNEL_SUBSCRIBTION_END);
            $this->subscribeIfNotExists($user, self::CHANNEL_SUBSCRIBTION_GIFT);
            $this->subscribeIfNotExists($user, self::CHANNEL_SUBSCRIBTION_MESSAGE);

            $eshopConfig = $this->eshopConfigRepository->findOneBy(['streamer' => $user]);
            if ($eshopConfig === null) {
                /** @var \App\Entity\Currency $currency */
                $currency = $this->currencyRepository->findOneBy(['code' => 'EUR']);
                $eshopConfig = new EshopConfig(null, $user, $currency, 0);
                $this->eshopConfigRepository->save($eshopConfig, true);
            }

        }

        return $user;
    }

    private function subscribeIfNotExists(User $user, string $subscriptionType): void
    {
        if ($user->getWebhookEvents()->exists(function ($key, $value) use ($subscriptionType) { return $value->getType() === $subscriptionType; })) {
            /** @var \App\Entity\WebhookEvent $webhookEvent */
            $webhookEvent = $this->webhookEventRepository->findOneBy(['user' => $user, 'type' => $subscriptionType]);
            $events = $this->apiService->fetchSubscriptionsByUser($user->getUserId());
            foreach ($events as $event) {
                if ($event->getType() === $subscriptionType && in_array($event->getStatus(), [self::EVEN_STATUS_WEBHOOK_CALLBACK_VERIFICATION_FAILED, self::EVENT_STATUS_AUTHORIZATION_REVOKED, self::EVENT_STATUS_USER_REMOVED, self::EVENT_STATUS_NOTIFICATION_FAILURES_EXCEEDED])) {
                    $this->apiService->removeSubscription($event->getId());
                    $this->webhookEventRepository->remove($webhookEvent, true);
                    $subEvent = $this->apiService->createSubscription($subscriptionType, $user->getUserId());
                    $this->OAuthRegisterService->addEventSub($subEvent);

                    return;
                }
            }
            return;
        }

        $subEvent = $this->apiService->createSubscription($subscriptionType, $user->getUserId());
        $this->OAuthRegisterService->addEventSub($subEvent);
    }
}
