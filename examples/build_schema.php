<?php

use DataLib\Transform\Interface\NodeInterface;
use DataLib\Transform\SchemaBuilder;
use DataLib\Transform\Transformer\DefaultValue;
use DataLib\Transform\Transformer\SkipField;
use DataLib\Transform\Validator\NotEmpty;

include __DIR__ . "/../vendor/autoload.php";

$root = SchemaBuilder::root();

$root
    ->field('added', NodeInterface::TYPE_STRING)->isAdded(true)->end()
    ->field('key', NodeInterface::TYPE_STRING)
        ->transformer(new DefaultValue('default'))
        ->required()
    ->end()
    ->field('name', NodeInterface::TYPE_STRING)->validator(new NotEmpty())->end()
    ->field('test_array', NodeInterface::TYPE_ARRAY)
        ->child()
            ->field('key_int', NodeInterface::TYPE_INT)->outputFields(['key_int_mapped'])->end()
            ->field('key_array', NodeInterface::TYPE_ARRAY)
                ->child()
                    ->field('not_skip_test', NodeInterface::TYPE_STRING)->end()
                    ->field('skip_test', NodeInterface::TYPE_STRING)->transformer(new SkipField(true))->end()
                ->endChild()
            ->end()
        ->endChild()
    ->end();

$schema = SchemaBuilder::createFromRoot('test', $root);


$data = [
    'key' => 'example_key',
    'name' => 'name',
    'test_array' => [
        'key_int' => 1,
        'key_array' => [
            'not_skip_test' => '1',
            'skip_test' => '1'
        ]
    ]
];

try {
    $data = $schema->transform($data);
    print_r($data);

} catch (\Exception $exception) {
    echo $exception->getMessage();
}