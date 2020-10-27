<?php

namespace App\Twig;

use App\Entity\LikeNotification;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigTest;

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

    public function getTests()
    {
        return [
            new TwigTest('like_notification', function ($object) {
                return ($object instanceof LikeNotification);
            }),
        ];
    }
}
