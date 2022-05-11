<?php

declare(strict_types=1);

use PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\AssignmentInConditionSniff;
use PhpCsFixer\Fixer\Phpdoc\GeneralPhpdocAnnotationRemoveFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitStrictFixer;
use Symplify\CodingStandard\Fixer\Naming\StandardizeHereNowDocKeywordFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->paths([__DIR__ . '/src', __DIR__ . '/tests', __DIR__ . '/ecs.php']);

    $ecsConfig->sets([SetList::CONTROL_STRUCTURES, SetList::PSR_12, SetList::COMMON, SetList::SYMPLIFY]);

    $ecsConfig->skip([
        PhpUnitStrictFixer::class,
        // Because it makes no sense ;) (well, I just need assertEquals())
        // fixture files
        'tests/PhpStan/Fixtures',

        // allow @throws
        GeneralPhpdocAnnotationRemoveFixer::class,
        // We don't want all here/now docs to become CODESAMPLE block
        StandardizeHereNowDocKeywordFixer::class,
        // Allow assignment in while at least
        AssignmentInConditionSniff::class,
    ]);
};
