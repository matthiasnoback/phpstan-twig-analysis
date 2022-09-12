<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\PhpStan;

final class IncludedTemplate
{
    public function __construct(
        public IncludedFrom $includedFrom,
        public string $templateName,
    ) {
    }
}
