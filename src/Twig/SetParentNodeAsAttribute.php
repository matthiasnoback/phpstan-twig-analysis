<?php
declare(strict_types=1);

namespace PhpStanTwigAnalysis\Twig;

use Twig\Environment;
use Twig\Node\Node;
use Twig\NodeVisitor\NodeVisitorInterface;

final class SetParentNodeAsAttribute implements NodeVisitorInterface
{
    public function enterNode(Node $node, Environment $env): Node
    {
        foreach ($node as $subNode) {
            /** @var Node $subNode */
            $subNode->setAttribute(NodeAttribute::PARENT, $node);
        }

        return $node;
    }

    public function leaveNode(Node $node, Environment $env): ?Node
    {
        return $node;
    }

    public function getPriority(): int
    {
        return 0;
    }
}
