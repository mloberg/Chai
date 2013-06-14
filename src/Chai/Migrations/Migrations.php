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

    public function setup()
    {
        if (!$this->schema()->hasTable('migrations')) {
            $this->schema()->create('migrations', function($table) {
                $table->date('date')->uniqe();
                $table->string('name');
                $table->boolean('applied');
                $table->timestamp('ran_at');
            });
        }
        $files = $this->getFilesystem();
        $path = $this->getMigrationsPath();
        if (!$files->isDirectory($path)) {
            if (!$files->makeDirectory($path, 0664)) {
                throw new MigrationsException('Could not create migrations directory');
            }
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
