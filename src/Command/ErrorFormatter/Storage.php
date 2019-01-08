<?php
declare(strict_types = 1);
namespace Smolarium\DiffLint\Command\ErrorFormatter;

class Storage
{
    public function addToStorage(\PHPStan\Analyser\Error $error) {
        if (!\array_key_exists($error->getFile(), $this->storage)) {
            $this->storage[$error->getFile()] = 0;
        }

        $this->storage[$error->getFile()]++;
    }

    public function countErrors() : int
    {
        $counter = 0;
        foreach ($this->storage as $perFile) {
            $counter += $perFile;
        }

        return $counter;
    }

    /**
     * @var int[]
     */
    private $storage = [];
}