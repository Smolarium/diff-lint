<?php
declare(strict_types = 1);
namespace Smolarium\DiffLint\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Analyse extends \PHPStan\Command\AnalyseCommand
{
    private const NAME = 'analyse';

    protected function configure(): void
    {
        $this->setName(self::NAME)
            ->setDescription('Analyses source code')
            ->setDefinition([
                new InputArgument('paths', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'Paths with source code to run analysis on'),
                new InputOption('paths-file', null, InputOption::VALUE_REQUIRED, 'Path to a file with a list of paths to run analysis on'),
                new InputOption('configuration', 'c', InputOption::VALUE_REQUIRED, 'Path to project configuration file'),
                new InputOption(self::OPTION_LEVEL, 'l', InputOption::VALUE_REQUIRED, 'Level of rule options - the higher the stricter'),
                new InputOption('debug', null, InputOption::VALUE_NONE, 'Show debug information - which file is analysed, do not catch internal errors'),
                new InputOption('autoload-file', 'a', InputOption::VALUE_REQUIRED, 'Project\'s additional autoload file path'),
                new InputOption('memory-limit', null, InputOption::VALUE_REQUIRED, 'Memory limit for analysis'),
            ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $paths = $input->getArgument('paths');
        $memoryLimit = $input->getOption('memory-limit');
        $autoloadFile = $input->getOption('autoload-file');
        $configuration = $input->getOption('configuration');
        $level = $input->getOption(self::OPTION_LEVEL);
        $pathsFile = $input->getOption('paths-file');

        if (
            !is_array($paths)
            || (!is_string($memoryLimit) && $memoryLimit !== null)
            || (!is_string($autoloadFile) && $autoloadFile !== null)
            || (!is_string($configuration) && $configuration !== null)
            || (!is_string($level) && $level !== null)
            || (!is_string($pathsFile) && $pathsFile !== null)
        ) {
            throw new \PHPStan\ShouldNotHappenException();
        }

        try {
            $inceptionResult = \PHPStan\Command\CommandHelper::begin(
                $input,
                $output,
                $paths,
                $pathsFile,
                $memoryLimit,
                $autoloadFile,
                $configuration,
                $level
            );
        } catch (\PHPStan\Command\InceptionNotSuccessfulException $e) {
            return 1;
        }

        $container = $inceptionResult->getContainer();

        /** @var \PHPStan\Command\AnalyseApplication  $application */
        $application = $container->getByType(\PHPStan\Command\AnalyseApplication::class);

        $debug = $input->getOption('debug');
        if (!is_bool($debug)) {
            throw new \PHPStan\ShouldNotHappenException();
        }

        return $inceptionResult->handleReturn(
            $application->analyse(
                $inceptionResult->getFiles(),
                true,
                new \PHPStan\Command\ErrorsConsoleStyle($input, $output),
                new ErrorFormatter(),
                $inceptionResult->isDefaultLevelUsed(),
                $debug
            )
        );
    }
}
