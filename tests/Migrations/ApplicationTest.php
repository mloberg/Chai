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

    public function testInit()
    {
        $connection = $this->capsule->getConnection('testing');
        $schema = $connection->getSchemaBuilder();
        $schema->dropIfExists('migrations');
        $this->migrations->setMigrationsPath('foo');

        $command = $this->application->find('migrate:init');

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

    public function testCreate()
    {
        $this->migrations->setMigrationsPath('foo');

        $creator = $this->getCreator();
        $command = $this->application->find('migrate:create');
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

    protected function getCreator()
    {
        return $this->getMock('Chai\Migrations\Creator', array('getDatePrefix'), array($this->files));
    }

}
