<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\Twig;

use Twig\Environment;

final class CachedTwigFactory implements TwigFactory
{
    private ?Environment $twig = null;

    public function __construct(private TwigFactory $realFactory)
    {
    }

    public function create(): Environment
    {
        if ($this->twig === null) {
            $this->twig = $this->realFactory->create();
        }

        return $this->twig;
    }
}
