<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\Twig;

use PhpStanTwigAnalysis\PhpStan\IncludedTemplate;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use Twig\NodeTraverser;

final class TwigAnalyzer
{
    /**
     * @param array<TwigRule> $twigRules
     */
    public function __construct(
        private TwigFactory $twigFactory,
        private array $twigRules,
    ) {
    }

    /**
     * @throws LoaderError
     */
    public function analyze(IncludedTemplate $template, TwigAnalysis $twigAnalysis): void
    {
        $twig = $this->twigFactory->create();

        $source = $twig->getLoader()
            ->getSourceContext($template->templateName);

        $twigAnalysis->addAnalyzedTemplate(new ResolvedTemplate($template, $source->getPath()));

        // We have our own rules for finding undefined functions and don't want the parser to trigger a "SyntaxError"
        UnknownFunctionCallback::catchAllUnknownFunctions($twig, true);
        UnknownFilterCallback::catchAllUnknownFilters($twig, true);

        try {
            $nodeTree = $twig->parse($twig->tokenize($source));
        } catch (SyntaxError $error) {
            $twigAnalysis->addError(TwigError::createFromSyntaxError($error));
            return;
        }

        UnknownFunctionCallback::catchAllUnknownFunctions($twig, false);
        UnknownFilterCallback::catchAllUnknownFilters($twig, false);

        // Set the parent node as an attribute on each node, so rules can access traverse up the node tree:
        $nodeTraverser = new NodeTraverser($twig, [new SetParentNodeAsAttribute()]);
        $nodeTraverser->traverse($nodeTree);

        $collectErrors = new CollectErrors($this->twigRules);
        $collectIncludes = new CollectIncludes();

        $nodeTraverser = new NodeTraverser($twig, [$collectErrors, $collectIncludes]);
        $nodeTraverser->traverse($nodeTree);

        $twigAnalysis->addErrors($collectErrors->errors());
        $twigAnalysis->addTemplatesToBeAnalyzed($collectIncludes->includedTemplates());
    }
}
