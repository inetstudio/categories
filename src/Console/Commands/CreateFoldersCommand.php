<?php

namespace InetStudio\Categories\Console\Commands;

use Illuminate\Console\Command;

class CreateFoldersCommand extends Command
{
    /**
     * Имя команды.
     *
     * @var string
     */
    protected $name = 'inetstudio:categories:folders';

    /**
     * Описание команды.
     *
     * @var string
     */
    protected $description = 'Create package folders';

    /**
     * Запуск команды.
     *
     * @return void
     */
    public function handle(): void
    {
        if (config('filesystems.disks.categories')) {
            $path = config('filesystems.disks.categories.root');

            if (! is_dir($path)) {
                mkdir($path, 0777, true);
            }
        }
    }
}
