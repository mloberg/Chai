<?php

namespace Chai\Migrations\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Filesystem\Filesystem;

class StatusCommand extends BaseCommand
{

    protected $files;

    protected function configure()
    {
        $this->setName('migration:status')
             ->setDescription('See migration status')
             ->setHelp('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $migrations = $this->app->getAll();
        if ($migrations) {
            foreach ($migrations as $migrationName) {
                $out = "* {$migrationName}";
                $migration = $this->app->resolve($migrationName);
                if ($migration->applied()) {
                    $out = "<info>{$out} (applied)</info>";
                }
                $output->writeln($out);
            }
        } else {
            $output->writeln('No migrations found');
        }
    }

}
