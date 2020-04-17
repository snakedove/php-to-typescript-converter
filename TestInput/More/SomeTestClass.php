<?php


namespace Snakedove\PHPToTypescriptConverter\TestInput\More;


class SomeTestClass
{
    private ?string $test1;
    public ?int $test2;
    protected ?array $test3;
    private iterable $test4;
    public \DateTime $test5;
    public float $test6;
    protected ?object $test7;
    private SomeTestClass $test8;
    private ?SomeTestClass $test9;

    public function getTest7(): ?object {
        return $this->test7;
    }
}
