<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\Twig\Rule\FilterNotFound;

use PhpStanTwigAnalysis\Twig\Rule\AbstractTwigRuleTest;

final class FilterNotFoundTest extends AbstractTwigRuleTest
{
    public function testSkipTemplateUsesExistingFilter(): void
    {
        $this->assertTwigAnalysisErrors('skip-template-uses-existing-filter.html.twig', []);
    }

    public function testTemplateUsesForbiddenFunctions(): void
    {
        $this->assertTwigAnalysisErrors(
            'template-uses-unknown-filter.html.twig',
            [['Unknown "unknownFilter" filter.', 2]]
        );
    }
}
