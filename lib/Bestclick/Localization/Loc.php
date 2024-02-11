<?php
/**
 * Created by Aidyn Bakenov
 * Email: aidyn.bakenov@yandex.kz
 * 09.09.2023 21:59
 */

namespace Bestclick\Localization;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bestclick\HighloadBlock\TranslationTable;
use CUtil;

class Loc
{
	#region Свойства

	private static array $messages = [];

	#endregion

	#region Подключение

	/**
	 * Сохраняет сообщения
	 *
	 * @return void
	 * @throws ArgumentException
	 * @throws ObjectPropertyException
	 * @throws SystemException
	 */
	public static function loadMessages(): void
	{
		static::$messages = self::getFromCache();
		if (empty(static::$messages))
		{
			static::$messages = self::getFromDatabase();
		}
	}

	/**
	 * Возвращает сообщения из кэша
	 *
	 * @return array
	 */
	private static function getFromCache(): array
	{
		return [];
	}

	/**
	 * Возвращает сообщения из базы данных
	 *
	 * @return array
	 * @throws ArgumentException
	 * @throws ObjectPropertyException
	 * @throws SystemException
	 */
	private static function getFromDatabase(): array
	{
		$messages = [];

		$rs = TranslationTable::getList([
			'select' => [
				'UF_CODE',
				'ru' => 'UF_RU',
				'kk' => 'UF_KK',
				'en' => 'UF_EN',
			],
			'cache' => [
				'ttl' => 3600,
			],
		])->fetchAll();
		foreach ($rs as $ob)
		{
			$code = (string)$ob['UF_CODE'];
			$messages['ru'][$code] = (string)$ob['ru'];
			$messages['kk'][$code] = (string)$ob['kk'];
			$messages['en'][$code] = (string)$ob['en'];
		}

		return $messages;
	}

	#endregion

	#region Сообщение

	/**
	 * Возвращает сообщение
	 *
	 * @param string $code
	 * @param array|null $replace
	 * @param string|null $lang
	 * @return string
	 */
	public static function getMessage(string $code, ?array $replace = null, ?string $lang = null): string
	{
		$lang = $lang ?? LANGUAGE_ID;
		$message = (string)static::$messages[$lang][$code];
		if (is_array($replace))
		{
			$message = str_replace(array_keys($replace), array_values($replace), $message);
		}
		return $message;
	}

	/**
	 * Возвращает сообщение для вывода в javascript
	 *
	 * @param string $code
	 * @param array|null $replace
	 * @param string|null $lang
	 * @return string
	 */
	public static function getMessageJs(string $code, ?array $replace = null, ?string $lang = null): string
	{
		return CUtil::JSEscape(static::getMessage($code, $replace, $lang));
	}

	/**
	 * Сохраняет новое сообщение
	 *
	 * @param string $code
	 * @param string $ru
	 * @param string $kk
	 * @param string $en
	 * @return void
	 * @throws SystemException
	 */
	public static function add(string $code, string $ru = '', string $kk = '', string $en = ''): void
	{
		TranslationTable::add([
			'UF_CODE' => $code,
			'UF_RU' => $ru,
			'UF_KK' => $kk,
			'UF_EN' => $en,
		]);
	}

	#endregion
}