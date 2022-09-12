<?php
declare(strict_types=1);

namespace PhpStanTwigAnalysis\PhpStan\UnusedTemplatesTest;

use PHPStan\Testing\RuleTestCase;
use PhpStanTwigAnalysis\PhpStan\CheckTwigRulesRule;
use PhpStanTwigAnalysis\PhpStan\CollectTwigTemplateNames;

/**
 * @extends RuleTestCase<CheckTwigRulesRule>
 */
final class UnusedTemplatesTest extends RuleTestCase
{
    public function testTwigTemplateIsUnused(): void
    {
        $this->analyse(
            [__DIR__ . '/Templates/TemplateIncludesAnotherTemplate.php'],
            [['Template is unused', 1]],
        );
    }

    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/../../extension_test.neon', __DIR__ . '/report_unused_templates.neon'];
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
