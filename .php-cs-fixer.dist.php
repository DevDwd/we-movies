<?php

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests'
    ])
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => true,
        'no_unused_imports' => true,
        'declare_strict_types' => true,
        'strict_param' => true,
        'void_return' => true,
        'single_line_throw' => true,
        'no_extra_blank_lines' => true,
        'trim_array_spaces' => true,
        'indentation_type' => true,
    ])
    ->setFinder($finder)
;
