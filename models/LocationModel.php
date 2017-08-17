<?php

class LocationModel extends Model
{
	const TABLE_NAME = "locations";
	
	public function __construct($data = null)
	{
		parent::__construct(
			[
				"id",
				"name",
				"peoples",
				"desc"
			],
			$data
		);
	}

	public static function seeder()
	{
		self::clearTable();

		$data = [
			'Областная детская библиотека',
			'Областной центр развития творчества детей и юношества',
			'Театр юного зрителя',
			'Парк Аркадия',
			'Братский садик',
			'Библиотека им. Н.К. Крупской',
			'Развлекательный центр «Мульти-дом»',
			'МАУК «ЦБС им. Горького»',
			'Ледовый дворец «Победа»',
			'Дворец Металлургов',
			'Бассейна «Волна»',
			'Мини-клуб «Зимняя вишня»',
			'Мемориал воинов-интернационалистов',
			'Казачий кадетский корпус'
		];

		foreach ($data as $name) {
			$model = new LocationModel([
				"name" => $name,
				"peoples" => rand(5, 15) * 10
			]);
			$model->insert();
		}
	}
}