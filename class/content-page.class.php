<?php
//require_once('index.class.php');

class ContentPage
{
	private $consultas;
	private $template;
	private $ruta;
	private $id;
	private $menu;
	private $fecha_hoy;
	private $yo;
	private $id_tipo_user;
	private $error;
	private $fecha_hora_hoy;
	private $tipo_usuario;
	private $nombre;
	private $nombres;
	private $apellidos;

	function __construct( $id = null )
	{

	$oConf    				= new config();
	$cfg      				= $oConf->getConfig();
	$db       				= new mysqldb(  $cfg['base']['dbhost'],
											$cfg['base']['dbuser'],
											$cfg['base']['dbpass'],
											$cfg['base']['dbdata'] );

    $this->consultas 		= new querys( $db );
    $this->template  		= new template();
    $this->ruta      		= $cfg['base']['template'];
    $this->id 				= $id;
    $this->error 			= $cfg['base']['error'];
    $this->fecha_hoy 		= date("Y-m-d");
    $this->fecha_hora_hoy 	= date("Y-m-d H:i:s");
	//$this->ob_index 		= new Index();
	//$this->nombre 				= (isset($_SESSION['nombre'])) ? $_SESSION['nombre'] : null;
	$this->tipo_usuario = $_SESSION['tipo_usuario'] ;

	$this->menu  			=  new Menu( $this->yo , $this->tipo_usuario );

	}

	/**
	 * control(): algoritmo de deciciones
	 * @return string
	 */
	private function control()
	{
		switch ($this->id)
		{	

			case 'inicio':

				return $this::baseHtml();
				break;

			default:
				return $this::baseHtmlError();
				break;
		}
	}

	/**
	 * baseHtml(): despliegue del contenido de un enlace desde menu o link
	 * @return string
	 */
	private function baseHtml()
	{
        if (!isset( $_GET['id']  ))
        {       $content = "CONTENIDO INICIAL DE LA PAGINA  <br>" .$this->id ;
        }else{  $content = $this::importaModulos(); }

			//$nombre = ($this->tipo_usuario == 1) ? $this->nombre : "{$this->nombres} {$this->apellidos}";


			$data = ['@@@TITLE'  	=> 'Sistema de Tickets',
					 '@@@USER' 		=> null,
					 '@@@FECHA' 	=> $this::arreglaFechas(  $this->fecha_hoy ),
					 '@@@CONTENT'	=> $content,
					 '@@@MENU'      => $this->menu->getCode() ,
					 '###tags###'   => null 
			];

			return $this::despliegueTemplate($data,'inicio-principal.html');
	}

	/**
	 * baseError(): despliegue del contenido de un enlace desde menu o link, cuando es error
	 * @return string
	 */
	private function baseHtmlError()
	{
		$nombre = ($this->tipo_usuario == 1) ? $this->nombre : "{$this->nombres} {$this->apellidos}";

		$data = ['@@@TITLE'  	=> 'Sistema de Tickets',
				 '@@@USER' 		=> null,
				 '@@@FECHA' 	=> $this::arreglaFechas(  $this->fecha_hoy ),
				 '@@@CONTENT'	=>"{$this->error} ::: {$this->id}",
				 '@@@MENU'      => $this->menu->getCode()
			];
		return $this::despliegueTemplate($data,'inicio-principal.html');
	}


    /**
     * importaModulos()
     * @param
     * @return string
     */
    private function importaModulos()
    {

      switch ($this->id) {

			case 'inicio':
				return $this::generalCall( 'principal.class.php',
										   'Principal', $this->id );
			break;

      default:
        # code...
				return $this->error;
      		break;
        }
    }

    /**
     * generalCall()
     * @param string file
     * @param string className
     * @param string idItem
     * @return string
     */
    private function generalCall( $file        = null,
                                  $className   = null,
                                  $idItem      = null   )
    {
        if( require_once( $file ) ) { $ob = new $className($idItem); return $ob->getCode();   }
        else return "error al cargar clase";
    }

    /**
     * arreglaFechas()
     * @param string fecha
     * @return string
     */
    private function arreglaFechas( $fecha = null )
    {
        $div = $this->separa( $fecha , '-'  );

        if( count( $div ) > 0 )
            return "{$div[2]}-{$div[1]}-{$div[0]}";
        else return "Error de Formato";
    }

    /**
     * separa()
     * @param string cadena
     * @param string simbolo
     * @return string
     */
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
	  * @return String
	  */
    private function despliegueTemplate($arrayData,$tpl){

     	  $tpl = $this->ruta.$tpl;

	      $this->template->setTemplate($tpl);
	      $this->template->llena($arrayData);

	      return $this->template->getCode();
	  }

	public function getCode()
	{
		return $this::control();
	}
}
?>
