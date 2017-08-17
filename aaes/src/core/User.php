<?php

class User extends Auth
{
	const TABLE_NAME = "users";

	public function __construct($data = null)
	{
		parent::__construct([
			"id",
			"name",
			"username",
			"password",
			"status"
		], $data);
	}
}