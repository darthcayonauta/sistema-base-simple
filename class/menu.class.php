<?php
/**
 * class : Menu
 * @author    : Claudio Guzmán Herrera
 * @version   : 1.0
 * @package   : tickets / tickets-demo
 * @copyright : cgh
 */
class Menu
{

  private $id_tipo_user;
  private $template;
  private $ruta;
  private $consultas;
  private $yo;
  private $usr ;
  private $error;
  private $fecha_hora_hoy;
  private $fecha_hoy;
  private $tipo_usuario ;
  private $nombres ;
  private $apellidos ;
  private $nombreCentro ;

  function __construct(  $yo           = null,
                         $id_tipo_user = null )
  {

      $this->id_tipo_user       = $id_tipo_user;
      $oConf                    = new config();
  	  $cfg                      = $oConf->getConfig();
  	  $db                       = new mysqldb( 	 $cfg['base']['dbhost'],
  															                 $cfg['base']['dbuser'],
  															                 $cfg['base']['dbpass'],
  															                 $cfg['base']['dbdata'] );

      $this->consultas 					= new querys( $db );
      $this->template  					= new template();
      $this->ruta      					= $cfg['base']['template'];
  		$this->error 							= $cfg['base']['error'];
  		$this->fecha_hoy 					= date("Y-m-d");
  		$this->fecha_hora_hoy 		= date("Y-m-d H:i:s");
      $this->yo                 = $yo;
      $this->tipo_usuario       = $_SESSION['tipo_usuario'] ;
      $this->nombres            = $_SESSION['nombres'] ;
      $this->apellidos          = $_SESSION['apellidos'] ;
      $this->nombreCentro       = ( isset($_SESSION['nombreCentro']) ) ? $_SESSION['nombreCentro'] : null ;

      $nombreCentro = ($this->tipo_usuario == 1) ? "<br>{$this->nombreCentro}" : null ;

      $this->usr = ($this->tipo_usuario == 2) ? "{$this->nombres} {$this->apellidos}" : "{$this->nombres} {$this->apellidos} {$nombreCentro}";

  }

  /**
   * control() : clase de control, despliega el navbar en su conjunto
   * 
   * @return string
   */
  private function control()
  {

    //target de 'home'
     switch ($this->id_tipo_user ) {
      case 1:
         $target = 'aW5pY2lv' ;
        break;

      case 2:
      case 3:
      case 4:
      case 6:

        $target = 'aW5pY2lvLWltYXRlaw==' ;
        break;

      case 5:
        $target = 'bGlzdGEtY2VudHJvcw==' ;
        break ;
      
      default:
 
        break;
     }

     //link de salida
     switch ($this->tipo_usuario) {
        case 1:
        case 3:
        case 4:
        case 6:
          $salida = 'usuario_salir.php' ;
          # code...
          break;
        
        default:
          # code...
          $salida = 'usuario_imatek.php' ;
          break;
     }
      //usuario
  
      $data = array('@@@LI-CONJUNTO' => $this::enlaces(),
                    '@@@yo'          => $this->yo , 
                    '###target###'   => $target  ,
                    '###salida###'   => $salida , 
                    '@@@usuario@@@'  => $this->usr);

      return $this::despliegueTemplate( $data,"navbar.html" );
  }

  /**
   * enlaces() : clase que despliega los componentes de menu , si su dropdown es 1 despliega submenu
   * 
   * @return string
   */
  private function enlaces()
  {
      $code = "";
      $arr  = $this->consultas->menu( $this->id_tipo_user );

      $i = 0;

      foreach ($arr['process'] as $key => $value)
      {
        $class ="";
        if( $i < ( $arr['total-recs'] -1)  )
        $class = "";

          if( $value['dropdown']  == 0 )
          {
            $data = array('@@@DROPDOWN'     =>'',
                          '@@@TOGGLE'       =>'',
                          '@@@ID-LINK'      => $value['id_link'],
                          '@@@LINK'         => $value['link'].'?id='.base64_encode( $value['id_link'] ),
                          '@@@RESTO'        => '',
                          '@@@DESCRIPCION'  => $value['descripcion'],
                          '@@@SUBMENU'      => '',
                          '@@@class'        => $class);

            $code.= $this::despliegueTemplate( $data, "navbar-li.html" );

          }else {

            $data = array('@@@DROPDOWN'     =>'dropdown',
                          '@@@TOGGLE'       =>'dropdown-toggle',
                          '@@@ID-LINK'      => $value['id_link'],
                          '@@@LINK'         => $value['link'],
                          '@@@RESTO'        => 'id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"',
                          '@@@DESCRIPCION'  => $value['descripcion'],
                          '@@@SUBMENU'      => $this::submenu( $value['id'] ),
                          '@@@class'        => $class
                          );

            $code.= $this::despliegueTemplate( $data, "navbar-li.html" );
          }

         $i++;
      }

      return $code;
  }

  /**
   * submenu() : muestra los elementos del submenu
   * 
   * @param int id_menu
   * @return string
   */
  private function submenu( $id_menu = null  )
  {

    $data = array( '@@@enlaces' => $this::enlacesSubmenu( $id_menu ) );
    return $this::despliegueTemplate( $data, "navbar-submenu-principal.html" );

  }


  /**
   * enlacesSubmenu() : miestra el contenido del submenu
   * 
   * @param int id_menu
   * @return string
   */
  private function enlacesSubmenu( $id_menu = null )
  {
    $code = "";

    $arr = $this->consultas->sub_menu( $id_menu );

    foreach ($arr['process'] as $key => $value) {

      $data = array('@@@ID-LINK'      => base64_encode( $value['id_link'] ),
                    '@@@LINK'         =>   $value['link'] ,
                    '@@@DESCRIPCION'  => $value['descripcion']
                  );

      $code.= $this::despliegueTemplate( $data, "navbar-final.html" );
    }

    return $code;
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
