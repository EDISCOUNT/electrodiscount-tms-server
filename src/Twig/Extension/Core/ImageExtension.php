<?php

namespace App\Twig\Extension\Core;

use App\Twig\Runtime\Core\ImageRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class ImageExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('embed_image', [ImageRuntime::class, 'base64Filter']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('embed_image', [ImageRuntime::class, 'base64Filter']),
        ];
    }
}
