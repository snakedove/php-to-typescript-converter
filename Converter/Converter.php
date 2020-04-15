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

    private string $inputFile;
    private string $outputFile;
    private string $nameSuffix;
    private bool $convertCollections;

    /**
     * Converter constructor.
     * @param $inputFile
     * @param $outputFile
     * @param $nameSuffix
     * @param $convertCollections
     */
    public function __construct(
        $inputFile,
        $outputFile,
        $nameSuffix,
        $convertCollections
    )
    {
        $this->inputFile = $inputFile;
        $this->outputFile = $outputFile;
        $this->nameSuffix = $nameSuffix;
        $this->convertCollections = $convertCollections;
    }

    /**
     * @return string
     */
    public function convert(): string {
        $converted = $this->writeInterface($this->convertFile($this->inputFile));

        if ($converted) {
            return $this->outputFile . ' created';
        }

        return 'OOPS: could not create ' . $this->outputFile;
    }

    /**
     * @param string $pattern
     * @param string $subject
     * @return string
     */
    private function getMatch(string $pattern, string $subject): string {
        $matches = null;
        $matched = preg_match($pattern, $subject, $matches);

        if ($matched && !empty($matches) && isset($matches[1])) {
            return $matches[1];
        }

        return '';
    }

    /**
     * @param string $pattern
     * @param string $subject
     * @return array
     */
    private function getMatches(string $pattern, string $subject): array {
        $matches = null;
        $matched = preg_match_all($pattern, $subject, $matches);

        if ($matched && !empty($matches) && isset($matches[1])) {
            return $matches[1];
        }

        return [];
    }

    /**
     * @param array $props
     * @return array
     */
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

    /**
     * @param string $type
     * @return string
     */
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

    /**
     * @param string $filePath
     * @return ConvertedFile
     */
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

    /**
     * @param ConvertedFile $convertedFile
     * @return bool
     */
    private function writeInterface(ConvertedFile $convertedFile): bool {
        return (bool) file_put_contents($this->outputFile, $convertedFile->toString());
    }
}
