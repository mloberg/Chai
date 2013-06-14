<?php

namespace Chai\Migrations\Console;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Filesystem\Filesystem;
use Chai\Console\Component;
use Chai\Migrations\Console\Command\InitCommand;
use Chai\Migrations\Console\Command\CreateCommand;
use Chai\Migrations\Console\Command\StatusCommand;

class Application extends Component
{

    protected $migrationsPath = './migrations';
    protected $capsule;

    public function __construct($database = array(), $path = null)
    {
        $this->capsule = new Capsule;
        if ($database) {
            $this->setDatabaseParameters($database);
        }
        if ($path) {
            $this->setMigrationsPath($path);
        }
    }

    public function db()
    {
        return $this->capsule->getConnection('migrations');
    }

    public function schema()
    {
        return $this->db()->getSchemaBuilder();
    }

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

    public function setDatabaseParameters($parameters = array())
    {
        $defaults = array(
            'driver'    => 'mysql',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
        );
        $this->capsule->addConnection($parameters + $defaults, 'migrations');
    }

    public function setMigrationsPath($path)
    {
        $this->migrationsPath = $path;
    }

    public function getMigrationsPath()
    {
        return $this->migrationsPath;
    }

}
