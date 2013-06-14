<?php

namespace Chai\Migrations;

use Illuminate\Database\Capsule\Manager as Capsule;

abstract class BaseMigration
{

    protected $capsule;

    public function __construct($database = array())
    {
        $this->capsule = new Capsule;
        if ($database) {
            $this->setDatabaseParameters($database);
        }
    }

    abstract public function up();
    abstract public function down();

    public function setDatabaseParameters($parameters)
    {
        $defaults = array(
            'driver'    => 'mysql',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
        );
        $this->capsule->addConnection($parameters + $defaults, 'migration');
    }

    protected function db()
    {
        return $this->capsule->getConnection('migration');
    }

    protected function schema()
    {
        return $this->db()->getSchemaBuilder();
    }

}
