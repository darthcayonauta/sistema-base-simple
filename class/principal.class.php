<?php

class Principal
{
	private $consultas;
	private $template;
	private $ruta;
	private $sesion;
	private $id;
	private $menu;
	private $contenido_sesion;
	private $usuario;
	private $clave;
	private $id_user;
	private $id_tipo_usuario;
	private $fecha_hoy;
	private $yo;
	private $apateno;
	private $mpateno;
	private $nombres;
	private $tipo_usuario;
	private $error;
	private $fecha_hora_hoy ;


	function __construct( $id = null,$yo = null )
	{

		if(is_null($yo))
			$this->yo = $_SESSION['yo'];
		else {
			$this->yo = $yo;
		}

		$oConf    = new config();
	  	$cfg      = $oConf->getConfig();
	  	$db       = new mysqldb( $cfg['base']['dbhost'],
								 $cfg['base']['dbuser'],
								 $cfg['base']['dbpass'],
								 $cfg['base']['dbdata'] );

   		$this->consultas 			= 	new querys( $db );
   		$this->template  			= 	new template();
   		$this->ruta      			= 	$cfg['base']['template'];
		$this->id 					= 	$id;
		$this->error 				= 	$cfg['base']['error'];
		$this->fecha_hoy 			=  date("Y-m-d");
		$this->fecha_hora_hoy		=  date("Y-m-d H:i:s");
		//$this->nombre 				= (isset($_SESSION['nombre'])) ? $_SESSION['nombre'] : null;
		$this->tipo_usuario         = $_SESSION['tipo_usuario'] ;
		$this->menu  				=  new Menu( $this->yo , $this->tipo_usuario );
	}

	private function control()
	{
		switch ($this->id)
		{
			case 'inicio-imatek' :
			case 'inicio':
				return $this::content() ;
			break;
			case 'logged':
				return $this::logged();
				break;

			case 'logged-imatek':
				return $this::loggedImatek();
				break;

			default:
				return $this->error;
				break;
		}
	}

	/**
	 * logged(): la primera funcion de inicio que da pie a la interfaz principal
	 * @return string
	 */
	private function logged()
	{
		$data = ['@@@TITLE'  	=> 'Sistema de Tickets',
				 '@@@USER' 		=> null ,
				 '@@@FECHA' 	=> $this::arreglaFechas(  $this->fecha_hoy ),
				 '@@@CONTENT'	=> $this::content(),
				 '@@@MENU'      => $this->menu->getCode()

					 ];
	return $this::despliegueTemplate($data,'inicio-principal.html');
	}


	private function loggedImatek()
	{	

		switch ($this->tipo_usuario) {
			case 2:
				
				$content = $this::content();
				break;

			case 5 : 
				$content = $this::contentTecnicos() ;
				break;
			
			default:
				$content = "{$this->tipo_usuario} no registrado";
				break;
		}

		//return "Módulo en construccion";
		$data = ['@@@TITLE'  	=> 'Sistema de Tickets',
				'@@@USER' 		=> null,
				'@@@FECHA' 		=> $this::arreglaFechas(  $this->fecha_hoy ),
				'@@@CONTENT'	=> $content,
				'@@@MENU'      	=> $this->menu->getCode(),
				'###tags###'	=> $this::tags()
				

			];
		return $this::despliegueTemplate($data,'inicio-principal.html');
	}


	public function tags(){
		try {
			//code...
			require_once('tickets.class.php') ;
			$obj = new Tickets() ;
			return $obj->tags() ;

		} catch (\Throwable $th) {
			//throw $th;
			return "Error de clase {$th}";
		}
	}


	/**
	 * content() : despliega el contenido inicial de la página
	 * 
	 * @return string
	 */
	private function content()
	{
		try {
			
			require_once("tickets.class.php") ;
			$ob_tickets = new Tickets('lista-tickets');
			return $ob_tickets->getCode() ;

		} catch (\Throwable $th) {
			
			return "Error al cargar archivo de clases" ;
		}
	}

	/**	
	 * contentTecnicos() : interfaz de inicio de los técnicos
	 * 
	 */
	private function contentTecnicos(){
		try {
			require_once('cliente.class.php') ;
			$ob_cliente = new Cliente('lista-centros') ;
			return $ob_cliente->getCode();

		} catch (\Throwable $th) {
			//throw $th;
			return $th ;
		}
	}



	/**
	 * arreglaFechas()
	 * @param string fecha
	 * @return string
	 */
	private function arreglaFechas( $fecha = null )
	{
			$div = $this::separa( $fecha , '-'  );

			if( count( $div ) > 0 )
					return "{$div[2]}-{$div[1]}-{$div[0]}";
			else return "Error de Formato";
	}


	private function separa($cadena=null,$simbolo=null)
	{
		if( is_null($cadena) )
			return "";
		else
			return explode($simbolo,$cadena);
	}

	 /**
	  * despliegueTemplate(), metodo que sirve para procesar los templates
	  *
	  * @param  array   arrayData (array de datos)
	  * @param  array   tpl ( template )
	  * @return string
	  */
    private function despliegueTemplate($arrayData,$tpl){

     	  $tpl = $this->ruta.$tpl;

	      $this->template->setTemplate($tpl);
	      $this->template->llena($arrayData);

	      return $this->template->getCode();
	  }

	/**
	 * getCode(): salida pública de control()
	 * @return string
	 */
	public function getCode()
	{
		return $this::control();
	}
}
?>
