<?php

namespace BedMaker\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use BedMaker\Code\Tokenizer;

class BedMakerCommand extends Command
{
    protected static $defaultName = 'run';

    protected function configure()
    {
        $this
          ->setDescription('Applies Quality and Styling to PHP Code')
          ->setHelp('This command helps you to apply quality and styling to your code')
          ->addArgument('dir', InputArgument::OPTIONAL, 'The directory to search for php files.')
          ->addArgument('file', InputArgument::OPTIONAL, 'You can also specify a single php file.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //$config = require_once(dirname(__FILE__) . '/../../../config.php');

        $io = new SymfonyStyle($input, $output);
        $io->title('Bedmaker: Apply Code Style');

        $io->writeln("Directory: " . $input->getArgument('dir'));
        foreach (new \DirectoryIterator($input->getArgument('dir')) as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }

            $io->writeln('Fixing file: ' . $fileInfo->getFilename());
            $tokenizer = new Tokenizer(file_get_contents($fileInfo->getPathname()));
            //$tokenizer->runSelected($config);
            $fileContents = $tokenizer->runAll();
            file_put_contents($fileInfo->getPathname(), $fileContents);
            exit(0);
        }
    }
}
