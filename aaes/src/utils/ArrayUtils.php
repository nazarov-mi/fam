<?php
final class ArrayUtils
{
	/**
	 * Выполняет поиск значения в колонках массива
	 * @param needle   - искомое значение
	 * @param haystack - массив
	 * @param name     - имя колонки
	 * @param stricct  - стоит ли применять строгое сравнение (===)
	 * @return Возвращает индекс вложенного массива или false в случае неудачи 
	 */
	public static function colSearch($haystack, $needle, $name, $strict = false)
	{
		if (!is_array($haystack)) return false;
		
		foreach ($haystack as $key => $item) {
			
			if (!is_array($item)) continue;
			
			if (
				(!$strict && $item[$name] ==  $needle) ||
				( $strict && $item[$name] === $needle)
			) {
				return $key;
			}
		}
		
		return false;
	}

	/**
	 * Возвращает колонку массива
	 * @param haystack - массив
	 * @param name - название колонки
	 * @param key - название колонки для значений ключа
	 * @param Возвращает массив или false в случае неудачи 
	 */
	public static function column($haystack, $name, $key = null)
	{
		if (!is_array($haystack)) return false;
		
		$res = array();
		
		foreach ($haystack as $value) {
			if ($key && isset($value[$key])) {
				$res[$value[$key]] = $value[$name];
			} else {
				$res[] = $value[$name];
			}
		}
		
		return $res;
	}

	/**
	 * Удаляет элемент из массива
	 * @param haystack - массив
	 * @param needle - элемент, который необходимо удалить
	 * @return true/false
	 */
	public static function remove(&$haystack, $needle)
	{
		if (!is_array($haystack)) return false;
		
		$k = array_search($needle, $haystack);
		
		if ($k !== false) {
			unset($haystack[$k]);
			return true;
		}
		
		return false;
	}
}