<?php
declare(strict_types=1);

namespace DataLib\Transform\Transformer;

use DataLib\Transform\Interface\NodeInterface;
use DataLib\Transform\Interface\TransformerInterface;

class PipeTransformer implements TransformerInterface
{
    public function __construct(array $transformers)
    {
        $transformers = array_reverse($transformers);

        /** @var TransformerInterface $transformer */
        foreach ($transformers as $transformer) {
            if (!$transformer instanceof TransformerInterface) {
                $class = get_class($transformer);
                throw new \Exception('Class: '. $class . ' should implement ' . TransformerInterface::class);
            }

            $this->pushTransformer($transformer);
        }
    }


    protected ?TransformerInterface $transformer = null;

    public function transform(mixed $data, NodeInterface $node): mixed
    {
        if (!$this->transformer) {
            return $data;
        }

        return $this->transformer->transform($data, $node);
    }

    public function pushTransformer(TransformerInterface $transformer): void
    {
        $this->isSupported($transformer);

        /** @var $transformer PipeTransform */
        $transformer->setNext($this->transformer);
        $this->transformer = $transformer;
    }

    protected function isSupported(TransformerInterface $transformer): void
    {
        if (!in_array(PipeTransform::class, class_uses($transformer))) {
            $class = get_class($transformer);
            throw new \Exception('Class: '. $class .' should uses trait: ' . PipeTransform::class);
        }
    }
}