<?php


namespace Snakedove\PHPToTypescriptConverter\Iterator;


interface IteratorInterface
{
    public function iterate(string $path): void;
}
