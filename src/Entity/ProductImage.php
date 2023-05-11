<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Repository\ProductImageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ProductImageRepository::class)]
class ProductImage
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    #[ORM\ManyToOne(inversedBy: 'productImages')]
    #[ORM\JoinColumn(nullable: false)]
    private Product $product;

    #[ORM\Column(type: Types::TEXT)]
    private string $path;

    #[ORM\Column(type: Types::TEXT)]
    private string $thumbnail;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column]
    private int $size;

    public function __construct(
        ?Uuid $id,
        Product $product,
        string $path,
        string $thumbnail,
        string $name,
        int $size
    )
    {
        if ($id === null) {
            $id = Uuid::v4();
        }
        $this->id = $id;
        $this->product = $product;
        $this->path = $path;
        $this->thumbnail = $thumbnail;
        $this->name = $name;
        $this->size = $size;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getThumbnail(): string
    {
        return $this->thumbnail;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSize(): int
    {
        return $this->size;
    }
}
