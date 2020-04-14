<?php


namespace Snakedove\PHPToTypescriptConverter\Command;

use Snakedove\PHPToTypescriptConverter\Service\InterfaceCreator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PhpToTypescriptCommand extends Command
{
    private string $nameSuffix;

    public function __construct($nameSuffix = '', string $name = null)
    {
        $this->nameSuffix = $nameSuffix;
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

        if(!is_dir($outDir)) {
            $output->writeln('output directory does not exist');
            return 0;
        }

        if ($matched && !empty($matches) && isset($matches[1])) {
            $outFile = $outDir . '/' . $matches[1] . $this->nameSuffix . '.d.ts';
        } else {
            $output->writeln('php file not correct');
            return 0;
        }

        if(!file_exists($inFile)) {
            $output->writeln('php file does not exist');
            return 0;
        }

        $interFaceCreator = new InterfaceCreator($inFile, $outFile, $this->nameSuffix);
        $output->writeln($interFaceCreator->run());

        return 0;
    }
}
