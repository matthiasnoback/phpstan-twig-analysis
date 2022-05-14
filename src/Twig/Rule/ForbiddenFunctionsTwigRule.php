<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\Twig\Rule;

use PhpStanTwigAnalysis\Twig\TwigError;
use PhpStanTwigAnalysis\Twig\TwigRule;
use Twig\Node\Expression\FunctionExpression;
use Twig\Node\Node;

final class ForbiddenFunctionsTwigRule implements TwigRule
{
    /**
     * @param array<string> $forbiddenFunctions
     */
    public function __construct(private array $forbiddenFunctions)
    {
    }

    public function getNodeType(): string
    {
        return FunctionExpression::class;
    }

    /**
     * @param FunctionExpression $node
     */
    public function processNode(Node $node): array
    {
        $functionName = $node->getAttribute('name');

        if (! in_array($functionName, $this->forbiddenFunctions, true,)) {
            return [];
        }

        return [TwigError::createForNode($node, sprintf('Forbidden function used: %s', $functionName))];
    }
}
