<?php
/**
* @author  Claudio Guzman Herrera
* @version 1.0
*/
class Index
{
	private $id;
	private $consultas;
	private $template;
	private $ruta;
	private $yo;

	function __construct()
	{
		# invocar archivo de configuracion

		$oConf    = new config();
	    $cfg      = $oConf->getConfig();
	    $db       = new mysqldb( 	$cfg['base']['dbhost'],
									$cfg['base']['dbuser'],
									$cfg['base']['dbpass'],
									$cfg['base']['dbdata'] );

	    $this->consultas = new querys( $db );
	    $this->template  = new template();
	    $this->ruta      = $cfg['base']['template'];

	    
	}
		/**
		 * 
		 */
		private function control()
		{	
			$data = [];
			return $this::despliegueTemplate( $data , 'index.html' );

		}


	/**
	  * despliegueTemplate(), metodo que sirve para procesar los templates
	  *
	  * @param  array   arrayData (array de datos)
	  * @param  array   tpl ( template )
	  * @return String
	  */
    private function despliegueTemplate($arrayData,$tpl){

     	  $tpl = $this->ruta.$tpl;

	      $this->template->setTemplate($tpl);
	      $this->template->llena($arrayData);

	      return $this->template->getCode();
	  }

	public function getCode(  ){

		return $this::control();
	}

}

?>
