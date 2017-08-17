<?php
class Image
{
	private $tmp_name;
	private $type;
	private $width;
	private $height;
	private $src;

	public function __construct($tmp_name)
	{
		if (empty($tmp_name)) {
			throw new Except("Image: Неверное имя файла");
		}
		
		if ($info = getimagesize($tmp_name)) {
			$this->tmp_name = $tmp_name;
			
			$this->type = trim(strrchr($info["mime"], "/"), "/");
			list($this->width, $this->height) = $info;
			
			$imagecreate = "imagecreatefrom" . $this->type;
			$this->src = $imagecreate($tmp_name);
		} else {
			throw new Except("Image: Не удалось получить данные файла");
		}
	}

	public function dispose()
	{
		unset($this->src);
	}

	public function save($dir, $name = null, $dispose = true)
	{
		if (empty($this->src)) {
			return false;
		}

		if (empty($name)) {
			$name = Utils::getHash();
		}
		
		$name .= "." . $this->type;
		
		if (is_dir($dir) || mkdir($dir, 0777)) {
			$imagesave = "image" . $this->type;
			$url = $dir . $name;
			$res = $imagesave($this->src, $url);
		}
		
		if ($dispose) {
			$this->dispose();
		}

		return ($res ? $url : false);
	}

	public function getWidth()
	{
		return $width ?: 0;
	}

	public function getHeight()
	{
		return $height ?: 0;
	}

	public function getType()
	{
		return $this->type;
	}

	public function resize($width, $height)
	{
		if (empty($this->src)) {
			return false;
		}

		$dst_img = imagecreatetruecolor($width, $height);
		imagecopyresampled($dst_img, $this->src, 0, 0, 0, 0, $width, $height, $this->width, $this->height);
		unset($this->src);
		$this->src = $dst_img;

		return true;
	}

	public function resizeFromEdge($size, $max = true)
	{
		if ($this->width == 0 || $this->height == 0) {
			return false;
		}

		$width = ($this->width > $this->height) == $max ? $size : ceil(($this->width * $size) / $this->height);
		$height = ($this->width < $this->height) == $max ? $size : ceil(($this->height * $size) / $this->width);
		
		return $this->resize($width, $height);
	}
}