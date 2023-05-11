<?php

declare(strict_types = 1);

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use App\TwitchApiBundle\Service\ApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RefreshOauthTokenCommand extends Command
{
    public function __construct(private UserRepository $userRepository, private ApiService $apiService, private EntityManagerInterface $entityManager)
    {
        parent::__construct('oauth:refresh');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $broadcasters = $this->userRepository->findAll();

        foreach ($broadcasters as $broadcaster) {
            try {
                $newOauth = $this->apiService->refreshOauthToken($broadcaster->getRefreshToken());
            } catch (\Throwable $exception) {
                $newBroadcaster = new User(
                    $broadcaster->getUserId(),
                    $broadcaster->getUserName(),
                    'pepega',
                    $broadcaster->getRefreshToken(),
                    $broadcaster->getEmail(),
                    $broadcaster->getImage(),
                    $broadcaster->isStreamer(),
                    false,
                    $broadcaster->getUserScopes(),
                    $broadcaster->getWebhookEvents(),
                    $broadcaster->getBroadcasterEvents(),
                    $broadcaster->getId(),
                    $broadcaster->getCarts(),
                    $broadcaster->getOrders(),
                    $broadcaster->getStreamerOrders()
                );

                /** @phpstan-ignore-next-line */
                $this->entityManager->merge($newBroadcaster);
                $this->entityManager->flush();

                continue;
            }

            $newBroadcaster = new User(
                $broadcaster->getUserId(),
                $broadcaster->getUserName(),
                $newOauth->getAccessToken(),
                $broadcaster->getRefreshToken(),
                $broadcaster->getEmail(),
                $broadcaster->getImage(),
                $broadcaster->isStreamer(),
                true,
                $broadcaster->getUserScopes(),
                $broadcaster->getWebhookEvents(),
                $broadcaster->getBroadcasterEvents(),
                $broadcaster->getId(),
                $broadcaster->getCarts(),
                $broadcaster->getOrders(),
                $broadcaster->getStreamerOrders()
            );

            /** @phpstan-ignore-next-line */
            $this->entityManager->merge($newBroadcaster);
            $this->entityManager->flush();
        }

        return 0;
    }

}
