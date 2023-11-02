<?php

  require_once( "class/mysqldb.class.php" );
  require_once( "class/querys.class.php" );
  require_once( "class/template.class.php" );
  require_once( "class/index.class.php" );
  require_once( "class/select.class.php" );

  require_once("config.php");

  $ob_index = new Index();
  echo $ob_index->getCode();

 ?>
