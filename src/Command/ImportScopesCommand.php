<?php

declare(strict_types = 1);

namespace App\Command;

use App\Entity\Scope;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportScopesCommand extends Command
{
    private const SCOPES = [
    "analytics:read:extensions",
    "analytics:read:games",
    "bits:read",
    "channel:edit:commercial",
    "channel:manage:broadcast",
    "channel:read:charity",
    "channel:manage:extensions",
    "channel:manage:moderators",
    "channel:manage:polls",
    "channel:manage:predictions",
    "channel:manage:raids",
    "channel:manage:redemptions",
    "channel:manage:schedule",
    "channel:manage:videos",
    "channel:read:editors",
    "channel:read:goals",
    "channel:read:hype_train",
    "channel:read:polls",
    "channel:read:predictions",
    "channel:read:redemptions",
    "channel:read:stream_key",
    "channel:read:subscriptions",
    "channel:read:vips",
    "channel:manage:vips",
    "clips:edit",
    "moderation:read",
    "moderator:manage:announcements",
    "moderator:manage:automod",
    "moderator:read:automod_settings",
    "moderator:manage:automod_settings",
    "moderator:manage:banned_users",
    "moderator:read:blocked_terms",
    "moderator:manage:blocked_terms",
    "moderator:manage:chat_messages",
    "moderator:read:chat_settings",
    "moderator:manage:chat_settings",
    "user:edit",
    "user:edit:follows",
    "user:manage:blocked_users",
    "user:read:blocked_users",
    "user:read:broadcast",
    "user:manage:chat_color",
    "user:read:email",
    "user:read:follows",
    "user:read:subscriptions",
    "user:manage:whispers",
];
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct('scopes:import');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach (self::SCOPES as $scope) {
            $scopeEntity = new Scope($scope);
            $this->entityManager->persist($scopeEntity);
        }

        $this->entityManager->flush();

        return 0;
    }
}
