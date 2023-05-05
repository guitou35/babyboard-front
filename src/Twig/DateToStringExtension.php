<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Datetime;

class DateToStringExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('dateToString', [$this, 'dateToString']),
        ];
    }

    public function dateToString($date): string
    {
        return $date["month"]."/".$date["day"]."/".$date["year"];
    }
}