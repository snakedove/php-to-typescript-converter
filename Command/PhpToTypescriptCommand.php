<?php


namespace Snakedove\PHPToTypescriptConverter\Command;

use Snakedove\PHPToTypescriptConverter\Converter\Converter;
use Snakedove\PHPToTypescriptConverter\Iterator\SingleIterator;
use Snakedove\PHPToTypescriptConverter\Visitor\SingleVisitor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PhpToTypescriptCommand extends Command
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
            ->setName('ts-create')
            ->setDescription('Creates Typescript interfaces from POPOs')
            ->addArgument('php file', InputArgument::REQUIRED)
            ->addArgument('output directory', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $inFile = $input->getArgument('php file');
        $inPath = '';
        $matched = preg_replace('/\/([a-zA-Z0-9-_]+\.php)?$/', '', $inFile);

        if ($matched !== null) {
            $inPath = $matched;
        }

        $outDir = $input->getArgument('output directory');

        try {
            $converter = new Converter($inPath, $outDir, $this->nameSuffix, $this->convertCollections);
            $converter->convert($inFile);
            return 0;
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
            return 0;
        }
    }
}
