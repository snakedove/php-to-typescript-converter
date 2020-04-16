<?php


namespace Snakedove\PHPToTypescriptConverter\Iterator;


use Snakedove\PHPToTypescriptConverter\Converter\Converter;

class ArrayIterator extends AbstractIterator
{
    public Converter $converter;

    public function iterate(string $path): void
    {
        if (is_dir($path)) {
            echo "\n" . '+ changed to directory: ' . $path . "\n";
            $files = scandir($path);
            $files = array_slice($files, 2);
            foreach ($files as $file) {
                $this->iterate($path . '/' . $file);
            }
        } else {
            try{
                if ($this->converter->checkFile($path)) {
                    $this->converter->convert($path);
                }
            } catch(\Exception $e) {
                throw new \Exception($e->getMessage());
            }
        }
    }
}
