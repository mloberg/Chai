<?php

namespace Chai\Migrations\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Filesystem\Filesystem;
use Chai\Migrations\Creator;
use Chai\Migrations\MigrationException;

class ListCommand extends BaseCommand
{

    protected function configure()
    {
        $this->setName('migration:list')
             ->setDescription('List migrations.')
             ->setHelp('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $migrations = $this->app->getAll();
        if ($migrations) {
            foreach ($migrations as $migration) {
                $output->writeln("* {$migration}");
            }
        } else {
            $output->writeln('No migrations found');
        }
    }

}
