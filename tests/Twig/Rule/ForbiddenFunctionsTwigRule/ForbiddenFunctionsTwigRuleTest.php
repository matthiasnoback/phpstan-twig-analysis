<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\Twig\Rule\ForbiddenFunctionsTwigRule;

use PhpStanTwigAnalysis\Twig\Rule\AbstractTwigRuleTest;

final class ForbiddenFunctionsTwigRuleTest extends AbstractTwigRuleTest
{
    public function testSkipTemplateUsesNoForbiddenFunctions(): void
    {
        $this->assertTwigAnalysisErrors('skip-template-uses-no-forbidden-functions.html.twig', []);
    }

    public function testTemplateUsesForbiddenFunctions(): void
    {
        $this->assertTwigAnalysisErrors(
            'template-uses-forbidden-functions.html.twig',
            [['Forbidden function used: dump', 2], ['Forbidden function used: dump', 5]]
        );
    }

    protected static function getExtraConfigFilePathname(): string
    {
        return __DIR__ . '/phpstan.neon';
    }
}
