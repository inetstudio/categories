<?php

namespace InetStudio\CategoriesPackage\Console\Commands;

use InetStudio\AdminPanel\Base\Console\Commands\BaseSetupCommand;

/**
 * Class SetupCommand.
 */
class SetupCommand extends BaseSetupCommand
{
    /**
     * Имя команды.
     *
     * @var string
     */
    protected $name = 'inetstudio:categories-package:setup';

    /**
     * Описание команды.
     *
     * @var string
     */
    protected $description = 'Setup categories package';

    /**
     * Инициализация команд.
     */
    protected function initCommands(): void
    {
        $this->calls = [
            [
                'type' => 'artisan',
                'description' => 'Categories setup',
                'command' => 'inetstudio:categories-package:categories:setup',
            ],
        ];
    }
}
