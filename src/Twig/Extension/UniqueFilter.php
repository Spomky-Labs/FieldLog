<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use Twig\Attribute\AsTwigFilter;

final readonly class UniqueFilter
{
    /**
     * @param array<mixed> $array
     * @return array<mixed>
     */
    #[AsTwigFilter('unique')]
    public static function unique(array $array): array
    {
        return array_unique($array);
    }
}
