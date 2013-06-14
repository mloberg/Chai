<?php

namespace Chai\Migrations\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Filesystem\Filesystem;
use Chai\Migrations\Creator;
use Chai\Migrations\MigrationException;

class CreateCommand extends BaseCommand
{

    /**
     * Migration creator instance
     *
     * @var \Chai\Migrations\Creator
     */
    protected $creator;

    protected function configure()
    {
        $this->setName('migrate:create')
             ->setDescription('Create a new migration')
             ->addArgument('name', InputArgument::REQUIRED, 'Name of the migration')
             ->setHelp('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $creator = $this->getCreator();
        $path = $this->app->getMigrationsPath();
        $migration = $creator->create($input->getArgument('name'), $path);
        $output->writeln("<info>Created {$migration}</info>");
    }

    /**
     * Set the migration creator.
     *
     * @param \Chai\Migrations\Creator $creator
     */
    public function setCreator(Creator $creator)
    {
        $this->creator = $creator;
    }

    /**
     * Get the migration creator.
     *
     * Initialize creator if not set.
     *
     * @return \Chai\Migrations\Creator
     */
    public function getCreator()
    {
        if (!$this->creator) {
            $this->creator = new Creator($this->app->getFilesystem());
        }
        return $this->creator;
    }

}
