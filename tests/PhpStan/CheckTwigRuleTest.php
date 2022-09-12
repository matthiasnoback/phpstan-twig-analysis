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
    public function testTwigTemplateHasError(string $fixture): void
    {
        $this->analyse([$fixture], [['Error in template', 1]]);
    }

    public function testTwigTemplateIncludesAnotherTemplate(): void
    {
        $this->analyse(
            [__DIR__ . '/Fixtures/TemplateIncludesAnotherTemplate.php'],
            [['Error in template', 1], ['Error in template', 1]],
        );
    }

    public function testTwigTemplateRecursivelyIncludesAnotherTemplate(): void
    {
        $this->analyse(
            [__DIR__ . '/Fixtures/TemplateRecursivelyIncludesAnotherTemplate.php'],
            [['Error in template', 1], ['Error in template', 1]],
        );
    }

    public function testTwigTemplateExtendsAnotherTemplate(): void
    {
        $this->analyse(
            [__DIR__ . '/Fixtures/TemplateExtendsAnotherTemplate.php'],
            [['Error in template', 1], ['Error in template', 1]],
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
     * @return array<string,array{string}>
     */
    public function fixturesWithACallToRender(): array
    {
        return [
            'Basic Twig Environment' => [__DIR__ . '/Fixtures/ControllerUsesTwigRender.php'],
            'Symfony AbstractController::render()' => [__DIR__ . '/Fixtures/ControllerUsesThisRender.php'],
            'Symfony AbstractController::renderView()' => [__DIR__ . '/Fixtures/ControllerUsesThisRenderView.php'],
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
        return [__DIR__ . '/../extension_test.neon', __DIR__ . '/register_dummy_rule.neon'];
    }

    protected function getCollectors(): array
    {
        return [self::getContainer()->getByType(CollectTwigTemplateNames::class)];
    }

    protected function getRule(): CheckTwigRulesRule
    {
        return self::getContainer()->getByType(CheckTwigRulesRule::class);
    }
}
