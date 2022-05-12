<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\Twig\Rule\FilterNotFoundRule;

use PhpStanTwigAnalysis\Twig\Rule\AbstractTwigRuleTest;

final class FilterNotFoundRuleTest extends AbstractTwigRuleTest
{
    public function testSkipTemplateUsesExistingFilter(): void
    {
        $this->assertTwigAnalysisErrors('skip-template-uses-existing-filter.html.twig', []);
    }

    public function testTemplateUsesForbiddenFunctions(): void
    {
        $this->assertTwigAnalysisErrors(
            'template-uses-unknown-filter.html.twig',
            [['Unknown Twig filter: unknownFilter', 2], ['Unknown Twig filter: anotherUnknownFilter', 5]]
        );
    }

    protected static function getExtraConfigFilePathname(): ?string
    {
        return __DIR__ . '/phpstan.neon';
    }
}
