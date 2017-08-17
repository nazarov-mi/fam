<?php

class SearchController extends Controller
{

	public function __construct()
	{
		$this->setPrivilege(Auth::AUTHED, [
			"events",
			"types",
			"audience",
			"locations",
			"users"
		]);
	}

	private function search($tableName, $request)
	{
		$db = App::getDB();
		$sql = "SELECT e.`id` as value, e.`name` as label FROM :n as e WHERE 1";
		$page = isset($request->page) ? $request->page : 0;
		$num = isset($request->num) ? $request->num : 20;
		$query = $request->query;
		$value = $request->value;


		if (!empty($query)) {
			$sql .= $db->prepareSql(" AND LOCATE(:s, e.`name`)", $query);
		}

		if (!empty($value)) {
			$sql .= $db->prepareSql(" AND e.`id` IN (:a)", $value);
		}

		$sql .= " LIMIT :d, :d";
		
		return $db->selectAll($sql, $tableName, $page * $num, $num);
	}

	public function events($request)
	{
		return $this->search("events", $request);
	}

	public function types($request)
	{
		return $this->search("types", $request);
	}

	public function audience($request)
	{
		return $this->search("audience", $request);
	}

	public function locations($request)
	{
		return $this->search("locations", $request);
	}

	public function users($request)
	{
		return $this->search("users", $request);
	}
}