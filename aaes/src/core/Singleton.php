<?php
abstract class Singleton
{
	private static $instances = array();

	public static function getInstance($class)
	{
		if (class_exists($class)) {
			if (!isset(self::$instances[$class])) {
				self::$instances[$class] = new $class();
			}
			return self::$instances[$class];
		}
		return false;
	}

	public static function gi()
	{
		$class = get_called_class();
		return self::getInstance($class);
	}

	private function __construct() {}
	final private function __clone() {}
}