<?php

namespace Snakedove\PHPToTypescriptConverter\Service;

class InterfaceCreator {
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

    private string $inputFile;
    private string $outputFile;
    private string $nameSuffix;

    public function __construct($inputFile, $outputFile, $nameSuffix = '')
    {
        $this->inputFile = $inputFile;
        $this->outputFile = $outputFile;
        $this->nameSuffix = $nameSuffix;
    }

    public function run(): string {
        $run = $this->writeInterface($this->parseFile($this->inputFile));
        if ($run) {
            return $this->outputFile . ' created';
        }

        return 'OOPS: could not create ' . $this->outputFile;
    }

    private function getMatch($pattern, $subject): string {
        $matches = null;
        $matched = preg_match($pattern, $subject, $matches);

        if ($matched && !empty($matches) && isset($matches[1])) {
            return $matches[1];
        }

        return '';
    }

    private function getMatches($pattern, $subject): array {
        $matches = null;
        $matched = preg_match_all($pattern, $subject, $matches);

        if ($matched && !empty($matches) && isset($matches[1])) {
            return $matches[1];
        }

        return [];
    }

    private function convertType(string $type): string {
        $convertedType = $type;

        foreach (self::CONVERT_TYPES as $from => $to) {
            if (preg_match($from, $type)) {
                $convertedType = preg_replace($from, $to, $type);
            }
        }

        if (empty($type)) {
            $convertedType = 'any';
        } elseif (!in_array($type, self::INTERNAL_TYPES)) {
            $convertedType .= $this->nameSuffix;
        }

        return $convertedType;
    }

    private function parseFile(string $filePath): ParsedFile {
        $fileContent = file_get_contents($filePath);
        $parsedFile = new ParsedFile();

        $classNameMatch = $this->getMatch(self::MATCH_CLASS_NAME, $fileContent);
        
        if (!empty($classNameMatch)) {
            $parsedFile->setClassName($classNameMatch . $this->nameSuffix);
        }

        $extendsMatch = $this->getMatch(self::MATCH_EXTENDS, $fileContent);
        
        if (!empty($extendsMatch)) {
            $parsedFile->setExtends($extendsMatch . $this->nameSuffix);
        }

        $propsMatch = $this->getMatches(self::MATCH_PROPERTIES, $fileContent);
        $classProps = [];

        foreach ($propsMatch as $prop) {
            $propParts = explode(' ', $prop);
            $name = $propParts[0];
            $type = '';

            if (count($propParts) === 2) {
                $type = $propParts[0];
                $name = $propParts[1];
            }

            $name = str_replace('$', '', $name);
            $type = preg_replace('/\?/', '', $type);
            $type = $this->convertType($type);
            $newProp = $name  . ': ' . $type . ';';
            array_push($classProps, $newProp);
        }

        if (!empty($classProps)) {
            $parsedFile->setProperties($classProps);
        }

        return $parsedFile;
    }

    private function writeInterface(ParsedFile $parsedFile): bool {
        return (bool) file_put_contents($this->outputFile, $parsedFile->toString());
    }
}
