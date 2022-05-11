<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\Twig\Rule;

use PhpStanTwigAnalysis\Twig\TwigError;
use PhpStanTwigAnalysis\Twig\TwigRule;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\IncludeNode;
use Twig\Node\Node;

final class NoDynamicIncludes implements TwigRule
{
    public function getNodeType(): string
    {
        return IncludeNode::class;
    }

    /**
     * @param IncludeNode $node
     */
    public function processNode(Node $node): array
    {
        if ($node->getNode('expr') instanceof ConstantExpression) {
            return [];
        }

        return [TwigError::createForNode($node, 'Template uses dynamic include')];
    }
}
