<?php
declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void
{
    $rectorConfig->phpstanConfig(__DIR__ . '/phpstan.neon');

    $rectorConfig->paths(
		[
			__DIR__ . '/src',
			__DIR__ . '/tests'
		]
	);

    $rectorConfig->importNames(true);

    // here we can define, what sets of rules will be applied
    // tip: use "SetList" class to autocomplete sets
    $rectorConfig->sets([
        SetList::CODE_QUALITY,
        SetList::PHP_81,
        SetList::DEAD_CODE,
        SetList::TYPE_DECLARATION,
		SetList::PRIVATIZATION
    ]);
};
