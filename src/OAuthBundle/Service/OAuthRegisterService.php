<?php

declare(strict_types = 1);

namespace App\OAuthBundle\Service;

use App\Entity\WebhookEvent;
use App\Entity\User;
use App\Entity\UserScope;
use App\Repository\ScopeRepository;
use App\Repository\UserRepository;
use App\TwitchApiBundle\DTO\Response\EventSub\EventSubResponse;
use App\TwitchApiBundle\DTO\Response\OAuthToken;
use App\TwitchApiBundle\DTO\Response\OAuthValidToken;
use App\TwitchApiBundle\DTO\Response\TwitchUser\TwitchUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OAuthRegisterService
{
    public function __construct(
        private UserRepository $userRepository,
        private ScopeRepository $scopeRepository,
        private EntityManagerInterface $entityManager
    )
    {
    }

    public function user(OAuthToken $OAuthToken, OAuthValidToken $OAuthValidToken, TwitchUser $twitchUser): User
    {
        $entity = $this->userRepository->findOneBy(['userId' => $OAuthValidToken->getUserId()]);
        if ($entity === null) {
            $entity = new User(
                (int)$OAuthValidToken->getUserId(),
                $OAuthValidToken->getLogin(),
                $OAuthToken->getAccessToken(),
                $OAuthToken->getRefreshToken(),
                $twitchUser->getEmail(),
                $twitchUser->getProfileImageUrl(),
                false,
                true
            );
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
            /** @var User $entity */
            $entity = $this->userRepository->findOneBy(['email' => $entity->getEmail()]);


            foreach ($OAuthToken->getScope() as $scope) {
                $scopeEntity = $this->scopeRepository->findOneBy(['scope' => $scope]);
                if ($scopeEntity === null) {
                    continue;
                }
                $userScopeEntity = new UserScope($entity, $scopeEntity);
                $this->entityManager->persist($userScopeEntity);
                $this->entityManager->flush();
            }


            return $entity;
        }

        $entity = new User(
            (int)$OAuthValidToken->getUserId(),
            $OAuthValidToken->getLogin(),
            $OAuthToken->getAccessToken(),
            $OAuthToken->getRefreshToken(),
            $twitchUser->getEmail(),
            $twitchUser->getProfileImageUrl(),
            $entity->isStreamer(),
            true,
            $entity->getUserScopes(),
            $entity->getWebhookEvents(),
            $entity->getBroadcasterEvents(),
            $entity->getId(),
            $entity->getCarts(),
            $entity->getOrders(),
            $entity->getStreamerOrders()
        );
        /** @phpstan-ignore-next-line */
        $this->entityManager->merge($entity);
        $this->entityManager->flush();
        /** @var User $entity */
        $entity = $this->userRepository->findOneBy(['email' => $entity->getEmail()]);

        foreach ($OAuthToken->getScope() as $scope) {
            $scopeEntity = $this->scopeRepository->findOneBy(['scope' => $scope]);
            // if ($scopeEntity !== null && !$entity->getUserScopes()->contains($scopeEntity)) {
            // if ($entity->getUserScopes()->contains($scopeEntity) || $scopeEntity === null) {
            if ($entity->getUserScopes()->exists(function ($key, $value) use ($scopeEntity) { return $value->getScope() === $scopeEntity && $value->getScope()->getScope() === $scopeEntity->getScope(); }) || $scopeEntity === null) {
                continue;
            }
            $userScopeEntity = new UserScope($entity, $scopeEntity);
            $this->entityManager->persist($userScopeEntity);
            $this->entityManager->flush();
        }


        return $entity;
    }

    public function addEventSub(EventSubResponse $eventSubResponse): WebhookEvent
    {
        $user = $this->userRepository->findOneBy(['userId' => $eventSubResponse->getCondition()->getBroadcasterUserId()]);

        if ($user === null) {
            throw new NotFoundHttpException('User not found');
        }

        $event = new WebhookEvent(
            $eventSubResponse->getId(),
            $eventSubResponse->getType(),
            $user,
            new \DateTimeImmutable($eventSubResponse->getCreatedAt())
        );

        $this->entityManager->persist($event);
        $this->entityManager->flush();

        return $event;
    }

}
