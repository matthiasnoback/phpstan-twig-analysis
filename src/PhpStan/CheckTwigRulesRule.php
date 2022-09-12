<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\PhpStan;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PhpStanTwigAnalysis\Twig\ResolvedTemplate;
use PhpStanTwigAnalysis\Twig\TwigAnalysis;
use PhpStanTwigAnalysis\Twig\TwigAnalyzer;
use PhpStanTwigAnalysis\Twig\TwigError;
use Twig\Error\LoaderError;

/**
 * @implements Rule<CollectedDataNode>
 */
final class CheckTwigRulesRule implements Rule
{
    public function __construct(
        private TwigAnalyzer $twigAnalyzer,
        private bool $reportUnusedTemplates,
    ) {
    }

    public function getNodeType(): string
    {
        return CollectedDataNode::class;
    }

    /**
     * @param CollectedDataNode $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        /** @var array<string, array<array{string,int}>> $allData */
        $allData = $node->get(CollectTwigTemplateNames::class);

        $templates = [];
        foreach ($allData as $file => $collectedData) {
            foreach ($collectedData as $renderCall) {
                $templates[] = new IncludedTemplate(new IncludedFrom($file, $renderCall[1]), $renderCall[0]);
            }
        }

        $twigAnalysis = TwigAnalysis::startWith($templates);

        $errors = $this->keepAnalyzingUntilDone($twigAnalysis);

        if ($this->reportUnusedTemplates) {
            foreach ($this->collectUnusedTemplates($twigAnalysis) as $templateFile) {
                $errors[] = RuleErrorBuilder::message('Template is unused')->file($templateFile)->line(1)->build();
            }
        }

        return $errors;
    }

    /**
     * @return array<RuleError>
     */
    private function keepAnalyzingUntilDone(TwigAnalysis $twigAnalysis): array
    {
        while ($template = $twigAnalysis->nextTemplate()) {
            try {
                $this->twigAnalyzer->analyze($template, $twigAnalysis);
            } catch (LoaderError $loaderError) {
                return [RuleErrorBuilder::message($loaderError->getMessage())
                    ->file($template->includedFrom->file())
                    ->line($template->includedFrom->line())
                    ->build(), ];
            }
        }

        return array_map(
            fn (TwigError $twigError): RuleError => $twigError->asPhpStanError(),
            $twigAnalysis->collectedErrors()
        );
    }

    /**
     * @return array<string>
     */
    private function collectUnusedTemplates(TwigAnalysis $analysis): array
    {
        $usedTemplatePaths = array_map(
            fn (ResolvedTemplate $template) => $template->resolvedFilePath,
            $analysis->analyzedTemplates()
        );

        $allTemplateFiles = $this->twigAnalyzer->collectAllTemplateFilePaths();

        return array_diff($allTemplateFiles, $usedTemplatePaths);
    }
}
