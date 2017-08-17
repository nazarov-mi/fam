<?php

class Request
{

	protected $query;
	protected $request;
	protected $cookies;
	protected $files;
	protected $server;
	protected $attrs;

	
	public function __construct(array $query = array(), array $request = array(), array $cookies = array(), array $files = array(), array $server = array(), array $attrs = array())
	{
		$this->query = new Registry($query);
		$this->request = new Registry($request);
		$this->cookies = new Registry($cookies);
		$this->files = new Registry($files);
		$this->server = new Registry($server);
		$this->attrs = new Registry($attrs);
	}

	public function fromGlobals($attrs = null)
	{
		if (!isset($attrs)) {
			$attrs = array();
		}

		$request = $_POST;
		$restJson = file_get_contents("php://input");
		$restData = json_decode($restJson, true);
		
		if ($restData) {
			$request = array_replace($request, $restData);
		}

		return new self($_GET, $request, $_COOKIE, $_FILES, $_SERVER, $attrs);
	}

	public function getMethod()
	{
		return strtoupper($this->server->get("REQUEST_METHOD", "GET"));
	}

	public function all()
	{
		return $this->getMethod() == "GET" ? $this->query : $this->request;
	}

	public function __isset($key)
	{
		return $this->all()->__isset($key);
	}

	public function __get($key)
	{
		return $this->all()->get($key);
	}
}