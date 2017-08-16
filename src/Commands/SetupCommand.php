<?php

namespace InetStudio\Categories\Commands;

use Illuminate\Console\Command;

class SetupCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'inetstudio:categories:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup categories package';

    /**
     * Commands to call with their description.
     *
     * @var array
     */
    protected $calls = [
        'vandor:publish --provider="InetStudio\Categories\CategoriesServiceProvider" --tag="migrations" --force' => 'Publish migrations',
        'migrate' => 'Migration',
        'optimize' => 'Optimize',
        'inetstudio:categories:folders' => 'Create folders',
        'vandor:publish --provider="InetStudio\Categories\CategoriesServiceProvider" --tag="public" --force' => 'Publish public',
    ];

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        foreach ($this->calls as $command => $info) {
            $this->line(PHP_EOL.$info);
            $this->call($command);
        }
    }
}
