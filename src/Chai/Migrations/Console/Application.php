<?php

namespace Chai\Migrations\Console;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Filesystem\Filesystem;
use Chai\Console\Component;
use Chai\Migrations\Migrations;
use Chai\Migrations\Console\Command\InitCommand;
use Chai\Migrations\Console\Command\CreateCommand;
use Chai\Migrations\Console\Command\ListCommand;
use Chai\Migrations\Console\Command\StatusCommand;
use Chai\Migrations\Console\Command\UpCommand;
use Chai\Migrations\Console\Command\DownCommand;
use Chai\Migrations\Console\Command\LatestCommand;

class Application extends Migrations implements Component
{

    /**
     * Return commands for console application.
     *
     * @return array console commands
     */
    public function getCommands()
    {
        return array(
            new InitCommand($this),
            new CreateCommand($this),
            new ListCommand($this),
            new StatusCommand($this),
            new UpCommand($this),
            new DownCommand($this),
            new LatestCommand($this),
        );
    }

}
