# namespace-protector
A tool to validate namespace

[![Latest Stable Version](https://poser.pugx.org/brucegithub/namespace-protector/v)](//packagist.org/packages/brucegithub/namespace-protector) [![Latest Unstable Version](https://poser.pugx.org/brucegithub/namespace-protector/v/unstable)](//packagist.org/packages/brucegithub/namespace-protector) [![License](https://poser.pugx.org/brucegithub/namespace-protector/license)](//packagist.org/packages/brucegithub/namespace-protector) [![Build Status](https://travis-ci.org/BruceGitHub/namespace-protector.svg?branch=master)](https://travis-ci.org/BruceGitHub/namespace-protector) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/BruceGitHub/namespace-protector/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/BruceGitHub/namespace-protector/?branch=master) [![codecov](https://codecov.io/gh/BruceGitHub/namespace-protector/branch/master/graph/badge.svg)](https://codecov.io/gh/BruceGitHub/namespace-protector)

# Ispired by 

- https://www.slideshare.net/MicheleOrselli/comunicare-condividere-e-mantenere-decisioni-architetturali-nei-team-di-sviluppo-approcci-e-strumenti
- https://docs.microsoft.com/en-us/dotnet/csharp/programming-guide/classes-and-structs/access-modifiers#:~:text=Class%20members%2C%20including%20nested%20classes,from%20outside%20the%20containing%20type. 
- https://wiki.php.net/rfc/namespace-visibility

and for fun ...

# Motivation 

Allow to improve the information hiding at level of namespace Like C#/Java pubblic/private class. 
The idea is that namespace in some situations can be private at all except for a specific entry point. 
For example the namespace of third parts lib. 

# The Design of this project follows this rules 

- No NULL 
- No instanceof 
- No switch
- No static

## Nice to have 
- Minimize the @var annotation 

# todo
### 10/10/2021
- [] Review the db namespace 
- [] Remove psalm-suppress
- [] Remove psalm minor issues 

### Waiting
- [] Adds mode DISCOVER_CONFIG, so for each lib that in the `extra` node of composer.json declares the visibility settings of the namespace lib, it can perform the validity in autonomy

### Done
- [x] Adds command to build conflicts graph 

# Minimal config 

Trought the json configuration it's possible define 

```json
{
  "version": "0.1.0",
  "start-path": "src",
  "composer-json-path": "./",
  "public-entries": [],
  "private-entries": [],
  "mode": "MODE_MAKE_VENDOR_PRIVATE"
}

```
# Fast because each ast it's cached and reused until the target file change

- mode `public` default mode, in this setup only a private namespace it's validated
- mode `private vendor` in which each access of vendor namespace trigger a violation if was not added public namespace.

# Install and Run 

## with Composer 
`composer require --dev brucegithub/namespace-protector`
[![asciicast](https://asciinema.org/a/411325.svg)](https://asciinema.org/a/411325)

## with Phar 
Adds this to composer.json
```json
"repositories": [{
  "type": "vcs",
  "url": "https://github.com/brucegithub/namespace-protector-phar.git"
}],
```
composer require --dev brucegithub/namespace-protector-phar --no-cache "dev-main"
[![asciicast](https://asciinema.org/a/411326.svg)](https://asciinema.org/a/411326)

## with Docker 
```
docker run  --rm -it -v $(pwd):/namespace-protector brucedockerhub/namespace-protector:0.1.0 create-config
docker run  --rm -it -v $(pwd):/namespace-protector brucedockerhub/namespace-protector:0.1.0 validate-namespace
```

## setup 
`vendor/bin/namespace-protector create-config`

## run 
`vendor/bin/namespace-protector validate-namespace`

```bash
➜  namespace-protector git:(master) ✗ vendor/bin/namespace-protector validate-namespace
|Dump config:
|> Version: 0.1.0
|> Cache: FALSE
|> Plotter: plotter-terminal
|> Path start: tests/Stub/RealProject/src
|> Composer Json path: ./
|> Mode: PUBLIC
|> Private entries:
|       >NamespaceProtector\Common\
|       >NamespaceProtector\Scanner\
|       >PhpParser

|
|> Public entries:


Load data...
Loaded 3 files to validate
Loaded 5031 built in symbols
Start analysis...

Processed file: tests/Stub/RealProject/src/NamespaceProtectorProcessorFactory.php
	 > ERROR Line: 7 of use PhpParser\NodeTraverser
	 > ERROR Line: 8 of use PhpParser\ParserFactory
	 > ERROR Line: 14 of use NamespaceProtector\Scanner\ComposerJson
	 > ERROR Line: 16 of use NamespaceProtector\Common\FileSystemPath
	 > ERROR Line: 19 of use NamespaceProtector\Scanner\FileSystemScanner

Processed file: tests/Stub/RealProject/src/Analyser.php
	 > ERROR Line: 8 of use NamespaceProtector\Common\PathInterface

Processed file: tests/Stub/RealProject/src/EnvironmentDataLoader.php
	 > ERROR Line: 8 of use NamespaceProtector\Scanner\ComposerJson
Total files: 3
Total errors: 7
Elapsed time: 0.68148
```

```bash 
Example of output in png format.
➜  vendord/bin/namespace-protector validate-namespace plotter-png
```
![example output](https://github.com/BruceGitHub/namespace-protector/blob/master/img/example_output.png)


For now it is a lab but...
