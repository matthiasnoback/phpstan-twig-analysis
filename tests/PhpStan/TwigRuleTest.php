<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\PhpStan;

use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<CheckTwigRulesRule>
 */
final class TwigRuleTest extends RuleTestCase
{
    public function testTwigTemplateUsesForbiddenFunction(): void
    {
        $this->analyse(
            [__DIR__ . '/Fixtures/twig-template-uses-forbidden-function.php'],
            [['Forbidden function used: dump, in tests/PhpStan/Fixtures/uses-forbidden-function.html.twig:1', 13]]
        );
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

    public function testTemplateIsOkay(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/skip-template-is-okay.php'], []);
    }

    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/../../extension.neon', __DIR__ . '/phpstan.neon'];
    }

    protected function getRule(): CheckTwigRulesRule
    {
        return self::getContainer()->getByType(CheckTwigRulesRule::class);
    }
}
