<?php


namespace Snakedove\PHPToTypescriptConverter\Iterator;


use Snakedove\PHPToTypescriptConverter\Converter\Converter;

abstract class AbstractIterator implements IteratorInterface
{
    protected Converter $converter;

    public function __construct(Converter $converter)
    {
        $this->converter = $converter;
    }

    abstract function iterate(string $path): void;
}
