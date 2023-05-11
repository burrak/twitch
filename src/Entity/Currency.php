<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Repository\CurrencyRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: CurrencyRepository::class)]
class Currency
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(length: 255)]
    private string $code;

    #[ORM\Column(length: 255)]
    private string $displayName;

    /**
     * @param string $name
     * @param string $code
     * @param string $displayName
     * @param Uuid|null $id
     */
    public function __construct(string $name, string $code, string $displayName, ?Uuid $id = null)
    {
        $this->name = $name;
        $this->code = $code;
        $this->displayName = $displayName;

        if ($id === null) {
            $id = Uuid::v4();
        }
        $this->id = $id;
    }


    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }
}
