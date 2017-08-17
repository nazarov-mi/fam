<?php
class Utils
{
	/**
	 * Возвращает уникальный хэш-код длиной 32 символа
	 * @return String
	 */
	public static function getHash()
	{
		return md5(time() . rand());
	}

	public static function makePassword($value)
	{
		$hash = password_hash($value, PASSWORD_BCRYPT, ["cost" => 10]);

		if ($hash === false) {
			throw new Except("Utils: Не удалось сгенерировать пароль");
		}

		return $hash;
	}

	public static function checkPassword($value, $hash)
	{
		if (strlen($hash) === 0) {
            return false;
        }

        return password_verify($value, $hash);
	}

	/**
	 * Обрезает текст и добавляет окончание, если текст длиннее заданного размера
	 * @param text - исходный текст
	 * @param len  - максимальная длина
	 * @param end  - окончание
	 * @return String
	 */
	public static function subText($text, $len = 35, $end = "...")
	{
		if (mb_strlen($text) > $len) {
			return mb_substr($text, 0, $len) . $end;
		}
		return $text;
	}

	/**
	 * Добавляет символы в начало текста, до необходимой длинны
	 * @param text - исходный текст
	 * @param len  - необходимая длина
	 * @param char - символ
	 * @return String
	 */
	public static function fixString($text, $len, $char = " ")
	{
		while (mb_strlen($text) < $len) {
			$text = $char . $text;
		}
		return $text;
	}
	
	/**
	 * Приводит дату к формату 01 Январь 1970
	 * @param date - дата в формате Unix
	 * @return String
	 */
	public static function cnvDate($date)
	{
		return date('j F Y', strtotime($date));
	}
	
	/**
	 * Приводит дату к формату 01 Январь 1970, 00:00
	 * @param date - дата в формате Unix
	 * @return String
	 */
	public static function cnvDateTime($date)
	{
		return date('j F Y, H:M', strtotime($date));
	}

	/**
	 * Формирует список <select>
	 * @param name - поле name списка
	 * @param options - массив значений
	 * @param active - активный элемент
	 * @param createEmpty - имя пустого элемента
	 * Если имя не заданно, пустой элемент не будет создан
	 * @return String
	 */
	public static function getSelect($name, $options, $selected = null, $createEmpty = "")
	{
		if (!is_array($options)) return;
		
		$res = '<select name="' . $name . '" class="' . $name . '">';
		
		if ($createEmpty) {
			$res .= '<option value="">Нет</option>';
		}
		
		foreach ($options as $value => $option) {
			$isSelected = ($value == $selected ? "selected" : "");
			$res .= '<option value="' . $value . '" ' . $isSelected . '>' . $option . '</option>';
		}
		
		$res .= '</select>';
		
		return $res;
	}
	
	public static function getRadioList($name, $options, $checked = null)
	{
		if (!is_array($options)) return;
		
		$res = '';
		
		foreach ($options as $value => $label) {
			$isChecked = ($value == $checked ? 'checked' : '');
			$res .= '
				<label>
					<input type="radio" name="' . $name . '" class="' . $name . '" value="' . $value . '" ' . $isChecked . '/>
					' . $label . '
				</label>';
		}
		
		return $res;
	}
	
	public static function getCheckboxList($name, $options, $checked = null)
	{
		if (!is_array($options)) return;
		if (!is_array($checked)) $checked = array();
		
		$res = '';
		
		foreach ($options as $value => $label) {
			$isChecked = (in_array($value, $checked) ? 'checked' : '');
			$res .= '
				<label>
					<input type="checkbox" name="' . $name . '" class="' . $name . '" value="' . $value . '" ' . $isChecked . '/>
					' . $label . '
				</label>';
		}
		
		return $res;
	}

	public static function parseVideoUrl($url)
	{
		if (!preg_match("/^(http|https)\:\/\//i", $url)) {
			$url = "http://" . $url;
		}
		
		// YouTube
		if (preg_match("/[http|https]+:\/\/(?:www\.|)youtube\.com\/watch\?(?:.*)?v=([a-zA-Z0-9_\-]+)/i", $url, $matches) ||
			preg_match("/[http|https]+:\/\/(?:www\.|)youtube\.com\/embed\/([a-zA-Z0-9_\-]+)/i", $url, $matches) ||
			preg_match("/[http|https]+:\/\/(?:www\.|)youtu\.be\/([a-zA-Z0-9_\-]+)/i", $url, $matches)
		) {
			$host = "youtube";
			$key = $matches[1];
			$url = "http://www.youtube.com/embed/$key?autoplay=1&wmode=opaque";
			$small_preview = "http://img.youtube.com/vi/$key/mqdefault.jpg";
			$large_preview = "http://img.youtube.com/vi/$key/maxresdefault.jpg";

			$res = true;
		}
		
		// Vimeo
		else if (preg_match("/[http|https]+:\/\/(?:www\.|)vimeo\.com\/([a-zA-Z0-9_\-]+)(&.+)?/i", $url, $matches) ||
			     preg_match("/[http|https]+:\/\/player\.vimeo\.com\/video\/([a-zA-Z0-9_\-]+)(&.+)?/i", $url, $matches)
		) {
			$host = "vimeo";
			$key = $matches[1];
			if ($xml = simplexml_load_file("http://vimeo.com/api/v2/video/$key.xml")) {
				$url = "http://player.vimeo.com/video/$key?autoplay=1";
				$small_preview = (string) $xml->video->thumbnail_medium;
				$large_preview = (string) $xml->video->thumbnail_large;

				$res = true;
			}
		}

		if ($res) {
			return array(
				"host" => $host,
				"key"  => $key,
				"url"  => $url,
				"small_preview" => $small_preview,
				"large_preview" => $large_preview
			);
		}

		return false;
	}
}