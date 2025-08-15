<?php

use Rector\Config\RectorConfig;
use Rector\Doctrine\Set\DoctrineSetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/../config',
        __DIR__.'/../migrations',
        __DIR__.'/../src',
        __DIR__.'/../tests',
    ])
    ->withSkip([
        __DIR__.'/../config/bootstrap.php',
        __DIR__.'/../config/bundles.php',
        __DIR__.'/../config/preload.php',
        __DIR__.'/../public/index.php',
        __DIR__.'/../src/Kernel.php',
        __DIR__.'/../tests/bootstrap.php',
    ])
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        privatization: true,
        naming: true,
        earlyReturn: true,
        rectorPreset: true,
    )
    ->withSets([
        DoctrineSetList::ANNOTATIONS_TO_ATTRIBUTES,
        DoctrineSetList::DOCTRINE_CODE_QUALITY,
    ])
    ->withComposerBased(symfony: true)
    ->withAttributesSets(symfony: true)
    ->withPhpSets(php84: true)
;
