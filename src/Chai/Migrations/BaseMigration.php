<?php

namespace Chai\Migrations;

abstract class BaseMigration
{

    protected $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    abstract public function up();
    abstract public function down();

    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    protected function db()
    {
        return $this->connection;
    }

    protected function schema()
    {
        return $this->db()->getSchemaBuilder();
    }

    public function getDate()
    {
        $filename = $this->getFileName();
        $date = implode('', array_slice(explode('_', $filename), 0, 4));
        return date('Y-m-d H:i:s', strtotime($date));
    }

    public function getName()
    {
        $filename = $this->getFileName();
        return implode('_', array_slice(explode('_', $filename), 4));
    }

    private function getFileName()
    {
        $reflectionClass = new \ReflectionClass(get_called_class());
        $filename = basename($reflectionClass->getFileName(), '.php');
        return $filename;
    }

}
