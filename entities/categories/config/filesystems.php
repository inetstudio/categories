<?php

return [

    /*
     * Расширение файла конфигурации app/config/filesystems.php
     * добавляет локальные диски для хранения изображений категорий
     */

    'categories' => [
        'driver' => 'local',
        'root' => storage_path('app/public/categories'),
        'url' => env('APP_URL').'/storage/categories',
        'visibility' => 'public',
    ],

];
