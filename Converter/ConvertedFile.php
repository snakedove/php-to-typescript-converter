<?php


namespace Snakedove\PHPToTypescriptConverter\Converter;


class ConvertedFile
{
    private string $className;
    private string $extends;
    private array $properties;

    public function __construct(
        $className = '',
        $extends = '',
        $properties = []
    )
    {
        $this->className = $className;
        $this->extends = $extends;
        $this->properties = $properties;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function setClassName(string $className): void
    {
        $this->className = $className;
    }

    public function getExtends(): string
    {
        return $this->extends;
    }

    public function setExtends(string $extends): void
    {
        $this->extends = $extends;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function setProperties(array $properties): void
    {
        $this->properties = $properties;
    }

    public function toString(): string {
        $fileString = "/* Generated automatically by snakedove/php-to-typescript-converter */\n\n";
        $fileString .= 'interface ' . $this->getClassName();

        if (!empty($this->getExtends())) {
            $fileString .= ' extends ' . $this->getExtends();
        }

        $fileString .=' {' . "\n";

        foreach ($this->getProperties() as $property) {
            $fileString .= "\t" . $property . "\n";
        }

        $fileString .= "}\n";

        return $fileString;
    }
}
