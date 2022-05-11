<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\Twig\Rule\NoDynamicIncludesRule;

use PhpStanTwigAnalysis\Twig\Rule\AbstractTwigRuleTest;

final class NoDynamicIncludesRuleTest extends AbstractTwigRuleTest
{
    public function testSkipTemplateUsesConstantInclude(): void
    {
        $this->assertTwigAnalysisErrors('skip-template-uses-constant-include.html.twig', []);
    }

    public function testTemplateUsesDynamicInclude(): void
    {
        $this->assertTwigAnalysisErrors(
            'template-uses-dynamic-include.html.twig',
            [['Template uses dynamic include', 1]]
        );
    }

    protected static function getExtraConfigFilePathname(): string
    {
        return __DIR__ . '/phpstan.neon';
    }
}
