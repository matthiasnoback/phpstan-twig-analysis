<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\EarlyReturn\Rector\If_\ChangeAndIfToEarlyReturnRector;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\ClassMethod\AddArrayReturnDocTypeRector;

return static function (
    RectorConfig $config
): void {
    $config->import(SetList::DEAD_CODE);
    $config->import(SetList::TYPE_DECLARATION_STRICT);
    $config->import(SetList::TYPE_DECLARATION);
    $config->import(SetList::PHP_80);
    $config->import(SetList::PHP_74);
    $config->import(SetList::PHP_73);
    $config->import(SetList::EARLY_RETURN);

    $config->paths(
        [
            __DIR__ . '/src',
            __DIR__ . '/tests',
            __DIR__ . '/ecs.php',
            __DIR__ . '/rector.php',
        ]
    );

    $config->skip(
        [
            AddArrayReturnDocTypeRector::class,
            ChangeAndIfToEarlyReturnRector::class,
            __DIR__ . 'tests/PhpStan/Fixtures',
        ]
    );

    $config->importNames();
};
