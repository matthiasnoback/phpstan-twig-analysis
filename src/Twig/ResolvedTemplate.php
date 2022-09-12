<?php
declare(strict_types=1);

namespace PhpStanTwigAnalysis\Twig;

use PhpStanTwigAnalysis\PhpStan\IncludedTemplate;

final class ResolvedTemplate
{
    public function __construct(public IncludedTemplate $includedTemplate, public string $resolvedFilePath)
    {
    }
}
