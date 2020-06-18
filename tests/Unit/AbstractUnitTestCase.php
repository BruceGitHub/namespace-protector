<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

abstract class AbstractUnitTestCase extends TestCase
{
    protected function getVirtualFileSystem()
    {
        $directory = [
        'json' => [
          'composer.json' => '{
            "autoload": {
              "psr-4": {
              }          
            }
          }',
          'namespace-protector-config.json' => '{
                 "version": "0.1.0",
                 "start-path": ".",
                 "composer-json-path":".",
                 "public-entries": [

                 ],
                 "private-entries": [

                 ],
                 "mode": "MODE_MAKE_VENDOR_PRIVATE"
          }',
         ],
        'files' =>[
          'first.php' => '<?php 
            namespace Personal;                     
            use org\bovigo\vfs\vfsStream;
            class First {
            }
          ',
          'no_violation.php' => '<?php 
            namespace Personal;                     
            class NoViolation {
            }
          ',
        ]
      ];
 
        return vfsStream::setup('root', 777, $directory);
    }
}
