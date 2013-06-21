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

    /**
     * Create the migrations table and ensure the migrations directory exists.
     *
     * @return void
     */
    public function setup()
    {
        if (!$this->schema()->hasTable('migrations')) {
            $this->schema()->create('migrations', function($table) {
                $table->dateTime('id')->uniqe();
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

    /**
     * Get all migrations from file system.
     *
     * @return array migrations
     */
    public function getAll()
    {
        $path = $this->getMigrationsPath();
        $files = $this->getFilesystem()->glob($path.'/*_*.php');
        if ($files === false) {
            return array();
        }
        $files = array_map(function($file) {
            return str_replace('.php', '', basename($file));
        }, $files);
        sort($files);
        return $files;
    }

     * Return a new instance of a migration.
     *
     * @param  string $file Migration file (without extension or directory)
     * @return class        Migration
     */
    public function resolve($file)
    {
        $path = $this->getMigrationsPath();
        $this->getFilesystem()->requireOnce($path.'/'.$file.'.php');
        $file = implode('_', array_slice(explode('_', $file), 4));
        $class = studly_case($file);
        return new $class($this->db());
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
