<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('price', function ($value) {
                return '$' . number_format($value, 2, '.', ',');
            }),
        ];
    }
}
