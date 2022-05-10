<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\PhpStan\Fixtures;

use Twig\Environment;

class TemplateNotFound
{
    private Environment $twig;

    public function foo(): void
    {
        $this->twig->render('non-existing-template.html.twig',);
    }
}
