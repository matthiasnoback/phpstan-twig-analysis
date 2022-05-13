<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\Twig\Rule\ForbiddenVariablesTwigRule;

use PhpStanTwigAnalysis\Twig\Rule\AbstractTwigRuleTest;

final class ForbiddenVariablesTwigRuleTest extends AbstractTwigRuleTest
{
    public function testSkipTemplateUsesNoForbiddenVariables(): void
    {
        $this->assertTwigAnalysisErrors('skip-template-uses-no-forbidden-variables.html.twig', []);
    }

    public function testTemplateUsesForbiddenVariables(): void
    {
        $this->assertTwigAnalysisErrors(
            'template-uses-forbidden-variables.html.twig',
            [['Forbidden variable used: app', 2], ['Forbidden variable used: app', 5]]
        );
    }

    protected static function getExtraConfigFilePathname(): string
    {
        return __DIR__ . '/phpstan.neon';
    }
}
