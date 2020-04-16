<?php


namespace Snakedove\PHPToTypescriptConverter\Command;

use Snakedove\PHPToTypescriptConverter\Converter\Converter;
use Snakedove\PHPToTypescriptConverter\Iterator\ArrayIterator;
use Snakedove\PHPToTypescriptConverter\Visitor\DirectoryVisitor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PhpToTypescriptAllCommand extends Command
{
    private string $nameSuffix;
    private bool $convertCollections;

    public function __construct(string $nameSuffix = '', bool $convertCollections = true, string $name = null)
    {
        $this->nameSuffix = $nameSuffix;
        $this->convertCollections = $convertCollections;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setName('ts-create-all')
            ->setDescription('Creates Typescript interfaces from POPOs')
            ->addArgument('input directory', InputArgument::REQUIRED)
            ->addArgument('output directory', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $inDir = $input->getArgument('input directory');
        $outDir = $input->getArgument('output directory');

        try {
            $converter = new Converter($inDir, $outDir, $this->nameSuffix, $this->convertCollections);
            $iterator = new ArrayIterator($converter);
            $visitor = new DirectoryVisitor($iterator);
            $visitor->visit($inDir);
            return 0;
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
            return 0;
        }
    }
}
