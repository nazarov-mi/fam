<?php

class UserController extends Controller
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
		$list = $db->selectAll("SELECT * FROM `users`");
		
		return $list;
	}

	public function get($request)
	{
		$id = $request->id;

		if (isset($id)) {
			$model = UserModel::fromId($id);
			$data = $model->all();
		}

		return $data;
	}

	public function save($request)
	{
		$model = new UserModel($request->all());
		$id = $model->save();
		$data = [];

		if (!is_bool($id)) {
			$data["id"] = $id;
		}

		return App::jsonResponse($data, "Данные пользователя сохранены");
	}

	public function delete($request)
	{
		$status = UserModel::deleteById($request->id);

		return App::jsonResponse(null, $status ? "Пользователь удалён" : "Не удалось удалить пользователя", $status);
	}
}