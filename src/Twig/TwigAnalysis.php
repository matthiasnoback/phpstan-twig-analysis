<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\Twig;

use PhpStanTwigAnalysis\PhpStan\IncludedTemplate;

final class TwigAnalysis
{
    /**
     * @param array<string, IncludedTemplate> $analyzedTemplates
     * @param array<TwigError> $twigErrors
     * @param array<string, IncludedTemplate> $templatesToBeAnalyzed
     */
    private function __construct(
        private array $analyzedTemplates,
        private array $twigErrors,
        private array $templatesToBeAnalyzed,
    ) {
    }

    /**
     * @param array<IncludedTemplate> $templatesToBeAnalyzed
     */
    public static function startWith(array $templatesToBeAnalyzed): self
    {
        return new self([], [], $templatesToBeAnalyzed);
    }

    /**
     * @return array<TwigError>
     */
    public function collectedErrors(): array
    {
        return $this->twigErrors;
    }

    /**
     * @param array<TwigError> $moreErrors
     */
    public function addErrors(array $moreErrors): void
    {
        $this->twigErrors = array_merge($this->twigErrors, $moreErrors);
    }

    public function addError(TwigError $anotherError): void
    {
        $this->twigErrors[] = $anotherError;
    }

    /**
     * @param array<IncludedTemplate> $moreTemplates
     */
    public function addTemplatesToBeAnalyzed(array $moreTemplates): void
    {
        foreach ($moreTemplates as $template) {
            if (isset($this->analyzedTemplates[$template->templateName])) {
                continue;
            }

            if (isset($this->templatesToBeAnalyzed[$template->templateName])) {
                continue;
            }

            $this->templatesToBeAnalyzed[$template->templateName] = $template;
        }
    }

    public function nextTemplate(): ?IncludedTemplate
    {
        return array_shift($this->templatesToBeAnalyzed);
    }

    public function addAnalyzedTemplate(IncludedTemplate $template): void
    {
        $this->analyzedTemplates[$template->templateName] = $template;
    }
}
