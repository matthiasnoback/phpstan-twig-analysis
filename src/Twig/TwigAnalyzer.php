<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\Twig;

use PhpStanTwigAnalysis\PhpStan\IncludedTemplate;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;
use Twig\Loader\LoaderInterface;
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
    public function analyze(IncludedTemplate $template, TwigAnalysis $twigAnalysis): void
    {
        $source = $this->twig->getLoader()
            ->getSourceContext($template->templateName);

        $twigAnalysis->addAnalyzedTemplate(new ResolvedTemplate($template, $source->getPath()));

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
        $twigAnalysis->addTemplatesToBeAnalyzed($collectIncludes->includedTemplates());
    }

    /**
     * @return array<string>
     */
    public function collectAllTemplateFilePaths(): array
    {
        return $this->collectTwigFilePathsFromLoader($this->twig->getLoader());
    }

    /**
     * @return array<string>>
     */
    private function collectTwigFilePathsFromLoader(LoaderInterface $loader): array
    {
        $directories = [];

        if ($loader instanceof ChainLoader) {
            foreach ($loader->getLoaders() as $subLoader) {
                $directories = array_merge($directories, $this->collectTwigFilePathsFromLoader($subLoader));
            }
        } elseif ($loader instanceof FilesystemLoader) {
            foreach ($loader->getNamespaces() as $namespace) {
                foreach ($loader->getPaths($namespace) as $path) {
                    $directories[] = $path;
                }
            }
        }

        return array_map(
            fn (SplFileInfo $fileInfo) => $fileInfo->getRealPath(),
            iterator_to_array(Finder::create()->in($directories)->name('*.twig')->files())
        );
    }
}
