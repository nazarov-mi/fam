<?php
class View
{
	private static $lib;
	private static $data                  = array();
	private static $buffer_content_type   = array();
	private static $included_modules      = array();
	private static $included_files        = array();
	private static $included_views = [];
	private static $head;
	
	private static $curr_name;

	public static function init($lib = [])
	{
		if (!isset($lib)) {
			throw new Except("View: библиотека модулей не подключена");
		}
		
		self::$lib = $lib;
		self::removeAll();
	}
	
	/**
	 * Начинает запись секции
	 * @param $name - название секции
	 */
	public static function section($name)
	{
		self::$curr_name = $name;
		ob_start(array(self, "setSection"));
	}
	
	/**
	 * Заканчивает запись секции
	 */
	public static function endSection()
	{
		ob_end_clean();
	}
	
	/**
	 * Подключает view
	 * @param $name - имя view
	 */
	public static function inc($name, $unique = false)
	{
		if (!in_array($name, self::$included_views)) {
			self::$included_views[] = $name;
		} else
		if ($unique) {
			return false;
		}

		include ClassLoader::getViewSrc($name);

		return true;
	}
	
	/**
	 * Выводит view со всеми зависимостями и метками
	 * @param $name - имя view
	 */
	public static function show($name)
	{
		ob_start();
		self::inc($name);
		$content = ob_get_clean();
		$content = self::parseContent($content);

		return new Response($content);
	}
	
	/**
	 * Заменяет все метки на данные
	 * @param $content
	 */
	private static function parseContent($content)
	{
		$pattern = "/\{{(@?[a-z0-9_-]+)\}}/i";
		return preg_replace_callback($pattern, array(self, "replace"), $content);
	}

	/**
	 * Callback функция для обработки меток
	 * @param data - результат поиска фукции preg_replace_callback
	 * @return Возвращает текст для замены метки
	 */
	private static function replace($data)
	{
		$name = $data[1];
		$callback = self::$buffer_content_type[$name];
		
		if (isset($callback)) {
			return call_user_func($callback, $name);
		}
		
		return "";
	}
	
	/**
	 * Закрепляет callback функцию за конкретной меткой
	 * @param name - название метки
	 * @param callback - callback функция
	 */
	private static function addCallback($name, $callback)
	{
		self::$buffer_content_type[$name] = $callback;
	}
	
	/**
	 * Удаляет данные для замены метки и callback
	 * @param name - название метки или массив
	 */
	public static function remove($name)
	{
		if (is_array($name)) {
			foreach ($name as $item) {
				unset(self::$data[$item]);
				unset(self::$buffer_content_type[$item]);
			}
		} else {
			unset(self::$data[$name]);
			unset(self::$buffer_content_type[$name]);
		}
	}
	
	/**
	 * Удаляет все данные для замены меток и callback'и, кроме head
	 */
	public static function removeAll()
	{
		array_splice(self::$data, 0);
		array_splice(self::$buffer_content_type, 0);
		self::addCallback("head", array(self, "getHeadData"));
	}
	
	/**
	 * Устанавливает данные для замены метки
	 * @param name - название метки
	 * @param value - данные для замены
	 */
	public static function set($name, $value)
	{
		self::addCallback($name, array(self, "get"));
		self::$data[$name] = $value;
	}

	/**
	 * Устанавливает данные для замены меток
	 * @param array - массив (название метки => данные для замены)
	 */
	public static function setArray($array, $prefix = "")
	{
		if (is_array($array)) {
			foreach ($array as $name => $value) {
				self::set($prefix . $name, $value);
			}
		}
	}
	
	/**
	 * Возвращает данные для замены метки
	 * @param name - название метки
	 */
	public static function get($name)
	{
		return self::$data[$name];
	}

	/**
	 * Устанавливает заголовок страницы
	 * @param title - заголовок страницы
	 */
	public static function setTitle($title)
	{
		self::set("title", $title);
	}
	
	/**
	 * Устанавливает путь к файлу для замены метки
	 * @param name - название метки
	 * @param view - адрес файла
	 */
	public static function setView($name, $view)
	{
		if (!isset(self::$data[$name])) {
			self::addCallback($name, array(self, "getViewData"));
		}
		
		self::$data[$name] = $view;
	}
	
	/**
	 * Загружает образец по названию метки и обрабатывает все вложенные метки
	 * @param name - название метки
	 * @return Возвращает обработанный код страницы
	 */
	private static function getViewData($mark)
	{
		$name = self::$data[$mark];
		$content = self::inc($name);
		
		if (isset($content)) {
			return self::parseContent($content);
		}
		
		return "";
	}
	
