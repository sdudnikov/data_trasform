<?php

include __DIR__ . "/../vendor/autoload.php";

use DataLib\Transform\SchemaBuilder;
use DataLib\Transform\SchemaVersion;

$data = [
    'key_string' => 'example_key',
    'key_bool' => false,
    'key_null' => 'string',
    'test_array' => [
        'key_int' => 11,
        'key_array' => [
            'tt' => '1',
        ]
    ]
];

$schema = SchemaBuilder::createFromArray('test_array', $data);

$schemaVersion = SchemaVersion::create();

$ver = '1.0';
$jsonFormat = $schemaVersion->getSchemaVersion($schema, '1.0');
print_r($jsonFormat);

try {
    $schemaFromJson = $schemaVersion->getSchemaByVersion($jsonFormat);
} catch (\Exception $exception) {
    echo $exception->getMessage();
}
