<?php
class Unique
{
	private static $instances = [];

	public function __construct()
	{
		$class = get_called_class();

		if (isset(self::$instances[$class])) {
			throw new Except("$class: Нельзя создать больше одного экземпляра класса");
		}

		self::$instances[$class] = $this;
	}

	protected static function getInstance()
	{
		$class = get_called_class();
		
		return self::$instances[$class];
	}
}