<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Repository\UserScopeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserScopeRepository::class)]
class UserScope
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    #[ORM\ManyToOne(inversedBy: 'userScopes')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\ManyToOne(inversedBy: 'userScopes')]
    #[ORM\JoinColumn(nullable: false)]
    private Scope $scope;

    public function __construct(User $user, Scope $scope, ?Uuid $id = null)
    {
        $this->user = $user;
        $this->scope = $scope;
        if ($id === null) {
            $id = Uuid::v4();
        }
        $this->id = $id;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getScope(): Scope
    {
        return $this->scope;
    }
}
