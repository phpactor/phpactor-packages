<?php

return [
    'repos' => [
        'github.com' => [
            'phpactor/phpactor-packages' => [
                'sync-tags' => false,
                'split' => require __DIR__ . '/build/split-list.php',
            ],
        ],
    ],
];
