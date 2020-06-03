<?php

namespace NamespaceProtector\Command;

use NamespaceProtector\Config;
use NamespaceProtector\Common\FileSystemPath;

final class ConcreteValidateNamespaceCommand extends ValidateNamespaceCommand
{
    public function getConfig(): Config
    {
        return   new Config(
            new FileSystemPath('src'),
            [
                '\Legacy\Controller',
            ],
            [
                '\Facile\Pagamenti\Legacy\ClientPagamentiLegacy',
            ],
            Config::MODE_PUBLIC
        );        
    }

}
