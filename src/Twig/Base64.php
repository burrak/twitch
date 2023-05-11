<?php

declare(strict_types = 1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class Base64 extends AbstractExtension
{
    public function getFilters(): array
    {
        return array(
            new TwigFilter('base64_encode', [$this, 'base64Encode']),
            new TwigFilter('base64_decode', [$this, 'base64Decode'])
        );
    }

    public function base64Encode(string $string): string
    {
        return base64_encode($string);
    }

    public function base64Decode(string $string): string
    {
        return base64_decode($string);
    }

    public function getName(): string
    {
        return "base64";
    }
}
