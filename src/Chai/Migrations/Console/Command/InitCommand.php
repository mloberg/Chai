<?php
/**
 * Setup the database for migrations.
 * This means creating the *migrations* table and checking the
 * migrations directory exists.
 */

namespace Chai\Migrations\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Filesystem\Filesystem;

class InitCommand extends BaseCommand
{

    protected function configure()
    {
        $this->setName('migrate:init')
             ->setDescription('Setup database for migrations')
             ->setHelp('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->app->setup();
        $output->writeln('<info>You are now ready to run migrations.</info>');
    }

}
