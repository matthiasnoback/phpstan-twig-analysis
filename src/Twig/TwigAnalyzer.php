<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\Twig;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use Twig\NodeTraverser;

final class TwigAnalyzer
{
    private Environment $twig;

    /**
     * @param array<TwigRule> $twigRules
     */
    public function __construct(
        TwigFactory $twigFactory,
        private array $twigRules,
    ) {
        $this->twig = $twigFactory->create();
    }

    /**
     * @throws LoaderError
     */
    public function analyze(string $templateName, TwigAnalysis $twigAnalysis): void
    {
        $source = $this->twig->getLoader()
            ->getSourceContext($templateName);

        $twigAnalysis->addAnalyzedTemplate($templateName);

        // We have our own rules for finding undefined functions and don't want the parser to trigger a "SyntaxError"
        UnknownFunctionCallback::catchAllUnknownFunctions($this->twig, true);
        UnknownFilterCallback::catchAllUnknownFilters($this->twig, true);

        try {
            $nodeTree = $this->twig->parse($this->twig->tokenize($source));
        } catch (SyntaxError $error) {
            $twigAnalysis->addError(TwigError::createFromSyntaxError($error));
            return;
        }

        UnknownFunctionCallback::catchAllUnknownFunctions($this->twig, false);
        UnknownFilterCallback::catchAllUnknownFilters($this->twig, false);

        // Set the parent node as an attribute on each node, so rules can access traverse up the node tree:
        $nodeTraverser = new NodeTraverser($this->twig, [new SetParentNodeAsAttribute()]);
        $nodeTraverser->traverse($nodeTree);

        $collectErrors = new CollectErrors($this->twigRules);
        $collectIncludes = new CollectIncludes();

        $nodeTraverser = new NodeTraverser($this->twig, [$collectErrors, $collectIncludes]);
        $nodeTraverser->traverse($nodeTree);

        $twigAnalysis->addErrors($collectErrors->errors());
        $twigAnalysis->addTemplatesToBeAnalyzed($collectIncludes->includedTemplateNames());
    }
}
