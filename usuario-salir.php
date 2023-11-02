<?php
header('Cache-Control: no cache');
//session_cache_limiter('public'); // works too session_start();
session_cache_limiter('private, must-revalidate');
session_cache_expire(60);
define('DURACION_SESION','7200'); //2 horas
ini_set("session.cookie_lifetime",DURACION_SESION);
ini_set("session.gc_maxlifetime",DURACION_SESION);
ini_set("session.save_path","/tmp");
session_cache_expire(DURACION_SESION);

  session_start();
  $_SESSION = array() ;

  if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
  session_destroy();

  $insertGoTo = "index.php";
	header(sprintf("location: %s", $insertGoTo));
	?>
