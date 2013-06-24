<?php

namespace Chai\Migrations\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Filesystem\Filesystem;

class LatestCommand extends BaseCommand
{

    protected $files;

    protected function configure()
    {
        $this->setName('migration:latest')
             ->setDescription('Run all migrations')
             ->setHelp('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $migrations = $this->app->getAll();

        foreach ($migrations as $migrationName) {
            $migration = $this->app->resolve($migrationName);

            if ($migration->applied()) {
                $msg = "Migration {$migrationName} already applied. Skipping.";
                $output->writeln("<comment>{$msg}</comment>");
                continue;
            }

            $migration->runUp();
            if (!$migration->applied()) {
                $msg = "<error>Could not apply migration {$migrationName}";
                $output->writeln($msg);
                exit(1);
            } else {
                $msg = "<info>Ran migration {$migrationName}</info>";
                $output->writeln($msg);
            }
        }
    }

}
