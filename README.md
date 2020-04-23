# README #

Thanks for using `snakedove/php-to-typescript-converter`!

### What is this repository for? ###

* Adds a command to your symfony project with which you can convert plain old php objects e.g. DTOs to TypeScript Interfaces.

### Usage ###
`bin/console ts-create <Source File> <Destination Directory>` creates a Typescript Interface.d.ts file for a given source .php file.

`bin/console ts-create-all <Source Directory> <Destination Directory>` creates Typescript Interface files for all source .php files contained in the given directory and its sub-directories, recursively.

* Nullable types will not convert to `type | null` because all types are nullable by default in TypeScript.

### Prerequisites ###
* Works for PHP 7.4 POPOs only
* Works from Symfony 4

### Configuration ###

* add one or both of the following configuration to your `services.yaml`:

```
Snakedove\PHPToTypescriptConverter\Command\PhpToTypescriptCommand:
    tags: ['console.command']
```
* Now, the command `ts-create` should show up when using `bin/console list`.

```
Snakedove\PHPToTypescriptConverter\Command\PhpToTypescriptAllCommand:
    tags: ['console.command']
```
* Now, the command `ts-create-all` should show up when using `bin/console list`.

Optional:
* pass argument `$nameSuffix` to the command, e.g. 'Interface'. This will add 'Interface' to all file and interface names.
* pass argument `$convertCollection` to the command. Set to false, this will include the collection as an own InterfaceType. Per default, collections will be converted to `CollectionType[]`, and Collection classes will not be converted.

Example configuration
```
Snakedove\PHPToTypescriptConverter\Command\PhpToTypescriptCommand:
    tags: ['console.command']
    arguments:
        $nameSuffix: Interface
        $convertCollections: true
```
### Caveats ###
* Does currently not support fluent interfaces
