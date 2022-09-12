<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\Twig\Rule;

use PHPStan\Testing\PHPStanTestCase;
use PhpStanTwigAnalysis\PhpStan\IncludedTemplate;
use PhpStanTwigAnalysis\Twig\TwigAnalysis;
use PhpStanTwigAnalysis\Twig\TwigAnalyzer;
use PhpStanTwigAnalysis\Twig\TwigError;
use ReflectionClass;

abstract class AbstractTwigRuleTest extends PHPStanTestCase
{
    public static function getAdditionalConfigFiles(): array
    {
        $testClass = new ReflectionClass(static::class);
        $testFilename = $testClass->getFileName();
        assert(is_string($testFilename));

        $fixturesDir = dirname($testFilename) . '/Fixtures';
        $config = <<<NEON
        parameters:
            template_dir: ${fixturesDir}
        NEON;
        $configFile = sys_get_temp_dir() . '/' . $testClass->getName() . '.neon';
        file_put_contents($configFile, $config);

        $configFiles = [__DIR__ . '/../../extension_test.neon', $configFile];

        $extraConfigFile = static::getExtraConfigFilePathname();
        if ($extraConfigFile !== null) {
            $configFiles[] = $extraConfigFile;
        }

        return $configFiles;
    }

    /**
     * @param array<array{string,int}> $expectedErrors
     */
    protected function assertTwigAnalysisErrors(string $templatePathname, array $expectedErrors): void
    {
        /** @var TwigAnalyzer $twigAnalyzer */
        $twigAnalyzer = self::getContainer()->getByType(TwigAnalyzer::class);

        $template = new IncludedTemplate('unknown', 1, $templatePathname);

        $twigAnalysis = TwigAnalysis::startWith([$template]);
        $twigAnalyzer->analyze($template, $twigAnalysis);

        $expectedErrorsAsString = implode(
            "\n",
            array_map(fn (array $error): string => $error[1] . ': ' . $error[0], $expectedErrors)
        );
        $actualErrorsAsString = implode(
            "\n",
            array_map(
                fn (TwigError $twigError): string => $twigError->line() . ': ' . $twigError->error(),
                $twigAnalysis->collectedErrors()
            )
        );

        self::assertEquals($expectedErrorsAsString, $actualErrorsAsString);
    }

    protected static function getExtraConfigFilePathname(): ?string
    {
        return null;
    }
}
