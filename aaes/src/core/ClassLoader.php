<?php
final class ClassLoader
{
	private static $map;

	public static function init($map, $prepend = false)
	{
		self::$map = $map;
		
		spl_autoload_register(array(self, "loadClass"), true, $prepend);
		spl_autoload_register(array(self, "loadController"));
		spl_autoload_register(array(self, "loadModel"));
	}
	
	private static function loadController($className)
	{
		$file = CONTROLLERS . $className . ".php";

		if (file_exists($file)) {
			require_once ($file);
		}
	}

	private static function loadModel($className)
	{
		$file = MODELS . $className . ".php";

		if (file_exists($file)) {
			require_once ($file);
		}
	}

	private static function loadClass($className)
	{
		if (isset(self::$map[$className])) {
			require_once SRC . self::$map[$className] . $className . ".php";
		}
	}
	
	public static function checkController($name)
	{
		return file_exists(CONTROLLERS . $name . "Controller.php");
	}

	public static function getController($name)
	{
		if (self::checkController($name)) {
			$name = $name . "Controller";

			return new $name();
		}

		return false;
	}
	
	public static function checkModel($name)
	{
		return file_exists(MODELS . $name . "Model.php");
	}

	public static function getModel($name)
	{
		if (self::checkModel($name)) {
			$name = $name . "Model";

			return new $name();
		}

		return false;
	}
	
	public static function checkView($name)
	{
		return file_exists(VIEWS . $name . ".php");
	}
	
	public static function getViewSrc($name)
	{
		$name = str_replace(".", "/", $name);
		
		if (!self::checkView($name)) {
			throw new Except("ClassLoader: образец $name не найден");
		}
		
		return VIEWS . $name . ".php";
	}

	public static function getAssetUrl($src)
	{
		$arr = ["#", "//", "mailto:", "tel:", "http://", "https://"];
		$fl = true;

		foreach ($arr as $needle) {
			if (strpos($src, $needle) === 0) {
				$fl = false;
				break;
			}
		}
		
		if ($fl) {
			$src = HOST . $src;
		}

		return $src;
	}
}