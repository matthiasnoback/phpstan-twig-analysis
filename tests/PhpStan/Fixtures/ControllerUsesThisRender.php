<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\PhpStan\Fixtures;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ControllerUsesThisRender extends AbstractController
{
    public function foo(): Response
    {
        return $this->render('uses-forbidden-function.html.twig');
    }
}
