# README #

Thanks for using snakedove/php-to-typescript-converter!

### What is this repository for? ###

* Adds a command to your symfony project with which you can convert plain old php objects e.g. DTOs to TypeScript Interfaces.

### Usage ###
`bin/console ts-create <Source File> <Destination Directory>`
* The destination directory should already exist!
* Nullable types will not convert to `type | null` because all types are nullable by default in TypeScript.

### Prerequisite ###
* Works for PHP 7.4 POPOs only
* Works from Symfony 4

### Configuration ###

* add the following configuration to your `services.yaml`: 
```
Snakedove\PHPToTypescriptConverter\Command\PhpToTypescriptCommand:
    tags: ['console.command']
```
* Now, the command ts-create should show up when using `bin/console list`.

Optional:
* pass argument `$nameSuffix` to the command, e.g. 'Interface'. This will add 'Interface' to all file and interface names.
* pass argument `convertCollection` to the command. Set to false, this will include the collection as an own InterfaceType. Per default, collections will be converted to `CollectionType[]`, and Collection classes will not be converted.
```
Snakedove\PHPToTypescriptConverter\Command\PhpToTypescriptCommand:
    tags: ['console.command']
    arguments:
        $nameSuffix: Interface
        $convertCollections: false
```
