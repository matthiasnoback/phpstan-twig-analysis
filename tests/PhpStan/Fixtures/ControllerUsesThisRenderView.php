<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\PhpStan\Fixtures;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ControllerUsesThisRenderView extends AbstractController
{
    public function foo(): Response
    {
        return new Response($this->renderView('template.html.twig'));
    }
}
