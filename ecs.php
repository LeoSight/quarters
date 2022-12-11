<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $config): void
{
    $services = $config->services();
    $services->set(ArraySyntaxFixer::class)
        ->call('configure', [[
            'syntax' => 'short',
        ]]);

    // run, fix and commit, one by one
	$config->import(SetList::PSR_12);
	$config->import(SetList::SYMPLIFY);
	$config->import(SetList::COMMON);
	$config->import(SetList::CLEAN_CODE);

	$config->cacheDirectory('.ecs_cache');
	$config->lineEnding("\n");
};
