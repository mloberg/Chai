<?php

require __DIR__.'/stubs/2013_06_14_183835_migration_two.php';

use Illuminate\Database\Capsule\Manager as Capsule;

class BaseMigrationTest extends PHPUnit_Framework_TestCase
{

    protected $capsule;
    protected $migration;

    protected function setUp()
    {
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
        $this->migration = new MigrationTwo($this->getConnection());
        $this->getSchemaBuilder()->dropIfExists('migrations');
        $this->getSchemaBuilder()->create('migrations', function($table) {
            $table->dateTime('id')->uniqe();
            $table->string('name');
            $table->boolean('applied');
            $table->timestamp('ran_at');
        });
    }

    public function testDB()
    {
        $db = $this->getMethod('db');
        $this->assertEquals($db->invoke($this->migration),
                            $this->getConnection());
    }

    public function testSetConnection()
    {
        $test = new stdClass;
        $this->migration->setConnection($test);
        $db = $this->getMethod('db');
        $this->assertEquals($db->invoke($this->migration), $test);
    }

    public function testSchema()
    {
        $schema = $this->getMethod('schema');
        $this->assertEquals($schema->invoke($this->migration),
                            $this->getSchemaBuilder());
    }

    public function testApplied()
    {
        $this->assertFalse($this->migration->applied());
        $this->getConnection()->table('migrations')->insert(array(
            'id'      => $this->migration->getDate(),
            'name'    => $this->migration->getName(),
            'applied' => true,
        ));
        $this->assertTrue($this->migration->applied());
    }

    public function testRunUp()
    {
        $this->assertNull($this->getMigrationRecord());
        $this->assertTrue($this->migration->runUp());
        $this->assertTrue($this->migration->applied());
    }

    /**
     * @expectedException UpdateMethodRan
     */
    public function testRunUpWithUpdateMethod()
    {
        $this->getConnection()->table('migrations')->insert(array(
            'id'      => $this->migration->getDate(),
            'name'    => $this->migration->getName(),
            'applied' => true,
        ));
        $this->migration->runUp();
    }

    public function testGetDate()
    {
        $this->assertEquals('2013-06-14 18:38:35', $this->migration->getDate());
    }

    public function testGetName()
    {
        $this->assertEquals('migration_two', $this->migration->getName());
    }

    protected function getMethod($name)
    {
        $class = new ReflectionClass($this->migration);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    protected function getConnection()
    {
        return $this->capsule->getConnection('testing');
    }

    protected function getSchemaBuilder()
    {
        return $this->getConnection()->getSchemaBuilder();
    }

    protected function getMigrationRecord()
    {
        return $this->getConnection()->table('migrations')
                    ->where('id', $this->migration->getDate())->first();
    }

}
