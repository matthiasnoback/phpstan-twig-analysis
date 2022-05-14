<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\Twig\Rule;

use PhpStanTwigAnalysis\Twig\TwigError;
use PhpStanTwigAnalysis\Twig\TwigFactory;
use PhpStanTwigAnalysis\Twig\TwigRule;
use Twig\Environment;
use Twig\Node\Expression\FunctionExpression;
use Twig\Node\Node;
use Twig\TwigFunction;

final class FunctionNotFoundTwigRule implements TwigRule
{
    private Environment $twig;

    public function __construct(private TwigFactory $twigFactory)
    {
        $this->twig = $this->twigFactory->create();
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

        $function = $this->twig->getFunction($functionName);

        if ($function instanceof TwigFunction) {
            return [];
        }

        return [TwigError::createForNode($node, sprintf('Call to unknown Twig function: %s()', $functionName))];
    }
}
