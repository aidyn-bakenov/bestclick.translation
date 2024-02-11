<?php

use Bitrix\Main\Loader;

$moduleId = 'bestclick.translation';

Loader::registerAutoLoadClasses($moduleId, [
	'Bestclick\HighloadBlock\TranslationTable' => 'lib/Bestclick/HighloadBlock/TranslationTable.php',
	'Bestclick\Localization\Loc' => 'lib/Bestclick/Localization/Loc.php',
]);