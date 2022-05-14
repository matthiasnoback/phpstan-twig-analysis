<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\Twig\Rule\LawOfDemeterTwigRule;

use PhpStanTwigAnalysis\Twig\Rule\AbstractTwigRuleTest;

final class LawOfDemeterTwigRuleTest extends AbstractTwigRuleTest
{
    public function testSkipTemplateUsesConstantInclude(): void
    {
        $this->assertTwigAnalysisErrors('skip-template-uses-allowed-number-of-dots.html.twig', []);
    }

    public function testTemplateUsesDynamicInclude(): void
    {
        $this->assertTwigAnalysisErrors(
            'template-uses-more-than-the-allowed-number-of-dots.html.twig',
            [
                ['Template uses more than the allowed number of dots (2)', 1],
                ['Template uses more than the allowed number of dots (2)', 2],
            ]
        );
    }

    protected static function getExtraConfigFilePathname(): string
    {
        return __DIR__ . '/phpstan.neon';
    }
}
