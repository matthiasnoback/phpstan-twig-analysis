<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\PhpStan\Fixtures;

use Twig\Environment;

class TemplateRecursivelyIncludesAnotherTemplate
{
    private Environment $twig;

    public function foo(): void
    {
        $this->twig->render('template-recursively-includes-another-template.html.twig',);
    }
}
