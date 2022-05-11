<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\Twig;

use Twig\Environment;
use Twig\TwigFunction;

final class UnknownFunctionCallback
{
    /**
     * @var array<string,bool>
     */
    private static array $registeredFor = [];

    private static bool $catchAllUnknownFunctions = false;

    public static function catchAllUnknownFunctions(Environment $twig, bool $yesOrNo): void
    {
        self::registerIfNecessary($twig);

        self::$catchAllUnknownFunctions = $yesOrNo;
    }

    public static function resolveUnknownFunction(string $name): ?TwigFunction
    {
        if (self::$catchAllUnknownFunctions) {
            return new TwigFunction($name, function (): void {
            });
        }

        return null;
    }

    public static function registerIfNecessary(Environment $twig): void
    {
        if (isset(self::$registeredFor[spl_object_hash($twig)])) {
            return;
        }

        $twig->registerUndefinedFunctionCallback([self::class, 'resolveUnknownFunction']);
        self::$registeredFor[spl_object_hash($twig)] = true;
    }
}
