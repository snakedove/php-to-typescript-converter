<?php


namespace Snakedove\PHPToTypescriptConverter\Iterator;


class SingleIterator extends AbstractIterator
{
    function iterate(string $path): void
    {
        try{
            if ($this->converter->checkFile($path)) {
                $this->converter->convert($path);
            }
        } catch(\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
