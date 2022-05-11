<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\Twig\Rule;

use PhpStanTwigAnalysis\Twig\TwigError;
use PhpStanTwigAnalysis\Twig\TwigNodeFinder;
use PhpStanTwigAnalysis\Twig\TwigRule;
use Twig\Node\Expression\GetAttrExpression;
use Twig\Node\Node;

final class LawOfDemeterRule implements TwigRule
{
    public function __construct(private ?int $maximumNumberOfDots)
    {
    }

    public function getNodeType(): string
    {
        return GetAttrExpression::class;
    }

    /**
     * @param GetAttrExpression $node
     */
    public function processNode(Node $node): array
    {
        if ($this->maximumNumberOfDots === null) {
            // The rule is not enabled
            return [];
        }

        if ($node->getNode('node') instanceof GetAttrExpression) {
            // We're only interested in leaf nodes
            return [];
        }

        $numberOfGetAttrExpressionsBeforeUs = count(TwigNodeFinder::filterParents(
            $node,
            fn (Node $node): bool => $node instanceof GetAttrExpression
        ));

        // +1 because this node is a GetAttrExpression itself
        if ($numberOfGetAttrExpressionsBeforeUs + 1 <= $this->maximumNumberOfDots) {
            return [];
        }

        return [
            TwigError::createForNode(
                $node,
                sprintf('Template uses more than the allowed number of dots (%d)', $this->maximumNumberOfDots),
                'You can change this number with the setting <fg=cyan>twig.maximum_number_of_dots</> in your <fg=cyan>%configurationFile%</>'
            ),
        ];
    }
}
