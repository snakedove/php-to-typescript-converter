<?php


namespace Snakedove\PHPToTypescriptConverter\Visitor;


interface VisitorInterface
{
    public function visit(string $path): void;
}
