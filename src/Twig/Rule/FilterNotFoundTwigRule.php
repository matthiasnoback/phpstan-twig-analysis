<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\Twig\Rule;

use PhpStanTwigAnalysis\Twig\TwigError;
use PhpStanTwigAnalysis\Twig\TwigFactory;
use PhpStanTwigAnalysis\Twig\TwigRule;
use Twig\Environment;
use Twig\Node\Expression\FilterExpression;
use Twig\Node\Node;
use Twig\TwigFilter;

final class FilterNotFoundTwigRule implements TwigRule
{
    private Environment $twig;

    public function __construct(private TwigFactory $twigFactory)
    {
        $this->twig = $this->twigFactory->create();
    }

    public function getNodeType(): string
    {
        return FilterExpression::class;
    }

    /**
     * @param FilterExpression $node
     */
    public function processNode(Node $node): array
    {
        $filterName = $node->getNode('filter')
            ->getAttribute('value');

        $filter = $this->twig->getFilter($filterName);

        if ($filter instanceof TwigFilter) {
            return [];
        }

        return [TwigError::createForNode($node, sprintf('Unknown Twig filter: %s', $filterName))];
    }
}
