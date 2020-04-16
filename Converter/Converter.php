<?php

namespace Snakedove\PHPToTypescriptConverter\Converter;

class Converter {
    const MATCH_CLASS_NAME = '/\n[a-z ]*?class ([a-zA-Z0-9-_]+)/';
    const MATCH_EXTENDS = '/class [a-zA-Z0-9-_]+ extends ([a-zA-Z0-9-_]+)/';
    const MATCH_PROPERTIES = '/\n\t? *[a-z]+ (([^ ]+ )?[\$a-zA-Z0-9-_]+)( =[^;]+)?;/';
    const CONVERT_TYPES = [
        '/int/' => 'number',
        '/float/' => 'number',
        '/string/' => 'string',
        '/^\\\.+/' => 'any',
        '/array/' => 'any[]',
        '/object/' => 'any',
        '/bool/' => 'boolean',
        '/iterable/' => 'any[]'
    ];
    const INTERNAL_TYPES = ['bool', 'int', 'float', 'string', 'array', 'iterable', 'object'];

    private string $inputPath;
    private string $outputPath;
    private string $nameSuffix;
    private bool $convertCollections;

    public function __construct(
        $inputPath,
        $outputPath,
        $nameSuffix,
        $convertCollections
    )
    {
        $inputPath = preg_replace('/\/([a-zA-Z0-9-_]]+\.php)?$/', '', $inputPath);
        if ($inputPath !== null) {
            $this->inputPath = $inputPath;
        }
        $outputPath = preg_replace('/\/$/', '', $outputPath);
        if ($outputPath !== null) {
            $this->outputPath = $outputPath;
        }
        $this->nameSuffix = $nameSuffix;
        $this->convertCollections = $convertCollections;
    }

    public function checkFile(string $path): bool {
        $matches = null;
        $matched = preg_match('/^.*\/(.+)\.php/', $path, $matches);

        if (!$matched || empty($matches) || !isset($matches[1])) {
            throw new \Exception('php file not correct');
        }

        if (!file_exists($path)) {
            throw new \Exception('php file does not exist');
        }

        return true;
    }

    private function getOutputFile (string $inputFile): string {
        $matches = null;
        $replacedInputFile = str_replace($this->inputPath, $this->outputPath, $inputFile);
        $replacedInputFileExtension = preg_replace('/\.php$/', $this->nameSuffix . '.d.ts', $replacedInputFile);

        if ($replacedInputFileExtension !== null) {
            return $replacedInputFileExtension;
        }

        return '';
    }

    public function convert(string $inputFile): void {
        // collections will be ignored if $convertCollections is true, as their Type will be <InterfaceType>[]
        if ($this->convertCollections && strpos($inputFile,'Collection') !== false) {
            echo 'php collection ' . $inputFile . ' ignored' . "\n";
            return;
        }

        $outputFile = $this->getOutputFile($inputFile);
        $converted = $this->writeInterface($this->convertFile($inputFile), $outputFile);

        if ($converted) {
            echo $outputFile . ' created' . "\n";
            return;
        }

        throw new \Exception('OOPS: could not create ' . $outputFile);
    }

    private function getMatch(string $pattern, string $subject): string {
        $matches = null;
        $matched = preg_match($pattern, $subject, $matches);

        if ($matched && !empty($matches) && isset($matches[1])) {
            return $matches[1];
        }

        return '';
    }

    private function getMatches(string $pattern, string $subject): array {
        $matches = null;
        $matched = preg_match_all($pattern, $subject, $matches);

        if ($matched && !empty($matches) && isset($matches[1])) {
            return $matches[1];
        }

        return [];
    }

    public function convertProps(array $props): array {
        $classProps = [];

        foreach ($props as $prop) {
            $propParts = explode(' ', $prop);
            $name = $propParts[0];
            $type = '';

            if (count($propParts) === 2) {
                $type = $propParts[0];
                $name = $propParts[1];
            }

            $name = str_replace('$', '', $name);
            $type = preg_replace('/\?/', '', $type);

            if (empty($type)) {
                $type = 'any';
            } else {
                $type = $this->convertType($type);
            }

            $newProp = $name  . ': ' . $type . ';';
            array_push($classProps, $newProp);
        }

        return $classProps;
    }

    private function convertType(string $type): string {
        foreach (self::CONVERT_TYPES as $from => $to) {
            if (preg_match($from, $type)) {
                return preg_replace($from, $to, $type);
            }
        }

        if ($this->convertCollections && strpos($type, 'Collection') !== false) {
            return str_ireplace('Collection', $this->nameSuffix . '[]', $type);
        }

        if (!in_array($type, self::INTERNAL_TYPES)) {
            return $type . $this->nameSuffix;
        }

        return $type;
    }

    private function convertFile(string $filePath): ConvertedFile {
        $fileContent = file_get_contents($filePath);
        $convertedFile = new ConvertedFile();

        $classNameMatch = $this->getMatch(self::MATCH_CLASS_NAME, $fileContent);
        
        if (!empty($classNameMatch)) {
            $convertedFile->setClassName($classNameMatch . $this->nameSuffix);
        }

        $extendsMatch = $this->getMatch(self::MATCH_EXTENDS, $fileContent);
        
        if (!empty($extendsMatch)) {
            $convertedFile->setExtends($extendsMatch . $this->nameSuffix);
        }

        $propsMatches = $this->getMatches(self::MATCH_PROPERTIES, $fileContent);
        $classProps = $this->convertProps($propsMatches);

        if (!empty($classProps)) {
            $convertedFile->setProperties($classProps);
        }
        
        return $convertedFile;
    }

    private function writeInterface(ConvertedFile $convertedFile, string $outputFile): bool {
        $matched = preg_replace('/\/([a-zA-Z0-9-_]+\.d.ts)?$/', '', $outputFile);

        if ($matched !== null && !is_dir($matched)) {
            mkdir($matched, 0777, true);
        }

        return (bool) file_put_contents($outputFile, $convertedFile->toString());
    }
}
