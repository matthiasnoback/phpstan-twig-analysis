<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\PhpStan\UnusedTemplatesTest\Templates;

use Twig\Environment;

class TemplateIncludesAnotherTemplate
{
    private Environment $twig;

    public function foo(): void
    {
        $this->twig->render('template-includes-another-template.html.twig',);
    }
}
