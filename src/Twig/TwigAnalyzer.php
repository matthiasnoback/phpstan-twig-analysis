<?php
declare(strict_types=1);

namespace PhpStanTwigAnalysis\Twig;

use Twig\Environment;
use Twig\NodeTraverser;

final class TwigAnalyzer
{
    /**
     * @param array<TwigRule> $twigRules
     */
    public function __construct(
        private Environment $twig,
        private array $twigRules,
    ) {

    }
    /**
     * @return array<TwigError>
     */
    public function analyze(string $templateName): array
    {
        $source = $this->twig->getLoader()
            ->getSourceContext($templateName);

        $collectErrors = new CollectErrors($this->twigRules);

        $nodeTree = $this->twig->parse($this->twig->tokenize($source));

        $nodeTraverser = new NodeTraverser($this->twig, [$collectErrors]);
        $nodeTraverser->traverse($nodeTree);

        return $collectErrors->errors();
    }
}