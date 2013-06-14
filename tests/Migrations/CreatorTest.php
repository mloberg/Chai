<?php

use Mockery as m;
use Chai\Migrations\Creator;

class CreatorTest extends PHPUnit_Framework_TestCase
{

    protected function tearDown()
    {
        m::close();
    }

    public function testValidateName()
    {
        $creator = $this->getCreator();
        $class = new ReflectionClass($creator);
        $method = $class->getMethod('validateName');
        $method->setAccessible(true);
        $this->assertTrue($method->invoke($creator, 'create_table'));
        $this->assertFalse($method->invoke($creator, 'InvalidName'));
        $this->assertFalse($method->invoke($creator, '1'));
    }

    public function testCreate()
    {
        $creator = $this->getCreator();
        $creator->expects($this->any())->method('getDatePrefix')
                ->will($this->returnValue('foo'));
        $creator->getFilesystem()->shouldReceive('get')->once()
                ->with($creator->getStubsDirectory().'/default.stub')
                ->andReturn('{{class}}');
        $creator->getFilesystem()->shouldReceive('put')->once()
                ->with('foo/foo_create_bar.php', 'CreateBar');
        $creator->create('create_bar', 'foo');
    }

    /**
     * @expectedException Chai\Migrations\MigrationsException
     */
    public function testCreateBadMigrationName()
    {
        $creator = $this->getCreator();
        $creator->create('BadMigrationName', 'foo');
    }

    protected function getCreator()
    {
        $files = m::mock('Illuminate\Filesystem\Filesystem');

        return $this->getMock('Chai\Migrations\Creator', array('getDatePrefix'), array($files));
    }

}
