<?php

namespace Chai\Migrations\Console;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Filesystem\Filesystem;
use Chai\Console\Component;
use Chai\Migrations\Migrations;
use Chai\Migrations\Console\Command\InitCommand;
use Chai\Migrations\Console\Command\CreateCommand;
use Chai\Migrations\Console\Command\StatusCommand;

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
            new StatusCommand($this),
        );
    }

}
