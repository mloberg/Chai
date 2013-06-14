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

    protected $files;

    protected function configure()
    {
        $this->setName('migrate:init')
             ->setDescription('Setup database for migrations')
             ->setHelp('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $schema = $this->app->schema();
        if (!$schema->hasTable('migrations')) {
            $output->writeln("Creating migrations table");
            $schema->create('migrations', function($table) {
                $table->date('date')->unique();
                $table->string('name');
                $table->boolean('applied');
                $table->timestamp('ran_at');
            });
        }
        $path = $this->app->getMigrationsPath();
        if (!$this->getFilesystem()->isDirectory($path)) {
            $output->writeln('Attempting to create migrations directory');
            if (!$this->getFilesystem()->makeDirectory($path, 0664)) {
                $output->writeln('<error>Could not create migrations directory</error>');
                exit(1);
            }
        }
        $output->writeln('<info>You are now ready to run migrations.</info>');
    }

    public function setFilesystem($files)
    {
        $this->files = $files;
    }

    public function getFilesystem()
    {
        if (!$this->files) {
            $this->files = new Filesystem;
        }
        return $this->files;
    }

}
