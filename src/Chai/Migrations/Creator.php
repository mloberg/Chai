<?php

namespace Chai\Migrations;

use \Illuminate\Filesystem\Filesystem;

class Creator
{

    /**
     * The filesystem instance
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    private $files;

    /**
     * Create a new migration creator instance.
     *
     * @param \Illuminate\Filesystem\Filesystem $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    public function create($name, $path)
    {
        if (!$this->validateName($name)) {
            throw new MigrationsException('Invalid migration name');
        }

        $path = $this->getPath($name, $path);

        $stub = $this->getStub();

        $this->files->put($path, $this->populateStub($name, $stub));

        return $path;
    }

    protected function validateName($name)
    {
        return !!preg_match('/^[a-z][a-z0-9_]*$/', $name);
    }

    protected function getStub()
    {
        return $this->files->get($this->getStubsDirectory() . '/default.stub');
    }

    protected function populateStub($name, $stub)
    {
        $stub = str_replace('{{class}}', studly_case($name), $stub);

        return $stub;
    }

    protected function getPath($name, $path)
    {
        return $path.'/'.$this->getDatePrefix().'_'.$name.'.php';
    }

    protected function getDatePrefix()
    {
        return date('Y_m_d_His');
    }

    public function getStubsDirectory()
    {
        return __DIR__.'/stubs';
    }

    public function getFilesystem()
    {
        return $this->files;
    }

}
