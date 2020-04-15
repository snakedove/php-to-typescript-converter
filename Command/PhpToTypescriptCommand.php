<?php


namespace Snakedove\PHPToTypescriptConverter\Command;

use Snakedove\PHPToTypescriptConverter\Converter\Converter;
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
        $outDir = $input->getArgument('output directory');
        $matches = null;
        $matched = preg_match('/^.*\/(.+)\.php/', $inFile, $matches);

        if (!is_dir($outDir)) {
            $output->writeln('output directory does not exist');
            return 0;
        }

        if ($matched && !empty($matches) && isset($matches[1])) {
            $outFile = $outDir . '/' . $matches[1] . $this->nameSuffix . '.d.ts';
        } else {
            $output->writeln('php file not correct');
            return 0;
        }

        if (!file_exists($inFile)) {
            $output->writeln('php file does not exist');
            return 0;
        }

        // collections will be ignored if $convertCollections is true, as their Type will be <InterfaceType>[]
        if ($this->convertCollections && strpos($inFile,'Collection') !== false) {
            $output->writeln('php collection ' . $inFile . ' ignored');
            return 0;
        }

        $converter = new Converter($inFile, $outFile, $this->nameSuffix, $this->convertCollections);
        $output->writeln($converter->convert());

        return 0;
    }
}
