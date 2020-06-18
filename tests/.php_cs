<?php

return PhpCsFixer\Config::create()
    ->setRules([
        '@PhpCsFixer' => true,
        'php_unit_internal_class' => false,
        'php_unit_test_class_requires_covers' => false,
    ])
;