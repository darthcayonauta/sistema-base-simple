<?php 
//sistema de accesos
class Accesos{
    private $consultas;
	private $template;
	private $ruta;
	private $sesion;
	private $id;
	private $fecha_hoy;
	private $yo;

    function __construct( $id = null ){
        $this->id = $id;
        
        $oConf    = new config();
        $cfg      = $oConf->getConfig();
        $db       = new mysqldb(  $cfg['base']['dbhost'],
                                    $cfg['base']['dbuser'],
                                    $cfg['base']['dbpass'],
                                    $cfg['base']['dbdata'] );
    
        $this->consultas 			= 	new querys( $db );
        $this->template  			= 	new template();
        $this->ruta      			= 	$cfg['base']['template'];
        $this->error 				= 	"Error 404:  <strong>{$this->id}</strong> no definido";
        $this->fecha_hoy 			=  date("Y-m-d");
        $this->year 			    =  date("Y");
        $this->mes 			        =  date("m");    
        $this->fecha_hora_hoy	    =  date("Y-m-d H:i:s");
        $this->yo                   = $_SESSION['yo'];
        $this->token 		        =  date("YmdHis");
        $this->tipo_usuario         = (isset($_SESSION['tipo_usuario'])) ? $_SESSION['tipo_usuario'] : 1 ;
        $this->mail_imatek          = "claudio@imatek.cl" ;

    }

private function control (){
    switch ($this->id) {
        case 'accesos':
            # code...
            return $this::accesos() ;
            break;
        
        default:
            # code...
            return $this->error ;
            break;
    }
}


    private function accesos(){
        $data = ['###listado###' => $this::tabla()] ;
        return $this::despliegueTemplate( $data , 'accesos/accesos.html' ) ;
    }


    private function tabla(){
        $arr = $this::tr() ;

        $data = [ '###tr###' => $arr['code'] , '###total-recs###' => $arr['total-recs'] ] ;
        return $this::despliegueTemplate( $data , 'accesos/tabla.html' ) ;

    }

    private function tr(){
        $code = "" ;
        $counter = 0 ;

        $arr = $this->consultas->accesos() ;

        
        foreach ($arr['process'] as $key => $value) {
            $usuario = ( $value['usuario'] == '' || !$value['usuario'] ) ? 'No registrado' : $value['usuario'] ;
            # code...
            $data = ['###counter###' => $counter+1 , 
                     '###ip###'      => $value['ip'] , 
                     '###fecha###'   => $value['fecha'] ,
                     '###nombre###'  => $value['nombre'] , 
                     '###usuario###' => $usuario] ;

            $code .= $this::despliegueTemplate( $data , 'accesos/tr.html' ) ;
            $counter++;
        }

        $out['code'] = $code ;
        $out['total-recs'] = $arr['total-recs'] ;

        return $out ;
    }



 /**
   * modal(): extrae un modal desde una Clase
   *
   *@param string target
   *@param string img
   *@param string title
   *@param string content
   */
  private function modal( $target = null,$img = null, $title = null, $content = null )
  {
      require_once("modal.class.php");

      $ob_modal = new Modal($target ,$img , $title , $content );
      return $ob_modal->salida();
  }

  /**
   * notificaciones()
   * @param string tipo_alerta
   * @param string icon
   * @param string glosa
   * @return string
    */
   private function notificaciones( $tipo_alerta = null, $icon= null, $glosa = null )
   {
       return $this::despliegueTemplate( array( '@@@tipo-alert@@@' => $tipo_alerta,
                                                '@@@icon@@@'       => $icon,
                                                '@@@glosa'         => $glosa) , 'notificaciones.html' );
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