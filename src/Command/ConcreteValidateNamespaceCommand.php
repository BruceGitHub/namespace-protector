<?php

namespace App\Command;

use App\Config;
use App\Common\FileSystemPath;

final class ConcreteValidateNamespaceCommand extends ValidateNamespaceCommand
{
    public function getConfig(): Config
    {
        return   new Config(
            new FileSystemPath('.'), 
            [
                'L2',
                'L1',
                'PhpParser\Builder',
                'PhpParser\Node\Stmt'
            ]
        );        
    }

}
