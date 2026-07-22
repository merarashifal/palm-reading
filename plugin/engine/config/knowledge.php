<?php

return [
    'paths' => [
        'manifest' => dirname(__DIR__, 2) . '/database/knowledge/manifest.json',
        'schema' => dirname(__DIR__, 2) . '/database/schema/rules_schema.json',
        'dictionaries' => dirname(__DIR__, 2) . '/database/knowledge/dictionary',
        'output' => dirname(__DIR__, 2) . '/database/generated',
    ],
    'validation' => [
        'minimum_score' => 95,
        'languages' => ['en', 'hi'],
    ]
];
