<?php

class TypeController extends Controller
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
		$list = $db->selectAll("SELECT * FROM `types`");
		
		return $list;
	}

	public function get($request)
	{
		$id = $request->id;

		if (isset($id)) {
			$model = TypeModel::fromId($id);
			$data = $model->all();
		}

		return $data;
	}

	public function save($request)
	{
		$model = new TypeModel($request->all());
		$id = $model->save();
		$data = [];

		if (!is_bool($id)) {
			$data["id"] = $id;
		}

		return App::jsonResponse($data, "Данные сохранены");
	}

	public function delete($request)
	{
		$status = TypeModel::deleteById($request->id);

		return App::jsonResponse(null, $status ? "Тип удалён" : "Не удалось удалить тип", $status);
	}
}