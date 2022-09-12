<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\Twig\Rule\ForbiddenVariablesTwigRule;

use PhpStanTwigAnalysis\Twig\Rule\AbstractTwigRuleTest;

final class ForbiddenVariablesTwigRuleTest extends AbstractTwigRuleTest
{
    public function testSkip1(): void
    {
        $this->assertTwigAnalysisErrors('skip-template-uses-no-forbidden-variables.html.twig', []);
    }

    public function testSkip2(): void
    {
        $this->assertTwigAnalysisErrors('skip-template-accesses-subvalue-with-a-forbidden-name.html.twig', []);
    }

    public function testError1(): void
    {
        $this->assertTwigAnalysisErrors(
            'template-uses-forbidden-variable.html.twig',
            [['Forbidden variable used: app', 2]]
        );
    }

    public function testError2(): void
    {
        $this->assertTwigAnalysisErrors(
            'template-uses-subvalue-of-forbidden-variable.html.twig',
            [['Forbidden variable used: app', 2]]
        );
    }

    protected static function getExtraConfigFilePathname(): string
    {
        return __DIR__ . '/phpstan.neon';
    }
}
