<?php declare(strict_types=1);

namespace Personal;

use inpublic\entries\ns;

class First
{
    public function foo(): void
    {
        $bar = new \inpublic\entries\NS();
        $barConst = \inpublic\entries\ns::SOME_VALUE;
    }
}
