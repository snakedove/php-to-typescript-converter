<?php


use PHPUnit\Framework\TestCase;
use Snakedove\PHPToTypescriptConverter\Converter\Converter;
use Snakedove\PHPToTypescriptConverter\Traverser\Traverser;

class ConverterTest extends TestCase
{
    public function testSingleFileConversion() {
        $sourceFilePath = getcwd() . '/TestInput/OtherSomeTestClass.php';
        $outFilePath = getcwd() . '/TestOutput/TestSingle/OtherSomeTestClassInterface.d.ts';

        try {
            $converter = new Converter($sourceFilePath, $outFilePath, 'Interface', true);
            $converter->convert($sourceFilePath);
        } catch (\Exception $e) {}

        $this->assertEquals(true, file_exists($outFilePath));
    }

    public function testMultipleFileConversion() {
        $sourcePath = getcwd() . '/TestInput';
        $outFilePath = getcwd() . '/TestOutput/TestAll';
        $outFilePath1 = getcwd() . '/TestOutput/TestAll/OtherSomeTestClassInterface.d.ts';
        $outFilePath2 = getcwd() . '/TestOutput/TestAll/More/SomeTestClassInterface.d.ts';

        try {
            $converter = new Converter($sourcePath, $outFilePath, 'Interface', true);
            $traverser = new Traverser($converter);
            $traverser->traverse($sourcePath);
        } catch (\Exception $e) {}

        $this->assertEquals(true, file_exists($outFilePath1));
        $this->assertEquals(true, file_exists($outFilePath2));
    }
}
