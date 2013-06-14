<?php

namespace Chai\Migrations\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Chai\Migrations\Console\Application;

abstract class BaseCommand extends Command
{

    /**
     * @var Chai\Migrations\Console\Application
     */
    protected $app;

    public function __construct(Application $app, $name = null)
    {
        parent::__construct($name);
        $this->app = $app;
    }

}
