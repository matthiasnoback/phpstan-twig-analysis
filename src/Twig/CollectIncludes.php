<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\Twig;

use Twig\Environment;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\IncludeNode;
use Twig\Node\Node;
use Twig\NodeVisitor\NodeVisitorInterface;

final class CollectIncludes implements NodeVisitorInterface
{
    /**
     * @var array<IncludeNode>
     */
    private array $includeNodes = [];

    /**
     * @return array<string>
     */
    public function includedTemplateNames(): array
    {
        return array_map(
            fn (ConstantExpression $expr) => $expr->getAttribute('value'),
            array_filter(
                array_map(fn (IncludeNode $node): Node => $node->getNode('expr'), $this->includeNodes),
                fn (Node $expr): bool => $expr instanceof ConstantExpression,
            )
        );
    }

    public function enterNode(Node $node, Environment $env): Node
    {
        if ($node instanceof IncludeNode) {
            $this->includeNodes[] = $node;
        }

        return $node;
    }

    public function leaveNode(Node $node, Environment $env): ?Node
    {
        return $node;
    }

    public function getPriority(): int
    {
        return 10;
    }
}
