<?php

abstract class Auth extends Model
{
	const GUEST		= 0x1;
	const OPERATOR	= 0x2;
	const MODERATOR = 0x4;
	const ADMIN		= 0x8;
	const AUTHED	= 0xe;
	const ALL		= 0xf;


	public function __construct($cols, $data = null, $toJson = null)
	{
		foreach (["id", "username", "password", "status"] as $name) {
			if (!in_array($name, $cols)) {
				throw new Except("Auth: Модель должна содержать поле $name");
			}
		}

		parent::__construct($cols, $data, $toJson);
	}

	public function loginFromCookie()
	{
		$id = $_COOKIE["USERID"];
		$this->find($id);

		if (empty($this->id)) {
			$this->status = Auth::GUEST;
		}

		return $this;
	}

	public function set($name, $value)
	{
		if ($name == "password") {
			$value = Utils::makePassword($value);
		}

		parent::set($name, $value);
	}

	/**
	 * Проверяет статус пользователя
	 * @param status - статус для проверки
	 * @return true/false
	 */
	public function checkStatus($status)
	{
		return (($this->status & $status) > 0);
	}

	/**
	 * Проверяет вошёл ли пользователь
	 * @return true/false
	 */
	public function isAuthorized()
	{
		$user = App::getUSer();

		return (($user->status & Auth::GUEST) === 0 && $user->id === $this->id);
	}
	
	/**
	 * Проверяет данные пользователя
	 * @param username - логин пользователя
	 * @param password - пароль пользователя
	 * @return Возвращает User или false 
	 */
	public function login($username, $password)
	{
		$db = App::getDB();
		$tablename = self::getTableName();

		$data = $db->get("SELECT * FROM :n WHERE `username`=:s", $tablename, $username);
		// var_dump($password, $data->password, Utils::checkPassword($password, $data->password));
		if ($data !== false && Utils::checkPassword($password, $data->password)) {
			$data->delete("password");
			$this->record($data);

			if (in_array("login_at", $this->cols)) {
				$this->set("login_at", date("Y-m-d H:i:s"));
				$this->update();
			}

			setcookie("USERID", $this->id, 0, "/");

			return true;
		}

		return false;
	}

	public function logout()
	{
		setcookie("USERID", $this->id, time() - 3600, "/");

		$this->clear();
		$this->status = Auth::GUEST;
	}


	// Static

	public static function check()
	{
		$user = App::getUSer();

		return (($user->status & Auth::GUEST) === 0);
	}
}