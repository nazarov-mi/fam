<?php

class TypeModel extends Model
{
	const TABLE_NAME = "types";
	
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
			"Социальные акции в поддержку семьи и семейных ценностей",
			"Социальные акции по профилактике детского травматизма",
			"Социальные акции по пропаганде замещающего родительства",
			"Мастер-классы",
			"Уличные анимационные мероприятия",
			"Детская игровая площадка",
			"Родительский клуб",
			"Супружеский клуб",
			"Праздники и памятные даты",
			"Круглые столы",
			"Стажировка",
			"Информационно-методические занятия"
		];

		foreach ($data as $name) {
			$model = new TypeModel([
				"name" => $name,
				"desc" => ""
			]);
			$model->insert();
		}
	}
}