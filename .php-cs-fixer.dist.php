<?php

$finder = PhpCsFixer\Finder::create()->in([__DIR__.'/src', __DIR__.'/tests']);

return (new PhpCsFixer\Config())->setRules([
    '@PSR12' => true,
    '@PhpCsFixer' => true,
    'array_syntax' => ['syntax' => 'short'],
    'php_unit_test_class_requires_covers' => false,
])->setFinder($finder);