	/**
	 * Устанавливает данные для замены метки
	 * @param buffer - данные секции для замены
	 */
	private static function setSection($buffer)
	{
		$name = "@" . self::$curr_name;
		
		if (!isset(self::$data[$name])) {
			self::addCallback($name, array(self, "getSectionData"));
		}
		
		self::$data[$name] = $buffer;
	}
	
	/**
	 * Обрабатывает и возвращает данные секции
	 */
	private static function getSectionData($name)
	{
		$content = self::$data[$name];
		
		if (!empty($content)) {
			return self::parseContent($content);
		}
		
		return "";
	}

	/**
	 * Callback
	 * Возвращает данные head
	 */
	public static function getHeadData()
	{
		$modules = [];
		
		foreach (self::$included_modules as $name) {
			self::getDepends($modules, $name);
		}
		
		$files = [];
		
		foreach ($modules as $name) {
			$module = self::$lib[$name];

			$assets = $module["assets"];
			if (!empty($assets)) {
				if (is_array($assets)) {
					foreach ($assets as $src) {
						$files[] = $src;
					}
				} else {
					$files[] = $assets;
				}
			} else {
				throw new Except("View: в модуле $name не указаны подключаемые файлы");
			}
		}

		foreach (self::$included_files as $src) {
			$files[] = $src;
		}

		$headData = "";

		foreach ($files as $src) {
			if (stripos($src, ".js") !== false) {
				$headData .= "<script type=\"text/javascript\" src=\"" . $src . "\"></script>";
			} elseif (stripos($src, ".css") !== false) {
				$headData .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $src . "\" />";
			} else {
				throw new Except("View: неизвестный тип файла $src");
			}
		}

		return $headData;
	}
	
	/**
	 * Возвращает зависимости модуля
	 * Функция работает рекурсивно и записывает результат в массив, переданный в качестве первого аргумента
	 * @param modules - массив подключенных модулей
	 * @param name - имя модуля
	 */
	private static function getDepends(&$modules, $name)
	{
		if (in_array($name, $modules)) return;

		$module = self::$lib[$name];
		if (empty($module)) {
			throw new Except("View: модуль $name не найден");
		}

		$depends = $module["depends"];
		if (!empty($depends)) {
			if (is_array($depends)) {
				foreach ($depends as $depend) {
					self::getDepends($modules, $depend);
				}
			} else {
				self::getDepends($modules, $depends);
			}
		}

		$modules[] = $name;
	}
	
	/**
	 * Подключает файл
	 * @param name - имя файла
	 * @param src - ссылка на файл (.js, .css)
	 */
	public static function addFile($name, $src)
	{
		$src = ClassLoader::getAssetUrl($src);

		if (!isset(self::$included_files[$name])) {
			self::$included_files[$name] = $src;
		}
	}

	/**
	 * Подключает файлы
	 * @param arr - массив (название файла => ссылка на файл (.js, .css))
	 */
	public static function addFiles(array $arr)
	{
		foreach ($arr as $name => $src) {
			self::addFile($name, $src);
		}
	}

	/**
	 * Возвращает ссылку на файл
	 * @param name - имя файла
	 */
	public static function getFileSrc($name)
	{
		return self::$included_files[$name];
	}

	/**
	 * Отключает файл
	 * @param name - название файла
	 */
	public static function removeFile($name)
	{
		ArrayUtils::remove(self::$included_files, $name);
	}

	/**
	 * Отключает файлы
	 * @param arr - массив названий
	 */
	public static function removeFiles(array $arr)
	{
		foreach ($arr as $name) {
			self::removeFile($name);
		}
	}

	/**
	 * Подключает модуль
	 * @param name - название модуля
	 */
	public static function addModule($name)
	{
		if (!in_array($name, self::$included_modules)) {
			self::$included_modules[] = $name;
		}
	}

	/**
	 * Подключает модули
	 * @param arr - массив названий
	 */
	public static function addModules(array $arr)
	{
		foreach ($arr as $name) {
			self::addModule($name);
		}
	}
	
	/**
	 * Отключает модуль
	 * @param name - название модуля
	 */
	public static function removeModule($name)
	{
		ArrayUtils::remove($this->included_modules, $name);
	}
	
	/**
	 * Отключает модули
	 * @param arr - массив названий
	 */
	public static function removeModules(array $arr)
	{
		foreach ($arr as $name) {
			self::removeModule($name);
		}
	}

	/**
	 * Отключает все модули
	 */
	public static function removeAllModules()
	{
		array_splice($this->included_modules, 0);
	}
}