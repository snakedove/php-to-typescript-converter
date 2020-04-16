<?php


namespace Snakedove\PHPToTypescriptConverter\Visitor;


use Snakedove\PHPToTypescriptConverter\Iterator\IteratorInterface;

abstract class AbstractVisitor implements VisitorInterface
{
    protected IteratorInterface $iterator;

    public function __construct(IteratorInterface $iterator)
    {
        $this->iterator = $iterator;
    }

    abstract function visit(string $path): void;
}
