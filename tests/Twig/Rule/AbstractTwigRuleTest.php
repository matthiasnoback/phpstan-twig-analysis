<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\Twig\Rule;

use PHPStan\Testing\PHPStanTestCase;
use PhpStanTwigAnalysis\PhpStan\TwigFactoryForTesting;
use PhpStanTwigAnalysis\Twig\TwigAnalyzer;
use PhpStanTwigAnalysis\Twig\TwigError;

abstract class AbstractTwigRuleTest extends PHPStanTestCase
{
    public static function getAdditionalConfigFiles(): array
    {
        TwigFactoryForTesting::setTemplateDir(static::getTemplateDirname());

        return [
            __DIR__ . '/../../../extension.neon',
            __DIR__ . '/../../extension_test.neon',
            static::getConfigFilePathname(),
        ];
    }

    /**
     * @param array<array{string,int}> $expectedErrors
     */
    protected function assertTwigAnalysisErrors(string $templatePathname, array $expectedErrors): void
    {
        /** @var TwigAnalyzer $twigAnalyzer */
        $twigAnalyzer = self::getContainer()->getByType(TwigAnalyzer::class);

        $actualErrors = $twigAnalyzer->analyze($templatePathname);

        $expectedErrorsAsString = implode(
            "\n",
            array_map(fn (array $error): string => $error[1] . ': ' . $error[0], $expectedErrors)
        );
        $actualErrorsAsString = implode(
            "\n",
            array_map(
                fn (TwigError $twigError): string => $twigError->line() . ': ' . $twigError->error(),
                $actualErrors
            )
        );

        self::assertEquals($expectedErrorsAsString, $actualErrorsAsString);
    }

    abstract protected static function getTemplateDirname(): string;

    abstract protected static function getConfigFilePathname(): string;
}
