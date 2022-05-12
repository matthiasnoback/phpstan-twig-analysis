<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\Twig;

use Twig\Environment;

interface TwigFactory
{
    public function create(): Environment;
}
