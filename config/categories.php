<?php

return [

    /*
     * Настройки изображений
     */

    'images' => [
        'quality' => 75,
        'conversions' => [
            'og_image' => [
                'default' => [
                    [
                        'name' => 'og_image_default',
                        'size' => [
                            'width' => 968,
                            'height' => 475,
                        ],
                    ],
                ],
            ],
            'content' => [
                'default' => [
                    [
                        'name' => 'content_admin',
                        'size' => [
                            'width' => 140,
                        ],
                    ],
                    [
                        'name' => 'content_front',
                        'quality' => 70,
                        'fit' => [
                            'width' => 768,
                            'height' => 512,
                        ],
                    ],
                ],
            ],
        ],
    ],
];
