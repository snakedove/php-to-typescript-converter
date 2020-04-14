# README #

Thanks for using snakedove/php-to-typescript-converter!

### What is this repository for? ###

* Adds a command to your symfony project with which you can convert plain old php objects e.g. DTOs to TypeScript Interfaces.

### Usage ###
`bin/console ts-create <Source File> <Destination Directory>`
* The destination directory should already exist!

### Prerequisite ###
* Works for PHP 7.4 POPOs only
* Works from Symfony 4

### Configuration ###

* add the following configuration to your `services.yaml`: 
```
Snakedove\PHPToTypescriptConverter\Command\PhpToTypescriptCommand:
    tags: ['console.command']
```
* optional: pass the argument `$nameSuffix` to the command, e.g. 'Interface'. This will add 'Interface' to all file and interface names.
```
Snakedove\PHPToTypescriptConverter\Command\PhpToTypescriptCommand:
    tags: ['console.command']
    arguments:
        $nameSuffix: Interface
```
* Now, the command ts-create should show up when using `bin/console list`
