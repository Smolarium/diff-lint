<?php
declare(strict_types = 1);
namespace Smolarium\DiffLint\Command;

class ErrorFormatter implements \PHPStan\Command\ErrorFormatter\ErrorFormatter
{
    /**
     * ErrorFormatter constructor.
     * @param ErrorFormatter\Storage $storage
     */
    public function __construct(ErrorFormatter\Storage $storage)
    {
        $this->storage = $storage;
    }

    public function formatErrors(
        \PHPStan\Command\AnalysisResult $analysisResult,
        \Symfony\Component\Console\Style\OutputStyle $style
    ): int
    {
        foreach ($analysisResult->getFileSpecificErrors() as $error) {
            $this->storage->addToStorage($error);
        }

        return 0;
    }

    /**
     * @var \Smolarium\DiffLint\Command\ErrorFormatter\Storage
     */
    private $storage;
}
