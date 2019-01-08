<?php
declare(strict_types = 1);
namespace Smolarium\DiffLint\Command;

class ErrorFormatter implements \PHPStan\Command\ErrorFormatter\ErrorFormatter
{
    public function formatErrors(
        \PHPStan\Command\AnalysisResult $analysisResult,
        \Symfony\Component\Console\Style\OutputStyle $style
    ): int
    {
        foreach ($analysisResult->getFileSpecificErrors() as $error) {
            /**
             * @todo there must be usage of some sort of storage for errors
             */
        }

        return 0;
    }
}
