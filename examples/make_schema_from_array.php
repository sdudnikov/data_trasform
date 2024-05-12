<?php

include __DIR__ . "/../vendor/autoload.php";

use DataLib\Transform\Interface\NodeInterface;
use DataLib\Transform\SchemaBuilder;
use DataLib\Transform\Transformer\MappingArrayFields;
use DataLib\Transform\Validator\NotEmpty;

$data = [
    'key_string' => '4',
    'key_bool' => false,
    'test_array' => [
        'test_not_mapped' => 'll',
        'key_int' => 0,
        'key_array' => [
            'tt' => '1',
            'key_float' => 4,
        ]
    ]
];

$schema = SchemaBuilder::createFromArray('test_array', $data);
$tree = $schema->getTree();

foreach ($tree->getChildren() as $child) {
    $child->validator(new NotEmpty());

    if ($child->getFieldType() == NodeInterface::TYPE_ARRAY) {
        $child->transformer(new MappingArrayFields(function ($field) {
            $mapping = [
                'key_int' => 'key_int_mapped',
                'key_array' => 'key_array_mapped',
            ];

            return $mapping[$field] ?? null;
        }));
    }
}

try {

    $data = $schema->transform($data);
    print_r($data);

} catch (\Exception $exception) {
    echo $exception->getMessage();
}