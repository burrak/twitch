<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Repository\ScopeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ScopeRepository::class)]
class Scope
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    #[ORM\Column(length: 255)]
    private string $scope;

    /** @var Collection<int, UserScope>  */
    #[ORM\OneToMany(mappedBy: 'scope', targetEntity: UserScope::class)]
    private Collection $userScopes;

    public function __construct(string $scope, ?Uuid $id = null)
    {
        $this->scope = $scope;
        $this->userScopes = new ArrayCollection();
        if ($id === null) {
            $id = Uuid::v4();
        }
        $this->id = $id;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getScope(): string
    {
        return $this->scope;
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
            $this->userScopes->add(new UserScope($userScope->getUser(), $this, $userScope->getId()));
        }

        return $this;
    }
}
