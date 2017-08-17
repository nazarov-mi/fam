<?php
class App extends Unique
{
	public $config;
	public $db;
	public $user;
	public $systemHandlers;
	public $route;
	
	public function __construct()
	{
		parent::__construct();

		// Подключение файлов кофигураций
		$this->config = new Registry(include AAES . "config.php");
		
		// Запускаем обработчик ошибок
		$this->systemHandlers = new SystemHandlers();

		// Создаём экземпляр базы данных
		$this->db = new DB(
			$this->config->get("host", "localhost"),
			$this->config->get("name", "dbname"),
			$this->config->get("username", "root"),
			$this->config->get("password", "")
		);

		View::init();

		// Создаём экземпляр пользователя
		$userModel = $this->config->get("user_model");

		if (!empty($userModel)) {
			$this->user = ClassLoader::getModel($userModel);

			if ($this->user === false) {
				throw new Except("App: Модель пользователя с именем $userModel не найдена");
			}

			if (!($this->user instanceof Auth)) {
				throw new Except("App: Модель пользователя $userModel должна быть наследником класса Auth");
			}
		} else {
			$this->user = new User();
		}

		$this->user->loginFromCookie();

		// Запускаем обработчик URL
		$this->route = new Router();
	}

	public static function getSetting($key, $default = null)
	{
		return self::getInstance()->config->get($key, $default);
	}

	public static function getDB()
	{
		return self::getInstance()->db;
	}

	public static function getUser()
	{
		return self::getInstance()->user;
	}

	public static function getSystemHandlers()
	{
		return self::getInstance()->$systemHandlers;
	}

	public static function getRoute()
	{
		return self::getInstance()->$route;
	}

	// Responses

	public static function response($content, $code = 200, $text = null)
	{
		return new Response($content, $code, $text);
	}

	public static function jsonResponse($data, $message = null, $status = true)
	{
		$content = [
			"message" => $message,
			"status" => $status,
			"data" => $data
		];

		return new Response($content);
	}

	public static function redirect($url, $code = 302)
	{
		$response = new Response(null, $code);
		$response->setHeader("Location", $url);

		return $response;
	}
}