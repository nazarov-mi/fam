<?php
class Controller
{
	private $privilege = [];

	final protected function setPrivilege($status, $nameArr, $redirectTo = false)
	{
		foreach ($nameArr as $name) {
			$method = &$this->privilege[$name];

			if (isset($method)) {
				$method |= $status;
			} else {
				$method = $status;
			}
		}
	}

	final public function _call($name, $request)
	{
		$user = App::getUser();
		$status = $this->privilege[$name];
		$class = get_called_class();

		if (!isset($status)) {
			throw new Except("$class: Метод $name не найден в контроллере", 404);
		}

		if (!$user->checkStatus($status)) {
			throw new Except("$class: У пользователя не хватает привелегий", 401);
		}

		return call_user_func([$this, $name], $request);
	}
}