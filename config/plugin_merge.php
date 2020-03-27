#!/usr/bin/env php
<?php

if (in_array($argv[1], ['help', '-h', '--help'])) {
    echo <<<EOF

Usage:

php ./plugin_merge.php PLUGIN_SETTINGS_JSON_FILE_PATH TARGET_PATH\n\n
EOF;

    exit;
}

$json_schem = json_decode(file_get_contents('./config_schema.json'), true);

$plugins = $json_schem['definitions']['plugins']['oneOf'];

$insert_plugin_json_file = $argv[1];
$generate_to_file = $argv[2] ?? 'generate.json';

$insert_plugin_json = json_decode(file_get_contents($insert_plugin_json_file), true);

$insert_plugin_name = $insert_plugin_json['$id'];
$insert_plugin_description = $insert_plugin_json['description'];
$insert_plugin_properties = $insert_plugin_json['properties'];
$insert_plugin_required = $insert_plugin_json['required'] ?? false;

$i = -1;

function write(array $json_schem, string $generate_to_file): void
{
    // $json_schem['definitions']['image']['not']['enum']
    file_put_contents("./$generate_to_file", json_encode(
        $json_schem, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT).PHP_EOL);
}

foreach ($plugins as $plugin) {
    ++$i;
    if ($plugin['properties']['image']['const'] === $insert_plugin_name) {
        $plugins[$i]['type'] = 'object';
        $plugins[$i]['description'] = $insert_plugin_description;
        $plugins[$i]['required'] = ['image'];
        $plugins[$i]['additionalProperties'] = false;

        $plugins[$i]['properties']['with']['properties'] = $insert_plugin_properties;
        $plugins[$i]['properties']['with']['additionalProperties'] = false;
        $plugins[$i]['properties']['with']['description'] = 'plugin settings';
        $insert_plugin_required && $plugins[$i]['properties']['with']['required'] = $insert_plugin_required;

        $plugins[$i]['properties']['additionalProperties'] = false;
        $plugins[$i]['properties']['image']['description'] = 'plugin image name';
        $plugins[$i]['properties']['if'] = ['$ref' => '#/definitions/if'];
        $plugins[$i]['properties']['pull'] = ['$ref' => '#/definitions/pull'];
        $plugins[$i]['properties']['privileged'] = ['$ref' => '#/definitions/privileged'];

        echo 'plugin found, update'.PHP_EOL;
        $json_schem['definitions']['plugins']['oneOf'] = $plugins;
        write($json_schem, $generate_to_file);

        exit;
    }
}

echo 'not found, insert'.PHP_EOL;

$plugins[] = [
    'type' => 'object',
    'description' => $insert_plugin_description,
    'properties' => [
        'image' => [
            'type' => 'string',
            'const' => $insert_plugin_name,
            'description' => 'plugin image name',
        ],
        'with' => [
            'type' => 'object',
            'description' => 'plugin setting',
            'properties' => $insert_plugin_properties,
            'additionalProperties' => false,
        ],
        'if' => [
            '$ref' => '#/definitions/if',
        ],
        'pull' => [
            '$ref' => '#/definitions/pull',
        ],
        'privileged' => [
            '$ref' => '#/definitions/privileged',
        ],
        'read_only' => [
            '$ref' => '#/definitions/read_only',
        ],
        'additionalProperties' => false,
    ],
    'additionalProperties' => false,
    'required' => [
        'image',
    ],
];

$json_schem['definitions']['plugins']['oneOf'] = $plugins;
write($json_schem, $generate_to_file);
