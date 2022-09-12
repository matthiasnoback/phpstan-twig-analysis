<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\PhpStan\Fixtures;

use Twig\Environment;

class TemplateImportsAnotherTemplate
{
    private Environment $twig;

    public function foo(): void
    {
        $this->twig->render('template-imports-another-template.html.twig',);
    }
}
