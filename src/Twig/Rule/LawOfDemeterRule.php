<?php
declare(strict_types=1);

namespace PhpStanTwigAnalysis\Twig\Rule;

use PhpStanTwigAnalysis\Twig\TwigError;
use PhpStanTwigAnalysis\Twig\TwigNodeFinder;
use PhpStanTwigAnalysis\Twig\TwigRule;
use Twig\Node\Expression\GetAttrExpression;
use Twig\Node\Expression\NameExpression;
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
            return [];
        }

        if ($node->getNode('node') instanceof GetAttrExpression) {
            // We're only interested in leaf nodes
            return [];
        }

        $numberOfGetAttrExpressionsBeforeUs = count(TwigNodeFinder::filterParents(
            $node, fn (Node $node) => $node instanceof GetAttrExpression
        ));
        if ($numberOfGetAttrExpressionsBeforeUs + 1 <= $this->maximumNumberOfDots) {
            return [];
        }

        return [
            TwigError::createForNode($node, sprintf('Template uses more than the allowed number of dots (%d)', $this->maximumNumberOfDots))
        ];
    }
}
