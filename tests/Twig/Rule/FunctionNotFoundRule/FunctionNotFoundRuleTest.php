<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\Twig\Rule\FunctionNotFoundRule;

use PhpStanTwigAnalysis\Twig\Rule\AbstractTwigRuleTest;

final class FunctionNotFoundRuleTest extends AbstractTwigRuleTest
{
    public function testSkipTemplateUsesExistingFunction(): void
    {
        $this->assertTwigAnalysisErrors('skip-template-uses-existing-function.html.twig', []);
    }

    public function testTemplateUsesUnknownFunction(): void
    {
        $this->assertTwigAnalysisErrors(
            'template-uses-unknown-function.html.twig',
            [
                ['Call to unknown Twig function: unknownFunction()', 2],
                ['Call to unknown Twig function: anotherUnknownFunction()', 5],
            ]
        );
    }

    protected static function getExtraConfigFilePathname(): ?string
    {
        return __DIR__ . '/phpstan.neon';
    }
}
