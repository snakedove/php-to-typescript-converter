<?php


namespace Snakedove\PHPToTypescriptConverter\Traverser;


use Snakedove\PHPToTypescriptConverter\Converter\Converter;

class Traverser
{
    private Converter $converter;

    public function __construct(Converter $converter)
    {
        $this->converter = $converter;
    }

    public function traverse(STRING $path): void {
        if (is_dir($path)) {
            echo "\n" . '+ changed to directory: ' . $path . "\n";
            $files = scandir($path);
            $files = array_slice($files, 2);
            foreach ($files as $file) {
                $this->traverse($path . '/' . $file);
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
