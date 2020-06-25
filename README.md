# namespace-protector
A tool to validate namespace

[![Latest Stable Version](https://poser.pugx.org/brucegithub/namespace-protector/v)](//packagist.org/packages/brucegithub/namespace-protector) [![Total Downloads](https://poser.pugx.org/brucegithub/namespace-protector/downloads)](//packagist.org/packages/brucegithub/namespace-protector) [![Latest Unstable Version](https://poser.pugx.org/brucegithub/namespace-protector/v/unstable)](//packagist.org/packages/brucegithub/namespace-protector) [![License](https://poser.pugx.org/brucegithub/namespace-protector/license)](//packagist.org/packages/brucegithub/namespace-protector) [![Build Status](https://travis-ci.org/BruceGitHub/namespace-protector.svg?branch=master)](https://travis-ci.org/BruceGitHub/namespace-protector) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/BruceGitHub/namespace-protector/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/BruceGitHub/namespace-protector/?branch=master) [![codecov](https://codecov.io/gh/BruceGitHub/namespace-protector/branch/master/graph/badge.svg)](https://codecov.io/gh/BruceGitHub/namespace-protector)

# Ispired by 

- https://www.slideshare.net/MicheleOrselli/comunicare-condividere-e-mantenere-decisioni-architetturali-nei-team-di-sviluppo-approcci-e-strumenti
- https://docs.microsoft.com/en-us/dotnet/csharp/programming-guide/classes-and-structs/access-modifiers#:~:text=Class%20members%2C%20including%20nested%20classes,from%20outside%20the%20containing%20type. 
- https://wiki.php.net/rfc/namespace-visibility

and for fun ...

# Motivation 

Allow to improve the information hiding at level of namespace Like C#/Java pubblic/private class. 
The idea is that namespace in some situations can be private at all except for a specific entry point. 
For example the namespace of third parts lib. 

# todo
- Adds mode DISCOVER_CONFIG, so for each lib that in the `extra` node of composer.json declares the visibility settings of the namespace lib, it can perform the validity in autonomy

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
I think thta the in future the modes can be increase

# Install and Run 

## with composer 
`composer require --dev brucegithub/namespace-protector`

## setup 
`bin/namespace-protector create-config`

## run 
```bash
➜  namespace-protector git:(master) ✗ bin/namespace-protector validate-namespace
Boot validate analysis....

|Dump config:
|> Version: 0.1.0
|> Path start: src
|> Composer Json path: ./
|> Mode: MODE_MAKE_VENDOR_PRIVATE
|> Private entries:

|
|> Public entries:


Load data....
Loaded 30 files to validate
Loaded 5097 built in symbols
Start analysis...
Process file: src/Cache/SimpleFileCache.php
	 > ERROR Line: 18 of use \safe\mkdir
	 > ERROR Line: 29 of use \PhpParser\JsonDecoder
	 > ERROR Line: 31 of use \safe\file_get_contents
Process file: src/Config/ConfigTemplateCreator.php
	 > ERROR Line: 32 of use \safe\file_get_contents
	 > ERROR Line: 33 of use \safe\file_put_contents
Process file: src/Config/Config.php
	 > ERROR Line: 118 of use \safe\file_get_contents
	 > ERROR Line: 119 of use \safe\json_decode
Process file: src/Parser/Node/PhpNode.php
	 > ERROR Line: 5 of use PhpParser\Node
	 > ERROR Line: 6 of use PhpParser\Node\Stmt\UseUse
	 > ERROR Line: 9 of use PhpParser\Node\Name\FullyQualified
	 > ERROR Line: 20 of use Safe\strtotime
Process file: src/Scanner/ComposerJson.php
	 > ERROR Line: 11 of use Safe\realpath
	 > ERROR Line: 44 of use \safe\realpath
	 > ERROR Line: 51 of use \safe\json_decode
	 > ERROR Line: 52 of use \safe\file_get_contents
	 > ERROR Line: 66 of use \safe\file_get_contents
	 > ERROR Line: 68 of use \safe\json_decode
Total errors: 17
Elapsed time: 0.4248
```

For now it is a lab but...
