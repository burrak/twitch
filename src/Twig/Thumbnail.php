<?php

declare(strict_types = 1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class Thumbnail extends AbstractExtension
{
    public function getFilters(): array
    {
        return array(
            new TwigFilter('thumbnail', [$this, 'thumbnail'])
        );
    }

    public function thumbnail(string $string): string
    {
        return 'thumbnail_' . $string;
    }

    public function getName(): string
    {
        return "thumbnail";
    }
}
