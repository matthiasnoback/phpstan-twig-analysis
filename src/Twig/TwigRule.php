<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\Twig;

use Twig\Node\Node;

interface TwigRule
{
    public function getNodeType(): string;

    /**
     * @return array<TwigError>
     */
    public function processNode(Node $node): array;
}
