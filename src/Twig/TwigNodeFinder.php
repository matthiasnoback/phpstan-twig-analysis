<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\Twig;

use Twig\Node\Node;

final class TwigNodeFinder
{
    /**
     * @param callable(Node): bool $filter
     * @return array<Node>
     */
    public static function filterParents(Node $node, callable $filter): array
    {
        $matchingNodes = [];

        $currentNode = $node;

        while ($currentNode->hasAttribute(NodeAttribute::PARENT)) {
            $currentNode = $currentNode->getAttribute(NodeAttribute::PARENT);
            if ($filter($currentNode)) {
                $matchingNodes[] = $currentNode;
            } else {
                break;
            }
        }

        return $matchingNodes;
    }
}
