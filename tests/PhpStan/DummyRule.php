<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\PhpStan;

use PhpStanTwigAnalysis\Twig\TwigError;
use PhpStanTwigAnalysis\Twig\TwigRule;
use Twig\Node\ModuleNode;
use Twig\Node\Node;

final class DummyRule implements TwigRule
{
    public function getNodeType(): string
    {
        return ModuleNode::class;
    }

    public function processNode(Node $node): array
    {
        return [TwigError::createForNode($node, 'Error in template')];
    }
}
