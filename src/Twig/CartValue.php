<?php

declare(strict_types = 1);

namespace App\Twig;

use App\Entity\Product;
use Doctrine\Common\Collections\Collection;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CartValue extends AbstractExtension
{

    public function getFunctions(): array
    {
        return array(
            new TwigFunction('cart_quantity', [$this, 'getQuantity']),
        );
    }

    public function getQuantity(Collection $collection, Product $product): int
    {
        $cartItem = $collection->filter(function ($element) use ($product) {
            return $element->getProduct() === $product;
        });

        return $cartItem->first() ? $cartItem->first()->getQuantity() : 0;
    }

    public function getName(): string
    {
        return "cartQuantity";
    }

}
