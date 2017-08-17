<?php
class SystemHandlers extends Unique
{
	public function __construct()
	{
		parent::__construct();
		
		set_exception_handler([&$this, "handleException"]);
		set_error_handler([&$this, "handleError"], error_reporting());
	}

	public function handleError($code, $message, $file, $line)
	{
		if ($code & error_reporting()) {
			restore_error_handler();
			restore_exception_handler();

			try {
				$this->displayError($code, $message, $file, $line);
			} catch(Except $e) {
				$this->displayException($e);
			}
		}
	}

	public function handleException($exception)
	{
		restore_error_handler();
		restore_exception_handler();

		$this->displayException($exception);
	}

	private function displayError($code, $message, $file, $line)
	{
		if (App::getSetting("display_errors")) {
			$content = "<h1>Ошибка PHP[$code]!</h1>
					<p>$message ($file : $line)</p><br/>
					<pre>";

			$trace = debug_backtrace();

			if (count($trace) > 3) {
				$trace = array_slice($trace, 3);
			}

			foreach ($trace as $key => $value) {
				if (!isset($value["file"])) {
					$value["file"] = "unknown";
				}

				if (!isset($value["line"])) {
					$value["line"] = "unknown";
				}
				
				if (!isset($value["function"])) {
					$value["function"] = "unknown";
				}
				
				$content .= "#$key ($file : $line): ";
				
				if (isset($value["object"]) && is_object($value["object"])) {
					$content .= get_class($value["object"]) . " => ";
				}
				
				$content .= $value["function"] . "<br/>";
			}
			
			$content .= "</pre>";
		}

		$this->sendError($content, 500, null);
	}

	private function displayException($except)
	{
		if ($except instanceof Except) {
			$text = $except->getStatusText();
			$code = $except->getStatusCode();
		} else {
			$text = $except->getMessage();
			$code = 500;
		}

		if (App::getSetting("display_errors")) {
			$content = "
				<h1>Ошибка " . $code . "</h1>
				<h3>" . $text . "</h3>
				<p>(" . $except->getFile() . " : " . $except->getLine() . ")</p>
				<pre>" . $except->getTraceAsString() . "</pre>
			";
		}

		$this->sendError($content, $code, $text);
	}

	private function getUserScreen($code, $text)
	{
		return "
			<div style=\"width: 100%; margin-top: 100px; text-align: center; font-family: sans-serif;\">
				<div style=\"font-size: 64px; color: red;\">×</div>
				<h1>$code</h1>
				<p>$text</p>
			</div>
		";
	}

	private function sendError($content, $code, $text)
	{
		if (!isset($content)) {
			$content = $this->getUserScreen($code, $text);
		}

		$response = new Response($content, $code, $text);
		$response->send();
	}
}