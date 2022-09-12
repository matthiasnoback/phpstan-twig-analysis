<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\PhpStan\Fixtures;

use Twig\Environment;

class TemplateExtendsAnotherTemplate
{
    private Environment $twig;

    public function foo(): void
    {
        $this->twig->render('template-extends-another-template.html.twig',);
    }
}
