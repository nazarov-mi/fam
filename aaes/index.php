<?php
session_start();

define( "HOST"			, $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"] . "/" );
define( "ROOT"			, $_SERVER["DOCUMENT_ROOT"] . "/" );
define( "AAES"			, ROOT . "aaes/" );
define( "SRC"			, AAES . "src/" );
define( "MODELS"		, ROOT . "models/" );
define( "CONTROLLERS", ROOT . "controllers/" );
define( "VIEWS"		, ROOT . "views/" );

include SRC . "core/ClassLoader.php";
ClassLoader::init(include AAES . "class.map.php");
$__app = new App();