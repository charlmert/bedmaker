<?php

namespace BedMaker\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use BedMaker\Code\Tokenizer;
use BedMaker\Sql\SqlPhpTokenizer;

class SqlToElloquentCommand extends Command
{
    protected static $defaultName = 'sql';

    protected function configure()
    {
        $this
          ->setDescription('Converts sql statements in file(s) to elloquent')
          ->setHelp('Makes use of existing table models if you have any defined')
          ->addArgument('src', InputArgument::REQUIRED, 'The directory to search for php files which contain sql strings. You can also specify a single php file.')
          ->addArgument('model-src', InputArgument::OPTIONAL, 'The directory to search for php files. You can also specify a single php file.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //$config = require_once(dirname(__FILE__) . '/../../../config.php');

        $io = new SymfonyStyle($input, $output);
        $io->title('Bedmaker: Convert SQL to Laravel Elloquent');

        $sqlPhpTokenizer = new SqlPhpTokenizer();
        if (is_dir($input->getArgument('src'))) {
            $io->writeln("Directory: " . $input->getArgument('src'));

            foreach (new \DirectoryIterator($input->getArgument('src')) as $fileInfo) {
                if ($fileInfo->isDot()) {
                    continue;
                }

                $io->writeln('Fixing file: ' . $fileInfo->getFilename());
                $sqlPhpTokenizer->load(file_get_contents($fileInfo->getPathname()));
                //$tokenizer->runSelected($config);
                $fileContents = $sqlPhpTokenizer->process();
                file_put_contents($fileInfo->getPathname(), $fileContents);
            }
        } else {
            $filename = basename($input->getArgument('src'));
            $io->writeln('Fixing file: ' . $filename);
            $sqlPhpTokenizer->load(file_get_contents($input->getArgument('src')));
            //$tokenizer->runSelected($config);
            $fileContents = $sqlPhpTokenizer->process();
            file_put_contents($input->getArgument('src'), $fileContents);
        }
        $io->writeln('All files fixed');
    }
}
