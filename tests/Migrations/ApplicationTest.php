<?php

use Mockery as m;
use Symfony\Component\Console\Tester\CommandTester;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Filesystem\Filesystem;
use Chai\Console\Application;
use Chai\Migrations\Console\Application as Migrations;

class ApplicationTest extends PHPUnit_Framework_TestCase
{

    protected $application;
    protected $migrations;
    protected $capsule;
    protected $files;

    protected function setUp()
    {
        $this->files = m::mock('Illuminate\Filesystem\Filesystem');
        // Create console application
        $this->application = new Application;
        $this->migrations = new Migrations;
        $this->migrations->setFilesystem($this->files);
        $this->migrations->setDatabaseParameters(array(
            'host'     => $_SERVER['DB_HOST'],
            'port'     => $_SERVER['DB_PORT'],
            'username' => $_SERVER['DB_USER'],
            'password' => $_SERVER['DB_PASS'],
            'database' => $_SERVER['DB_NAME'],
            'charset'  => 'utf8',
        ));
        $this->migrations->setMigrationsPath(__DIR__.'/stubs');
        $this->application->register($this->migrations);
        // Database access to test migrations
        $this->capsule = new Capsule;
        $this->capsule->addConnection(array(
            'driver'    => 'mysql',
            'host'      => $_SERVER['DB_HOST'],
            'port'      => $_SERVER['DB_PORT'],
            'username'  => $_SERVER['DB_USER'],
            'password'  => $_SERVER['DB_PASS'],
            'database'  => $_SERVER['DB_NAME'],
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
        ), 'testing');
    }

    /**
     * Test the migration:init command
     *
     * Should create migrations table and create the migrations directory
     */
    public function testInit()
    {
        $connection = $this->capsule->getConnection('testing');
        $schema = $connection->getSchemaBuilder();
        $schema->dropIfExists('migrations');
        $this->migrations->setMigrationsPath('foo');

        $command = $this->application->find('migration:init');

        $this->files->shouldReceive('isDirectory')->once()
                    ->with('foo')->andReturn(false);
        $this->files->shouldReceive('makeDirectory')->once()
                    ->with('foo', 0664)->andReturn(true);

        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));

        $rows = $connection->select("SELECT * FROM migrations");
        $this->assertEquals(0, count($rows));
        $this->assertRegExp('/You are now ready to run migrations/', $commandTester->getDisplay());
    }

    /**
     * Test the migration:create command
     *
     * Should create a migration with the current timestamp
     * and name in the migrations directory
     */
    public function testCreate()
    {
        $this->migrations->setMigrationsPath('foo');

        $creator = $this->getCreator();
        $command = $this->application->find('migration:create');
        $command->setCreator($creator);

        $creator->expects($this->any())->method('getDatePrefix')
                ->will($this->returnValue('foo'));
        $creator->getFilesystem()->shouldReceive('get')->once()
                ->with($creator->getStubsDirectory().'/default.stub')
                ->andReturn('{{class}}');
        $creator->getFilesystem()->shouldReceive('put')->once()
              ->with('foo/foo_create_user_table.php', 'CreateUserTable');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
            'name' => 'create_user_table',
        ));

        $this->assertRegExp('/Created foo\/foo_create_user_table\.php/', $commandTester->getDisplay());
    }

    /**
     * Test the migration:list command
     *
     * Should list all migrations (files).
     */
    public function testList()
    {
        $this->files->shouldReceive('glob')->once()
             ->with($this->migrations->getMigrationsPath().'/*_*.php')
             ->andReturn(array('foobar'));

        $command = $this->application->find('migration:list');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));

        $this->assertRegExp('/\* foobar/', $commandTester->getDisplay());
    }

    /**
     * Test the migration:list command when no migrations are found.
     */
    public function testListEmpty()
    {
        $this->files->shouldReceive('glob')->once()
             ->with($this->migrations->getMigrationsPath().'/*_*.php')
             ->andReturn(false);

        $command = $this->application->find('migration:list');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));

        $this->assertRegExp('/No migrations found/', $commandTester->getDisplay());
    }

    /**
     * Test the migration:status command
     *
     * Should list all current migrations (files) and if they are applied.
     */
    public function testStatus()
    {
        $this->files->shouldReceive('isDirectory')->once()->andReturn(true);
        $this->migrations->setup();
        $this->migrations->db()->table('migrations')->insert(array(
            'id'      => '2013-06-14 07:58:39',
            'name'    => 'create_test',
            'applied' => true,
        ));
        $migration = $this->migrations->getMigrationsPath() .
                     '/2013_06_14_075839_create_test.php';
        $this->files->shouldReceive('requireOnce')->once()->with($migration);
        require_once($migration);
        $this->files->shouldReceive('glob')->once()
             ->with($this->migrations->getMigrationsPath().'/*_*.php')
             ->andReturn(array('2013_06_14_075839_create_test'));

        $command = $this->application->find('migration:status');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));

        $this->assertRegExp(
            '/\* 2013_06_14_075839_create_test \(applied\)/',
            $commandTester->getDisplay()
        );
    }

     * Return a Migrations Creator mock object.
     * @return Chai\Migrations\creator mock object
     */
    protected function getCreator()
    {
        return $this->getMock('Chai\Migrations\Creator', array('getDatePrefix'), array($this->files));
    }

}
