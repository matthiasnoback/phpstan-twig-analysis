<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\PhpStan;

use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

final class TwigFactoryForTesting
{
    private static string $templateDir;

    public static function setTemplateDir(string $templateDir): void
    {
        self::$templateDir = $templateDir;
    }

    public function create(): Environment
    {
        $loader = new FilesystemLoader();
        $loader->addPath(self::$templateDir);

        $twig = new Environment($loader, [
            'debug' => true,
        ]);
        $twig->addExtension(new DebugExtension());

        return $twig;
    }
}
