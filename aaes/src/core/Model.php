<?php
abstract class Model extends Registry
{
	protected $cols;
	protected $toJson;

	public function __construct($cols, $data = null, $toJson = null)
	{
		if (!is_array($cols) || count($cols) == 0) {
			throw new Except("Model: Аргумент fields должен быть массивом и включать больше 0 элементов");
		}

		$this->cols = $cols;
		$this->toJson = (is_array($toJson) ? $toJson : []);

		parent::__construct($data);
	}

	protected static function getTableName()
	{
		$class = get_called_class();
		return $class::TABLE_NAME;
	}

	public static function fromId($id)
	{
		$class = get_called_class();
		
		return (new $class())->find($id);
	}

	public function set($key, $val)
	{
		if (in_array($key, $this->cols)) {
			parent::set($key, $val);
		}
	}

	public function add($data)
	{
		if (empty($data)) return;

		foreach ($data as $key => $val) {
			if (in_array($key, $this->toJson) && is_string($val)) {
				$val = json_decode($val);
			}

			$this->set($key, $val);
		}
	}
	
	public function allPrepared()
	{
		$data = array_slice($this->all(), 0);
		
		foreach ($this->toJson as $key) {
			$data[$key] = json_encode($data[$key]);
		}
		
		return $data;
	}

	public function find($id)
	{
		$db = App::getDB();
		$tablename = self::getTableName();
		$data = $db->select("SELECT * FROM :n WHERE `id`=:d", $tablename, $id);

		$this->record($data);

		return $this;
	}

	public function insert()
	{
		$date = date("Y-m-d H:i:s");

		if (in_array("create_at", $this->cols)) {
			$this->set("create_at", $date);
		}

		if (in_array("update_at", $this->cols)) {
			$this->set("update_at", $date);
		}

		$db = App::getDB();
		$tablename = self::getTableName();
		$data = $this->allPrepared();
		$id = $db->insert("INSERT INTO :n SET :a", $tablename, $data);

		if ($id !== false) {
			$this->set("id", $id);
			return $id;
		}

		return false;
	}

	public function update()
	{
		if (in_array("update_at", $this->cols)) {
			$this->set("update_at", date("Y-m-d H:i:s"));
		}
		
		$db = App::getDB();
		$tablename = self::getTableName();
		$id = $this->get("id");
		$data = $this->allPrepared();

		return $db->update("UPDATE :n SET :a WHERE `id`=:d", $tablename, $data, $id);
	}

	public function save()
	{
		if (empty($this->id)) {
			return $this->insert();
		}
		
		return $this->update();
	}

	public function delete()
	{
		$db = App::getDB();
		$tablename = self::getTableName();
		$id = $this->get("id");

		return $db->delete("DELETE FROM :n WHERE `id`=:d", $tablename, $id);
	}

	public static function deleteById($id)
	{
		$db = App::getDB();
		$tablename = self::getTableName();

		return $db->delete("DELETE FROM :n WHERE `id`=:d", $tablename, $id);
	}

	public static function getList($page = 0, $num = 1000000, $orderBy = "id", $toLower = true)
	{
		$db = App::getDB();
		$tablename = self::getTableName();
		$sql = "SELECT * FROM :n WHERE 1";
		$sql = $db->prepareSql($sql, $tablename);

		return new ArrayList($sql, $page, $num, $orderBy, $toLower);
	}

	public static function clearTable()
	{
		$db = App::getDB();
		$tablename = self::getTableName();

		return $db->clearTable($tablename);
	}
}