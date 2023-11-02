<?php

use JetBrains\PhpStorm\Internal\ReturnTypeContract;

/**
* @author  Claudio Guzman Herrera
* @version 1.0
*/
class querys
{
	private $fecha_hoy;
	private $fecha_hora_hoy;
	private $error;
	private $sql;
	private $token;

	function __construct($sql=null)
	{
		# code...
		if ( !is_null( $sql ) ){
			$this->sql   = $sql;
			$this->error = "Modulo no definido";
		}

		else{

			$oConf     = new config();
		  $cfg       = $oConf->getConfig();
		  $this->sql = new mysqldb( $cfg['base']['dbhost'],
				 					$cfg['base']['dbuser'],
									$cfg['base']['dbpass'],
									$cfg['base']['dbdata'] );

		$this->error = $cfg['base']['error'];
		}

		$this->fecha_hoy 		=  date("Y-m-d");
		$this->fecha_hora_hoy 	=  date("Y-m-d H:i:s");
		$this->token 		    =  date("YmdHis");

	}

	public function someQuery(){

		$ssql = "";

		$arr['sql'] = $ssql; 
		$arr['process'] = $this->sql->select( $ssql) ;
		$arr['total-recs'] = count($arr['process']) ;

		return $arr ;
	}

	public function menu( $tipo_usuario = 1 )
	{
		$ssql = "SELECT * FROM menu WHERE tipo_usuario = {$tipo_usuario} order by orden";
	
		$arr['sql'] 	= $ssql;
		$arr['process'] = $this->sql->select( $ssql );
		$arr['total-recs'] = count( $arr['process'] );
	
		return $arr;
	}

	public function sub_menu( $id_menu = null )
	{
		$ssql = "SELECT * FROM sub_menu WHERE id_menu = {$id_menu}";

		$arr['sql'] 	= $ssql;
		$arr['process'] = $this->sql->select( $ssql );
		$arr['total-recs'] = count( $arr['process'] );

		return $arr;
	}
}

?>
