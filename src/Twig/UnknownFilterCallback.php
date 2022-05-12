<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\Twig;

use Twig\Environment;
use Twig\TwigFilter;

final class UnknownFilterCallback
{
    /**
     * @var array<string,bool>
     */
    private static array $registeredFor = [];

    private static bool $catchAllUnknownFilters = false;

    public static function catchAllUnknownFilters(Environment $twig, bool $yesOrNo): void
    {
        self::registerIfNecessary($twig);

        self::$catchAllUnknownFilters = $yesOrNo;
    }

    public static function resolveUnknownFilter(string $name): ?TwigFilter
    {
        if (self::$catchAllUnknownFilters) {
            return new TwigFilter($name, function (): void {
            });
        }

        return null;
    }

    public static function registerIfNecessary(Environment $twig): void
    {
        if (isset(self::$registeredFor[spl_object_hash($twig)])) {
            return;
        }

        $twig->registerUndefinedFilterCallback([self::class, 'resolveUnknownFilter']);
        self::$registeredFor[spl_object_hash($twig)] = true;
    }
}
