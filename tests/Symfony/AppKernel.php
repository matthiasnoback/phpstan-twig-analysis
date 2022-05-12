<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\Symfony;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel;

final class AppKernel extends Kernel
{
    use MicroKernelTrait;

    public function __construct(private string $cacheDir)
    {
        parent::__construct('dev', true);
    }

    public function getCacheDir(): string
    {
        return $this->cacheDir;
    }
}
