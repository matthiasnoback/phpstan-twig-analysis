<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\PhpStan;

use PhpStanTwigAnalysis\Twig\TwigFactory;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

final class TwigFactoryForTesting implements TwigFactory
{
    public function __construct(private string $templateDir)
    {
    }

    public function create(): Environment
    {
        $loader = new FilesystemLoader();
        $loader->addPath($this->templateDir);

        $twig = new Environment($loader, [
            'debug' => true,
        ]);
        $twig->addExtension(new DebugExtension());

        return $twig;
    }
}
