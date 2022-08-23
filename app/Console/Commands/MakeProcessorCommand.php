<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeProcessorCommand extends GeneratorCommand
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Processors for App.';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:processor';

    /**
     * type being generated.
     * @var string
     */
    protected $type = 'Processors';

    /**
     * Build the class with the given name.
     *
     * Remove the base controller import if we are already in base namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {

        $controllerNamespace = $this->getNamespace($name);

        $replace = [];

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\\Processors';
    }

    /**
     * get stub
     * @return [type] [description]
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/processor.plain.stub';
    }
}
