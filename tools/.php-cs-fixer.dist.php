<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__.'/../migrations')
    ->in(__DIR__.'/../src')
    ->notPath('Kernel.php')
    ->in(__DIR__.'/../tests')
    ->notPath('bootstrap.php')
    ->exclude('var')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'declare_strict_types' => true,
        'global_namespace_import' => true,
    ])
    ->setFinder($finder)
    ->setCacheFile(__DIR__.'/.php-cs-fixer.cache')
;
