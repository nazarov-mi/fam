<?php

class LocationController extends Controller
{

	public function __construct()
	{
		$this->setPrivilege(Auth::AUTHED, [
			"all",
			"get",
			"save",
			"delete"
		]);
	}

	public function all()
	{
		$db = App::getDB();
		$list = $db->selectAll("SELECT * FROM `locations`");
		
		return $list;
	}

	public function get($request)
	{
		$id = $request->id;

		if (isset($id)) {
			$model = LocationModel::fromId($id);
			$data = $model->all();
		}

		return $data;
	}

	public function save($request)
	{
		$model = new LocationModel($request->all());
		$id = $model->save();
		$data = [];

		if (!is_bool($id)) {
			$data["id"] = $id;
		}

		return App::jsonResponse($data, "Данные места сохранены");
	}

	public function delete($request)
	{
		$status = LocationModel::deleteById($request->id);

		return App::jsonResponse(null, $status ? "Место удалёно" : "Не удалось удалить место", $status);
	}
}