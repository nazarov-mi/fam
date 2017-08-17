<?php

class MainController extends Controller
{

	public function __construct()
	{
		$this->setPrivilege(Auth::ALL, [
			'index'
		]);

		$this->setPrivilege(Auth::AUTHED, [
			'report',
			'seeder'
		]);
	}

	public function seeder()
	{
		TypeModel::seeder();
		LocationModel::seeder();
		AudienceModel::seeder();
		EventModel::seeder();
		UserModel::seeder();

		return 'База данных успешно обновлена';
	}

	public function index()
	{
		return View::show('index');
	}

	public function report($request)
	{
		$cur = [
			'id' => 'ID',
			'name' => 'Название',
			'type_id' => 'Тип',
			'audience_id' => 'Аудитория',
			'location_id' => 'Место проведения',
			'peoples' => 'Колличство человек',
			'audience' => 'Аудитория',
			'active_date' => 'Начало',
			'end_date' => 'Окончание'
		];

		$rfields = explode(',', $request->fields);
		$fields = [];
		$head = [];
		
		foreach ($cur as $key => $name) {
			if (in_array($key, $rfields)) {
				$field = '';

				switch ($key) {
					case 'type_id':
						$field = 't.name as type';
						break;

					case 'audience_id':
						$field = 'a.name as aud';
						break;

					case 'location_id':
						$field = 'l.name as loc';
						break;
					
					default:
						$field = 'e.' . $key;
						break;
				}
				
				$fields[] = $field;
				$head[] = $name;
			}
		}

		$db = App::getDB();
		$sql = 'SELECT ' . implode(',', $fields) . ' FROM `events` as e';
		$where = ['1'];

		if (in_array('type_id', $rfields) || $request->types) {
			$sql .= ' LEFT JOIN `types` as t ON t.id = e.type_id';
		}

		if (in_array('audience_id', $rfields) || $request->audience) {
			$sql .= ' LEFT JOIN `audience` as a ON a.id = e.audience_id';
		}

		if (in_array('location_id', $rfields) || $request->locations) {
			$sql .= ' LEFT JOIN `locations` as l ON l.id = e.location_id';
		}

		if ($request->conflicts && $request->conflicts !== '0') {

			$sql .= '
				LEFT JOIN `events` cur ON
					e.id <> cur.id AND
					e.location_id = cur.location_id AND
					cur.end_date > NOW() AND
					e.end_date > cur.active_date AND
					e.active_date < cur.end_date';

			if ($request->conflicts == '1') {
				$where[] = 'cur.id IS NULL';
			} else {
				$where[] = 'cur.id IS NOT NULL';
			}
		}

		if ($request->types) {
			$where[] = 't.id IN (' . $db->parseArray(explode(',', $request->types)) . ')';
		}

		if ($request->audience) {
			$where[] = 'a.id IN (' . $db->parseArray(explode(',', $request->audience)) . ')';
		}

		if ($request->locations) {
			$where[] = 'l.id IN (' . $db->parseArray(explode(',', $request->locations)) . ')';
		}

		if ($request->active_date) {
			$where[] = $db->prepareSql('e.end_date >= :s', $request->active_date);
		}

		if ($request->end_date) {
			$where[] = $db->prepareSql('e.active_date <= :s', $request->end_date);
		}

		if ($request->peoples_min) {
			$where[] = $db->prepareSql('e.peoples >= :d', $request->peoples_min);
		}

		if ($request->peoples_max) {
			$where[] = $db->prepareSql('e.peoples <= :d', $request->peoples_max);
		}

		$sql .= ' WHERE ' . implode(' AND ', $where) .  ' GROUP BY e.id ORDER BY e.active_date ASC';

		View::setArray([
			'head' => $head,
			'events' => $db->selectAll($sql)
		]);

		return View::show('report');
	}
}