<?php

namespace Chai\Migrations;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Filesystem\Filesystem;

class Migrations
{

    protected $files;
    protected $migrationsPath = './migrations';
    protected $capsule;

    public function __construct($path = null, $database = array())
    {
        $this->capsule = new Capsule;
        if ($path) {
            $this->setMigrationsPath($path);
        }
        if ($database) {
            $this->setDatabaseParameters($database);
        }
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

    public function db()
    {
        return $this->capsule->getConnection('migrations');
    }

    public function schema()
    {
        return $this->db()->getSchemaBuilder();
    }

    public function setMigrationsPath($path)
    {
        $this->migrationsPath = $path;
    }

    public function getMigrationsPath()
    {
        return $this->migrationsPath;
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
