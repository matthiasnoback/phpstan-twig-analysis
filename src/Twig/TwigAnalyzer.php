<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\Twig;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use Twig\NodeTraverser;
use Twig\TwigFunction;

final class TwigAnalyzer
{
    private bool $catchAllNonExistingTwigFunctions = true;

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

        // We have our own rules for finding undefined functions and don't want the parser to trigger a "SyntaxError"
        $this->twig->registerUndefinedFunctionCallback([$this, 'returnNoopTwigFunction']);
        $this->catchAllNonExistingTwigFunctions = true;

        try {
            $nodeTree = $this->twig->parse($this->twig->tokenize($source));
        } catch (SyntaxError $error) {
            $twigAnalysis->addError(TwigError::createFromSyntaxError($error));
            return;
        }

        $this->catchAllNonExistingTwigFunctions = false;

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

    public function returnNoopTwigFunction(string $name): ?TwigFunction
    {
        if ($this->catchAllNonExistingTwigFunctions) {
            return new TwigFunction($name, function (): void {
            });
        }

        return null;
    }
}
