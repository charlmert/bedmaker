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
          ->addArgument('src', InputArgument::OPTIONAL, 'The directory to search for php files. You can also specify a single php file.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //$config = require_once(dirname(__FILE__) . '/../../../config.php');

        $io = new SymfonyStyle($input, $output);
        $io->title('Bedmaker: Apply Code Style');

        $tokenizer = new Tokenizer();
        if (is_dir($input->getArgument('src'))) {
            $io->writeln("Directory: " . $input->getArgument('src'));

            foreach (new \DirectoryIterator($input->getArgument('src')) as $fileInfo) {
                if ($fileInfo->isDot()) {
                    continue;
                }

                $io->writeln('Fixing file: ' . $fileInfo->getFilename());
                $tokenizer->load(file_get_contents($fileInfo->getPathname()));
                //$tokenizer->runSelected($config);
                $fileContents = $tokenizer->runAll();
                file_put_contents($fileInfo->getPathname(), $fileContents);
            }

            foreach (new \DirectoryIterator($input->getArgument('src')) as $fileInfo) {
                if ($fileInfo->isDot()) {
                    continue;
                }

                $io->writeln('Fixing file after: ' . $fileInfo->getFilename());
                $tokenizer->load(file_get_contents($fileInfo->getPathname()));
                //$tokenizer->runSelected($config);
                $fileContents = $tokenizer->runAfter();
                file_put_contents($fileInfo->getPathname(), $fileContents);
            }
        } else {
            $filename = basename($input->getArgument('src'));
            $io->writeln('Fixing file: ' . $filename);
            $tokenizer->load(file_get_contents($input->getArgument('src')));
            //$tokenizer->runSelected($config);
            $fileContents = $tokenizer->runAll();
            file_put_contents($fileInfo->getPathname(), $fileContents);

            $io->writeln('Fixing file after: ' . $filename);
            $tokenizer->load(file_get_contents($input->getArgument('src')));
            //$tokenizer->runSelected($config);
            $fileContents = $tokenizer->runAfter();
            file_put_contents($input->getArgument('src'), $fileContents);
        }
        $io->writeln('All files fixed');
    }
}
