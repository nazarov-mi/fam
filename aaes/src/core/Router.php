<?php
class Router extends Unique
{
	public function __construct()
	{
		parent::__construct();
		
		$route = $_GET["route"];
		$controllerName = "Main";
		$methodName = "index";
		
		if (!empty($route)) {
			$attrs = explode("/", $route);
			
			if (count($attrs) > 0) {
				$controllerName = array_shift($attrs);
				$controllerName = ucfirst(strtolower($controllerName));
				
				if (count($attrs) > 0) {
					$methodName = array_shift($attrs);
				}
			}
		}

		$controller = ClassLoader::getController($controllerName);
		
		if ($controller === false) {
			throw new Except("Router: контроллер $controllerName не найден");
		}
		
		$request = Request::fromGlobals($attrs);
		$res = $controller->_call($methodName, $request);
		
		if (!($res instanceof Response)) {
			$res = new Response($res);
		}

		$res->send();
	}
}