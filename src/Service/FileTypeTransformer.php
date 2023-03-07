<?php

namespace App\Service;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\HttpFoundation\File\File;

class FileTypeTransformer implements DataTransformerInterface
{
    private $directory;

    public function __construct($directory)
    {
        $this->directory = $directory;
    }

    public function transform($value)
    {
        if (!$value) {
            return null;
        }

        return new File($this->directory . '/' . $value);
    }

    public function reverseTransform($value)
    {
        if (!$value) {
            return null;
        }

        if (!$value instanceof File) {
            throw new TransformationFailedException('Expected a Symfony\Component\HttpFoundation\File\File.');
        }

        return $value->getClientOriginalName();
    }
}