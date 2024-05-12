<?php

include __DIR__ . "/../vendor/autoload.php";

use DataLib\Transform\SchemaBuilder;
use DataLib\Transform\SchemaModification;
use DataLib\Transform\Transformer\DefaultValue;
use DataLib\Transform\Transformer\MappingArrayFields;

$data = [
    'req' => '',
    'key_string' => 'example_key',
    'key_bool' => false,
    'kl' => '',
    'test_array' => [
        'not_mapped' => '',
        'key_int' => 11,
        'key_array' => [
            'tt' => '1',
            'key_float' => 4,
            'not_mapped' => 'tt'
        ]
    ]
];


$schema = SchemaBuilder::createFromArray('test_array', $data);

$modifications = new SchemaModification();

$modifications->addModification($schema, 'kl', [
    SchemaModification::MODIFICATION_TRANSFORMER => new DefaultValue('test_default_value'),
]);

$modifications->addModification($schema, 'test_array', [
    SchemaModification::MODIFICATION_TRANSFORMER => new MappingArrayFields(function ($field) {
        $mapping = [
            'key_int' => 'key_int_mapped',
            'key_array' => 'key_array_mapped',
            'key_array.tt' => 'mapped_tt',
            'key_array.key_float' => 'mapped_key_float',
        ];

        return $mapping[$field] ?? null;
    }, true),
]);

$modifications->addModification($schema, 'req', [
    SchemaModification::MODIFICATION_IS_REQUIRED => true,
]);

try {
    $data = $schema->transform($data);
    print_r($data);
} catch (\Exception $e) {
    echo $e->getMessage();
}
