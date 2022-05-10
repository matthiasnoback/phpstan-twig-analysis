<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\PhpStan;

use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<CheckTwigRulesRule>
 */
final class CheckTwigRuleTest extends RuleTestCase
{
    /**
     * @dataProvider fixturesWithACallToRender
     */
    public function testTwigTemplateHasError(string $fixture, int $lineNumber): void
    {
        $this->analyse(
            [$fixture],
            [['Error in template, in tests/PhpStan/Fixtures/template.html.twig:1', $lineNumber]]
        );
    }

    public function testTwigTemplateNotFound(): void
    {
        $this->analyse(
            [__DIR__ . '/Fixtures/TemplateNotFound.php'],
            [
                [
                    'Unable to find template "non-existing-template.html.twig" (looked into: tests/PhpStan/Fixtures).',
                    15,
                ],
            ]
        );
    }

    /**
     * @return array<string,array{string,int}>
     */
    public function fixturesWithACallToRender(): array
    {
        return [
            'Basic Twig Environment' => [__DIR__ . '/Fixtures/ControllerUsesTwigRender.php', 15],
            'Symfony AbstractController::render()' => [__DIR__ . '/Fixtures/ControllerUsesThisRender.php', 14],
            'Symfony AbstractController::renderView()' => [__DIR__ . '/Fixtures/ControllerUsesThisRenderView.php', 14],
        ];
    }

    public function testSkipNotACallToTwigEnvironment(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/skip-not-a-call-to-twig-environment.php'], []);
    }

    public function testSkipNotACallToRender(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/skip-not-a-call-to-render.php'], []);
    }

    public function testSkipNotAConstantString(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/skip-not-a-constant-string.php'], []);
    }

    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/../extension_test.neon', __DIR__ . '/phpstan.neon'];
    }

    protected function getRule(): CheckTwigRulesRule
    {
        return self::getContainer()->getByType(CheckTwigRulesRule::class);
    }
}
