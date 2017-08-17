<?php

class AuthController extends Controller
{

	public function __construct()
	{
		$this->setPrivilege(Auth::GUEST, [
			"login",
		]);

		$this->setPrivilege(Auth::AUTHED, [
			"logout"
		]);
	}

	public function login($request)
	{
		$user = App::getUser();
		$username = $request->username;
		$password = $request->password;
		
		if ($user->login($username, $password)) {
			return App::jsonResponse(null);
		}

		return App::jsonResponse(null, "Неправильный логин или пароль", false);
	}

	public function logout()
	{
		$user = App::getUser();
		$user->logout();

		return App::redirect("/");
	}
}