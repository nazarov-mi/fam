<?php
class Registry implements Iterator, Countable
{
	private $data;

	public function __construct($data = null)
	{
		$this->record($data);
	}

	public function has($key)
	{
		return array_key_exists($key, $this->data);
	}

	public function get($key, $default = null)
	{
		return array_key_exists($key, $this->data) ? $this->data[$key] : $default;
	}

	public function set($key, $value)
	{
		$this->data[$key] = $value;
	}

	public function delete($key)
	{
		unset($this->data[$key]);
	}

	public function all()
	{
		return $this->data;
	}

	public function keys()
	{
		return array_keys($this->data);
	}

	public function clear()
	{
		$this->data = array();
	}

	public function record($data)
	{
		$this->data = array();
		$this->add($data);
	}

	public function add($data)
	{
		if (empty($data)) return;

		foreach ($data as $key => $val) {
			$this->set($key, $data[$key]);
		}
	}

	public function __get($key)
	{
		return $this->get($key);
	}

	public function __set($key, $val)
	{
		return $this->set($key, $val);
	}

	public function __isset($key)
	{
		return isset($this->data[$key]);
	}

	public function __unset($key)
	{
		$this->delete($key);
	}


	/**
	 * Iterator
	 * Методы
	 */

	public function current()
	{
		return current($this->data);
	}

	public function key()
	{
		return key($this->data);
	}

	public function next()
	{
		return next($this->data);
	}

	public function rewind()
	{
		reset($this->data);
	}

	public function valid()
	{
		$key = key($this->data);
		return ($key !== null && $key !== false);
	}


	/**
	 * Countable
	 * Методы
	 */

	public function count()
	{
		return count($this->data);
	}

}