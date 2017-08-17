<?php
class DB extends PDO
{
	public function __construct($host, $name, $username, $password, $charset="utf8")
	{
		parent::__construct("mysql:host=" . $host . ";dbname=" . $name . ";charset=" . $charset, $username, $password);
		parent::query("SET NAMES '" . $charset . "'");
		parent::query("SET CHARSET '" . $charset . "'");
		parent::query("SET CHARACTER SET '" . $charset . "'");
		parent::query("SET SESSION collation_connection = '" . $charset . "_general_ci'");
	}

	public function escape($s, $isIdent = false)
	{
		if ($isIdent) {
			return "`" . str_replace('`', '``', $s) . "`";
		}

		return parent::quote($s);
	}

	public function parseArray($arr, $delimiter = ",", $isIdent = false)
	{
		$arr = is_array($arr) ? $arr : [$arr];
		$parts = [];

		foreach ($arr as $key => $val) {
			$val = ($val === null ? "NULL" : $this->escape($val, $isIdent));

			if(!is_int($key)) {
				$key = $this->escape($key, true);
				$parts[] = $key . "=" . $val;
			} else {
				$parts[] = $val;
			}
		}

		return implode($delimiter, $parts);
	}

	private function expandPlaceholdersFlow($sql)
	{
		$sql = preg_replace_callback(
			"{:([dfsna])}i",
			[$this, "expandPlaceholdersCallback"],
			$sql
		);

		if (!$sql) {
			throw new Except("DB: не удалось подготовить Sql запрос");
		}

		return $sql;
	}

	private function expandPlaceholdersCallback($matches)
	{
		$type = $matches[1];

		if (empty($this->__placeholderArgs)) {
			throw new Except("DB: слишком мало аргументов для подстановки в sql запрос");
		}

		$value = array_shift($this->__placeholderArgs);
		if ($value === null) {
			return "NULL";
		}

		switch ($type) {
			case "d":
				return intval($value);
			
			case "f":
				return str_replace(",", ".", floatval($value));

			case "s":
				return $this->escape($value);

			case "n":
				return $this->escape($value, true);

			case "a":
				return $this->parseArray($value);
		}

		return false;
	}

	public function prepareSql()
	{
		$args = func_get_args();

		if (count($args) < 1) {
			throw new Except("DB: Ожидались один или более аргументов");
		}
		
		$sql = array_shift($args);

		if (count($args) > 0) {
			$this->__placeholderArgs = $args;
			$sql = $this->expandPlaceholdersFlow($sql);
		}

		// echo $sql . "<br/>";
		return $sql;
	}

	public function query_arr($args)
	{
		$sql = call_user_func_array([$this, "prepareSql"], $args);

		return parent::query($sql);
	}

	public function aquery()
	{
		$sql = call_user_func_array([$this, "prepareSql"], func_get_args());

		return parent::query($sql);
	}

	public function get()
	{
		$query = $this->query_arr(func_get_args());

		if ($query) {
			return new Registry($query->fetch(PDO::FETCH_ASSOC));
		}

		return false;
	}

	public function all()
	{
		$query = $this->query_arr(func_get_args());

		if ($query) {
			$rows = $query->fetchAll(PDO::FETCH_ASSOC);
			$res = [];

			foreach ($rows as $row) {
				$res[] = new Registry($row);
			}

			return $res;
		}

		return false;
	}

	public function select()
	{
		$query = $this->query_arr(func_get_args());

		if ($query) {
			return $query->fetch(PDO::FETCH_ASSOC);
		}

		return false;
	}

	public function selectAll()
	{
		$query = $this->query_arr(func_get_args());

		if ($query) {
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}

		return false;
	}

	public function update()
	{
		$query = $this->query_arr(func_get_args());
		return $query !== false;
	}

	public function insert()
	{
		$query = $this->query_arr(func_get_args());
		
		if ($query) {
			return $this->lastInsertId();
		}
		
		return false;
	}

	public function delete()
	{
		$query = $this->query_arr(func_get_args());

		return ($query && $query->rowCount() > 0);
	}

	public function clearTable($tableName)
	{
		$sql = "DELETE FROM :n";
		return $this->aquery($sql, $tableName);
	}

	public function count($tableName)
	{
		$sql = "SELECT COUNT(*) as cnt FROM :n";
		$row = $this->get($sql, $tableName);

		if ($row) {
			return $row->cnt;
		}

		return 0;
	}
}