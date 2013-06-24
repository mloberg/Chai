<?php

namespace Chai\Migrations\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Filesystem\Filesystem;

class DownCommand extends BaseCommand
{

    protected $files;

    protected function configure()
    {
        $this->setName('migration:down')
             ->setDescription('Rollback migrations')
             ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'migration'
             )
             ->addOption(
                'single',
                's',
                InputOption::VALUE_NONE,
                'run just this migration'
             )
             ->setHelp('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');

        if ($input->getOption('single')) {
            $migration = $this->app->runDown($name);
            if (!$migration->applied()) {
                $output->writeln("Rolled back migration {$name}.");
            } else {
                $msg = "<error>Error rolling back migration {$name}.</error>";
                $output->writeln($msg);
                exit(1);
            }
            return;
        }

        $migrations = array_reverse($this->app->getAll());

        if (!in_array($name, $migrations)) {
            $msg = "<error>Could not find migration {$name}</error>";
            $output->writeln($msg);
        }

        foreach ($migrations as $migrationName) {
            $migration = $this->app->resolve($migrationName);

            if (!$migration->applied()) {
                $msg  = "Migration {$migrationName} not applied. Skipping.";
                $output->writeln("<comment>{$msg}</comment>");
                if ($name === $migrationName) break;
                continue;
            }

            $migration->runDown();
            if ($migration->applied()) {
                $msg = "<error>Could not rollback migration {$migrationName}";
                $output->writeln($msg);
                exit(1);
            } else {
                $msg = "<info>Rolled back migration {$migrationName}</info>";
                $output->writeln($msg);
            }

            if ($name === $migrationName) break;
        }
    }

}
