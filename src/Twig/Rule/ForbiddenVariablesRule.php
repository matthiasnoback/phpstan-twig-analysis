<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\Twig\Rule;

use PhpStanTwigAnalysis\Twig\TwigError;
use PhpStanTwigAnalysis\Twig\TwigRule;
use Twig\Node\Expression\NameExpression;
use Twig\Node\Node;

final class ForbiddenVariablesRule implements TwigRule
{
    /**
     * @param array<string> $forbiddenVariables
     */
    public function __construct(private array $forbiddenVariables)
    {
    }

    public function getNodeType(): string
    {
        return NameExpression::class;
    }

    /**
     * @param NameExpression $node
     */
    public function processNode(Node $node): array
    {
        $variableName = $node->getAttribute('name');

        if (! in_array($variableName, $this->forbiddenVariables, true)) {
            return [];
        }

        return [TwigError::createForNode($node, sprintf('Forbidden variable used: %s', $variableName))];
    }
}
