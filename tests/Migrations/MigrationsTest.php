<?php

use Mockery as m;
use Chai\Migrations\Migrations;

class MigrationsTest extends PHPUnit_Framework_TestCase
{

    protected $migrations;

    protected function setUp()
    {
        $this->migrations = new Migrations;
        $this->migrations->setMigrationsPath(__DIR__.'/stubs');
        $this->migrations->setDatabaseParameters(array(
            'host'     => $_SERVER['DB_HOST'],
            'port'     => $_SERVER['DB_PORT'],
            'username' => $_SERVER['DB_USER'],
            'password' => $_SERVER['DB_PASS'],
            'database' => $_SERVER['DB_NAME'],
            'charset'  => 'utf8',
        ));
        $this->migrations->schema()->dropIfExists('migrations');
        $this->migrations->setup();
    }

    protected function tearDown()
    {
        $this->migrations->schema()->dropIfExists('migrations');
        m::close();
    }

    public function testGetAll()
    {
        $migrations = $this->migrations->getAll();
        $this->assertEquals(4, count($migrations));
        $this->assertEquals('2013_06_14_075839_create_test', $migrations[0]);
    }

    public function testGetAllEmpty()
    {
        $files = m::mock('Illuminate\Filesystem\Filesystem');
        $this->migrations->setFilesystem($files);
        $files->shouldReceive('glob')->once()->andReturn(false);

        $migrations = $this->migrations->getAll();
        $this->assertEquals(0, count($migrations));
    }

    public function testRunUp()
    {
        $migrationName = '2013_06_14_183835_migration_two';
        $migration = $this->migrations->runUp($migrationName);
        $db = $this->migrations->db();
        $ran = $db->table('migrations')
                  ->where('id', $migration->getDate())
                  ->first();
        $this->assertEquals($ran['applied'], 1);
        $this->assertTrue($migration->applied());
    }

    public function testGetRan()
    {
        $run = array('2013_06_14_183835_migration_two', '2013_06_14_183827_migration_one');
        foreach ($run as $migration) {
            $this->migrations->runUp($migration);
        }
        $ran = $this->migrations->getRan();
        $this->assertEquals(count($ran), 2);
    }

}
