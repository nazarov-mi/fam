<?php

class UserModel extends Auth
{
	const TABLE_NAME = "users";

	public function __construct($data = null)
	{
		parent::__construct([
			"id",
			"name",
			"username",
			"password",
			"status",
			"login_at",
			"update_at",
			"create_at"
		], $data);
	}

	public static function seeder()
	{
		self::clearTable();

		$data = [
			["name" => "Администратор",                   "position" => "Администратор",                                                                     "username" => "Admin"],
			["name" => "Александрова Наталья Алексеевна", "position" => "Директор центра",                                                                   "username" => "АлександроваНА"],
			["name" => "Иванова Ольга Николаевна",        "position" => "Заместитель директора центра",                                                      "username" => "ИвановаОН"],
			["name" => "Рудикова Наталья Александровна",  "position" => "Заместитель директора центра",                                                      "username" => "РудиковаНА"],
			["name" => "Дахин Сергей Дмитриевич",         "position" => "Заместитель директора центра",                                                      "username" => "ДахинСД"],
			["name" => "Батаева Елена Сергеевна",         "position" => "Заведующий социально-психологической службой по работе с семьёй",                   "username" => "БатаеваЕС"],
			["name" => "Шубенина Алёна Владимировна",     "position" => "Заведующий отделением социального сопровождения семей",                             "username" => "ШубенинаАВ"],
			["name" => "Давыденко Екатерина Алексеевна",  "position" => "Заведующий отделением по организации отдыха и оздоровления",                        "username" => "ДавыденкоЕА"],
			["name" => "Давлетова Елена Николаевна",      "position" => "Заведующий культурно – досуговым отделением граждан пожилого возраста и инвалидов", "username" => "ДавлетоваЕН"],
			["name" => "Семенова Наталия Юрьевна",        "position" => "Заведующий отделением постинтернатного сопровождения",                              "username" => "СеменоваНЮ"]
		];

		foreach ($data as $item) {
			$model = new UserModel([
				"name" => $item["name"],
				"position" => $item["position"],
				"username" => $item["username"],
				"password" => 1,
				"status" => Auth::ADMIN
			]);
			$model->insert();
		}
	}
}