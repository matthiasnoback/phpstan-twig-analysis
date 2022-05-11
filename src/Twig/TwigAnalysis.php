<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\Twig;

final class TwigAnalysis
{
    /**
     * @param array<string> $analyzedTemplates
     * @param array<TwigError> $twigErrors
     * @param array<string> $templatesToBeAnalyzed
     */
    private function __construct(
        private array $analyzedTemplates,
        private array $twigErrors,
        private array $templatesToBeAnalyzed,
    ) {
    }

    public static function startWith(string $template): self
    {
        return new self([], [], [$template]);
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
     * @param array<string> $moreTemplates
     */
    public function addTemplatesToBeAnalyzed(array $moreTemplates): void
    {
        foreach ($moreTemplates as $template) {
            if (in_array($template, $this->analyzedTemplates, true)) {
                continue;
            }

            $this->templatesToBeAnalyzed[] = $template;
        }
    }

    public function nextTemplate(): string|null
    {
        return array_shift($this->templatesToBeAnalyzed);
    }

    public function addAnalyzedTemplate(string $templateName): void
    {
        $this->analyzedTemplates[] = $templateName;
    }
}
