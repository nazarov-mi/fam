<?php

class AudienceController extends Controller
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
		$list = $db->selectAll("SELECT * FROM `audience`");
		
		return $list;
	}

	public function get($request)
	{
		$id = $request->id;

		if (isset($id)) {
			$model = AudienceModel::fromId($id);
			$data = $model->all();
		}

		return $data;
	}

	public function save($request)
	{
		$model = new AudienceModel($request->all());
		$id = $model->save();
		$data = [];

		if (!is_bool($id)) {
			$data["id"] = $id;
		}

		return App::jsonResponse($data, "Данные сохранены");
	}

	public function delete($request)
	{
		$status = AudienceModel::deleteById($request->id);

		return App::jsonResponse(null, $status ? "Аудитория удалена" : "Не удалось удалить аудиторию", $status);
	}
}