<?php
if (Auth::check()) {
	View::addFile("app_js", "dist/admin.js");
} else {
	View::addFile("app_js", "dist/login.js");
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<title>МСЦ Семья</title>
		{{head}}
	</head>
	<body>
		<div id="app"></div>
	</body>
</html>