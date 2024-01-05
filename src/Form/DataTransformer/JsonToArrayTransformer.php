<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class JsonToArrayTransformer implements DataTransformerInterface
{
    /**
     * @param array $value
     * @return string
     */
    public function transform(mixed $value): mixed
    {
        return json_encode($value);
    }

    /**
     * @param string $value
     * @return array
     */
    public function reverseTransform(mixed $value): mixed
    {
        return json_decode($value, true);
    }
}
