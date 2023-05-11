<?php

declare(strict_types = 1);

namespace App\DataFixtures;

use App\Entity\Currency;
use App\Entity\Scope;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private const SCOPES = [
        'user:read:subscriptions',
        'user:read:email',
        'channel:read:subscriptions',
        ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::SCOPES as $scope) {
            $scopeEntity = new Scope($scope);
            $manager->persist($scopeEntity);
        }

        $currency = new Currency('Euro', 'EUR', '€');
        $manager->persist($currency);

        $manager->flush();
    }
}
