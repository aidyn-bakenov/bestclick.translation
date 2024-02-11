<?php
/**
 * Created by Aidyn Bakenov
 * Email: aidyn.bakenov@yandex.kz
 * 09.09.2023 21:48
 */

namespace Bestclick\HighloadBlock;


use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\Loader;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Entity;
use Bitrix\Main\ORM\EntityError;
use Bitrix\Main\ORM\Event;
use Bitrix\Main\ORM\EventResult;
use Bitrix\Main\SystemException;
use Bestclick\Localization\Loc;
use BestclickTranslationTable;

Loader::includeModule('highloadblock');

$hlblockName = 'BestclickTranslation';
$hlblock = HighloadBlockTable::getList(['filter' => ['=NAME' => $hlblockName], 'select' => ['ID', 'NAME', 'TABLE_NAME']])->fetch();
HighloadBlockTable::compileEntity($hlblock);

define('HLBLOCK_BESTCLICK_TRANSLATION_ID', (int)$hlblock['ID']);

class TranslationTable extends BestclickTranslationTable implements IHighloadBlock
{
	use HighloadBlockTrait;

	#region Методы для работы с highload-блоком

	public static function getBlockId(): int
	{
		return HLBLOCK_BESTCLICK_TRANSLATION_ID;
	}

	public static function getEntity(): Entity
	{
		return Base::getInstance('BestclickTranslationTable');
	}

	#endregion

	#region Обработчики

	/**
	 * Обработчик onBeforeAdd
	 *
	 * @param Event $event
	 * @return EventResult
	 * @throws ArgumentException
	 * @throws ObjectPropertyException
	 * @throws SystemException
	 */
	public static function onBeforeAdd(Event $event): EventResult
	{
		$arFields = $event->getParameter('fields');

		$result = new EventResult();

		#region Проверка на уникальность символьного кода
		$rs = static::getList([
			'filter' => [
				'=UF_CODE' => $arFields['UF_CODE'],
			],
			'select' => [
				'ID',
			],
		]);
		if ($rs->fetch())
		{
			$result->addError(new EntityError(Loc::getMessage('BESTCLICK_TRANSLATION_TABLE_CODE_IS_ALREADY_EXISTS')));
		}
		#endregion

		return $result;
	}

	/**
	 * @param Event $event
	 * @return void
	 */
	public static function clearCache(Event $event): void
	{
		$event->getEntity()->cleanCache();
	}

	#endregion
}