<?php

class EventController extends Controller
{

	public function __construct()
	{
		$this->setPrivilege(Auth::AUTHED, [
			"get",
			"save",
			"delete",
			"all",
			"calendar"
		]);
	}

	public function get($request)
	{
		$id = $request->id;

		if (isset($id)) {
			$model = EventModel::fromId($id);
			$data = $model->all();
		}

		return $data;
	}

	public function save($request)
	{
		$model = new EventModel($request->all());
		$id = $model->save();
		$data = [];

		if (!is_bool($id)) {
			$data["id"] = $id;
		}

		return App::jsonResponse($data, "Событие сохранено");
	}

	public function delete($request)
	{
		$status = EventModel::deleteById($request->id);

		return App::jsonResponse(null, $status ? "Событие удалено" : "Не удалось удалить событие", $status);
	}

	public function all()
	{
		$db = App::getDB();
		$list = $db->selectAll("
			SELECT
				e.id,
				e.name,
				l.name as location,
				e.active_date,
				e.end_date,
				(e.peoples > l.peoples) as warn,
				(cur.id IS NOT NULL) as error
			FROM
				`events` e
			LEFT JOIN `locations` l ON l.id = e.location_id
			LEFT JOIN `events` cur ON
				e.id <> cur.id AND
				e.location_id = cur.location_id AND
				cur.end_date > NOW() AND
				e.end_date > cur.active_date AND
				e.active_date < cur.end_date
			WHERE e.end_date > NOW()
			GROUP BY e.id
			ORDER BY e.active_date ASC
		");
		
		return $list;
	}

	public function calendar()
	{
		$db = App::getDB();
		$list = $db->selectAll("
			SELECT
				e.id,
				e.name,
				l.name as location,
				(UNIX_TIMESTAMP(e.active_date) * 1000) as start,
				(UNIX_TIMESTAMP(e.end_date) * 1000) as end,
				TIMESTAMPDIFF(SECOND, e.active_date, e.end_date) as len,
				(e.peoples > l.peoples) as warn,
				(cur.id IS NOT NULL) as error
			FROM
				`events` e
			LEFT JOIN `locations` l ON l.id = e.location_id
			LEFT JOIN `events` cur ON
				e.id <> cur.id AND
				e.location_id = cur.location_id AND
				cur.end_date > NOW() AND
				e.end_date > cur.active_date AND
				e.active_date < cur.end_date
			WHERE e.end_date > NOW()
			GROUP BY e.id
			ORDER BY e.active_date ASC
		");

		return $list;
	}
}