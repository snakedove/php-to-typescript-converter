<?php


namespace Snakedove\PHPToTypescriptConverter\Visitor;


class SingleVisitor extends AbstractVisitor
{
    public function visit(string $path): void
    {
        try {
            if (!is_dir($path)) {
                $this->iterator->iterate($path);
            }
        } catch(\Exception $e) {
            echo $e->getMessage();
            throw new \Exception($e->getMessage());
        }
    }
}
