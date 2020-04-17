<?php


use Snakedove\PHPToTypescriptConverter\Converter\Converter;
use Snakedove\PHPToTypescriptConverter\Iterator\SingleIterator;
use Snakedove\PHPToTypescriptConverter\Iterator\ArrayIterator;
use Snakedove\PHPToTypescriptConverter\Visitor\SingleVisitor;
use Snakedove\PHPToTypescriptConverter\Visitor\DirectoryVisitor;

class ConverterTest extends \PHPUnit\Framework\TestCase
{
    public function testSingleFileConversion() {
        $sourceFilePath = getcwd() . '/TestInput/OtherSomeTestClass.php';
        $outFilePath = getcwd() . '/TestOutput/TestSingle/OtherSomeTestClassInterface.d.ts';

        try {
            $converter = new Converter($sourceFilePath, $outFilePath, 'Interface', true);
            $iterator = new SingleIterator($converter);
            $visitor = new SingleVisitor($iterator);
            $visitor->visit($sourceFilePath);
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
            $iterator = new ArrayIterator($converter);
            $visitor = new DirectoryVisitor($iterator);
            $visitor->visit($sourcePath);
        } catch (\Exception $e) {}

        $this->assertEquals(true, file_exists($outFilePath1));
        $this->assertEquals(true, file_exists($outFilePath2));
    }
}
