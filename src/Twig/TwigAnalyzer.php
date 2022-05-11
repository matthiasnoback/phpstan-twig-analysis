<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\Twig;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
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
     * @throws LoaderError
     */
    public function analyze(string $templateName, TwigAnalysis $twigAnalysis): void
    {
        $source = $this->twig->getLoader()
            ->getSourceContext($templateName);

        $twigAnalysis->addAnalyzedTemplate($templateName);

        try {
            $nodeTree = $this->twig->parse($this->twig->tokenize($source));
        } catch (SyntaxError $error) {
            $twigAnalysis->addError(TwigError::createFromSyntaxError($error));
            return;
        }

        $collectErrors = new CollectErrors($this->twigRules);
        $collectIncludes = new CollectIncludes();

        $nodeTraverser = new NodeTraverser($this->twig, [$collectErrors, $collectIncludes]);
        $nodeTraverser->traverse($nodeTree);

        $twigAnalysis->addErrors($collectErrors->errors());
        $twigAnalysis->addTemplatesToBeAnalyzed($collectIncludes->includedTemplateNames());
    }
}
