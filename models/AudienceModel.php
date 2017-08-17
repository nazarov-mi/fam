<?php

class AudienceModel extends Model
{
	const TABLE_NAME = "audience";
	
	public function __construct($data = null)
	{
		parent::__construct(
			[
				"id",
				"name",
				"desc"
			],
			$data
		);
	}

	public static function seeder()
	{
		self::clearTable();

		$data = [
			"Малообеспеченные семьи",
			"Неблагополучные семьи",
			"Семьи нуждающиеся в поддержке",
			"Сироты",
			"Дети оставшиеся без попечения родителей",
			"Инвалиды",
			"Граждане пожилого возраста"
		];

		foreach ($data as $name) {
			$model = new AudienceModel([
				"name" => $name,
				"desc" => ""
			]);
			$model->insert();
		}
	}
}