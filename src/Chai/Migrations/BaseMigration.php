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

    public function runUp()
    {
        if ($this->applied() && method_exists($this, 'update')) {
            $this->update();
        } elseif (!$this->applied()) {
            $this->getOrCreateRecord();
            $ran_at = date('Y-m-d H:i:s');
            $this->up();
            $this->db()->table('migrations')
                       ->where('id', $this->getDate())
                       ->update(array('applied' => true, 'ran_at' => $ran_at));
        }
        return true;
    }

    public function runDown()
    {
        if (!$this->applied()) {
            throw new MigrationsException('Migration not applied.');
            return false;
        }
        $this->getOrCreateRecord();
        $ran_at = date('Y-m-d H:i:s');
        $this->down();
        $this->db()->table('migrations')
                   ->where('id', $this->getDate())
                   ->update(array('applied' => false, 'ran_at' => $ran_at));
        return true;
    }

    public function applied()
    {
        $record = $this->db()->table('migrations')
                             ->where('id', $this->getDate())->first();
        return (bool)$record['applied'];
    }

    public function setConnection($connection)
    {
        $this->connection = $connection;
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

    protected function db()
    {
        return $this->connection;
    }

    protected function schema()
    {
        return $this->db()->getSchemaBuilder();
    }

    protected function getFileName()
    {
        $reflectionClass = new \ReflectionClass(get_called_class());
        $filename = basename($reflectionClass->getFileName(), '.php');
        return $filename;
    }

    protected function getRecord()
    {
        return $this->db()->table('migrations')
                          ->where('id', $this->getDate())->first();
    }

    protected function getOrCreateRecord()
    {
        $record = $this->getRecord();
        if (!$record) {
            $this->db()->table('migrations')->insert(array(
                'id'      => $this->getDate(),
                'name'    => $this->getName(),
                'applied' => false,
            ));
            return $this->getRecord();
        }
        return $record;
    }

}
