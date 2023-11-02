<?php
/**
* @class   : Usuarios
* @author  : Claudio Guzman Herrera
* @version : 1.0
* @package : TICKETS
* @date    : MARZO-2023
* 
*/
class Usuarios{
    private $id ;
    private $consultas ;
    private $template ;
    private $ruta ;
    private $error ;
    private $fecha_hora_hoy;
    private $fecha_hoy;
    private $year;
    private $mes ;
    private $yo;
    private $token ;
    private $tipo_usuario ;
    private $mail_imatek ;



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

    private function control(){
        switch ($this->id) {

            case 'actualizaDataUsuario':
                # code...
                return $this::actualizaDataUsuario() ;
                break;

            case 'editarCliente':
                # code...
                return $this::editarCliente() ;
                break;

            case 'eliminaCliente':
                # code...
                return $this::eliminaCliente() ;
                break;

            case 'ingresaDataUsuario':
                # code...
                return $this::ingresaDataUsuario() ;
                break;

            case 'crear-usuario':
                # code...
                return $this::crearUsuario() ;
                break;

            case 'listar-usuarios':
                    # code...
                return $this::listarUsuarios() ;
                break;          

            case 'cambiar-clave-cliente':
            case 'cambiar-clave-imatek':
                # code...
                return $this::formCambiaClaveImatek() ;
                break;
            
            case 'cambiaClave2':
            case 'cambiaClave1':
                # code...
                return $this::cambiaClave1() ;
                break;

            default:
                # code...
                return $this->error ;
                break;
        }
    }

    private function actualizaDataUsuario(){
        //print_r($_POST) ;
           /*
       Array ( [nombres] => claudio 
       [apellidos] => guzman 
       [email] => cguzmanherr@gmail.com 
       [fono] => +56 9 30185006 
       [cliente_centro] => 8 
       [id_tipo_usuario] => 1 [id_cliente] => 1 
       [id] => actualizaDataUsuario )
       
       */

      if( $this->consultas->userHandle( $_POST['nombres'], 
                                        $_POST['apellidos'],
                                        $_POST['email'] , 
                                        null, 
                                        $_POST['id_tipo_usuario'] , 
                                        $_POST['cliente_centro'],
                                        $_POST['fono'] , $_POST['id_cliente']) ) {

        $icon = "<i class='fas fa-thumbs-up'></i>";
        $msg  = " Usuario Actualizado" ;
        $color = 'success'; 

      }else{
        $icon = "<i class='fas fa-thumbs-down'></i>";
        $msg  = " Error al actualizar " ;
        $color = 'success'; 
      }

      return $this::notificaciones($color, $icon, $msg);

    }


    private function editarCliente(){
     //   print_r($_POST) ;
     $out = "";
     $cliente = $this->consultas->usuariosSistema( $_POST['id_cliente'] ) ;

     foreach ($cliente['process'] as $key => $value) {
        # code...

        $empresa = $this->consultas->empresa() ;
        $empresa = new Select($empresa['process'],
                'id',
                'descripcion',
                'id_empresa',
                'Cliente' , $value['id_empresa']) ;

        $centro = $this->consultas->clienteCentro( $value['id_empresa'] ) ;
        $centro = new Select($centro['process'],
                'id',
                'nombre',
                'cliente-centro',
                'Centro' , $value['id_centro']) ;

        $tipo_usuario = $this->consultas->tipo_usuario() ;
        $tipo_usuario = new Select($tipo_usuario['process'],
                'id',
                'descripcion',
                'id_tipo_usuario',
                'Tipo de Usuario' , $value['tipo_usuario']) ;

        $hidden = '<input type="hidden" id="id_cliente" 
                    name="id_cliente" 
                    value="'.$value['id'].'">' ;

        $data =['###title###'               => 'Edición',
                '###nombres###'             => $value['nombres'],
                '###apellidos###'           => $value['apellidos'],
                '###email###'               => $value['email'],
                '###fono###'                => $value['fono'],
                '###disabled###'            => 'disabled',
                '###select-centro###'       => $centro->getCode(),
                '###select-empresa###'      => $empresa->getCode(),
                '###select-tipo_usuario###' => $tipo_usuario->getCode(),
                '###hidden###'              => $hidden,
                '###button-id###'           => 'update'
        ] ;



        $out .= $this::despliegueTemplate( $data, 'usuarios/form-usuario.html'  ) ;

     }

     return $out;
    }

    private function eliminaCliente(){
        //Array ( [id] => eliminaCliente [id_cliente] => 10 )

      
        if ($this->consultas->eliminaUsuario($_POST['id_cliente'])){

            $icon = "<i class='fas fa-trash-alt'></i>";
            $msg  = " Usuario Eliminado" ;
            $color = 'danger'; 
        }else{
            $icon = null;
            $msg  = " Error al eliminar" ;
            $color = 'danger';  
        }

        return $this::notificaciones($color, $icon, $msg).$this::tabla() ;

    }



    private function ingresaDataUsuario(){

        $btn = "<a class='btn btn-sm btn-success'
                 href='content-page.php?id=bGlzdGFyLXVzdWFyaW9z'>Ir a listado</a>";

         if( $this->consultas->userHandle( $_POST['nombres'], 
                                           $_POST['apellidos'],
                                           $_POST['email'] , 
                                           $_POST['clave1'], 
                                           $_POST['id_tipo_usuario'] , 
                                           $_POST['cliente_centro'],
                                           $_POST['fono']) ){
           // return 'usuario ingresado' ;
     
            $icon = '<i class="fas fa-thumbs-up"></i>';
            $msg  = "Usuario Creado en Forma exitosa  {$btn}" ;
            $color = 'success';  

         }else{
        //    return "Error al ingresar ,......." ;
            $icon = '<i class="fas fa-thumbs-down"></i>';
            $msg  = 'Error al crear usuario '.$btn ;
            $color = 'danger' ;
            }
         return $this::notificaciones($color, $icon, $msg);
    }


    private function crearUsuario(){

        $empresa = $this->consultas->empresa() ;
        $empresa = new Select($empresa['process'],
                'id',
                'descripcion',
                'id_empresa',
                'Cliente') ;

        $tipo_usuario = $this->consultas->tipo_usuario() ;
        $tipo_usuario = new Select($tipo_usuario['process'],
                'id',
                'descripcion',
                'id_tipo_usuario',
                'Tipo de Usuario') ;

        
        $data = ['###select-empresa###'      => $empresa->getCode() , 
                 '###select-tipo_usuario###' => $tipo_usuario->getCode() ,
                 '###disabled###'            => null ,
                 '###nombres###'             => null,
                 '###apellidos###'           => null, 
                 '###email###'               => null , 
                 '###fono###'                => null , 
                 '###select-centro###'       => $this::selectCentroEstatico(),
                 '###hidden###'              => null,
                 '###title###'               => 'Creación',
                 '###button-id###'           => 'send'

        ] ;
        return $this::despliegueTemplate(  $data , 'usuarios/form-usuario.html') ;

    }


    private function selectCentroEstatico(){
        $sel = '  <select name="c" id="c" disabled class="form-control">
                    <option value="0">Centro</option>
                    </select>
                <input type="hidden" id="cliente-centro" name="cliente-centro" value="">';
        return $sel ;
            }


    private function listarUsuarios(){
        return $this::despliegueTemplate(['###listado###' => $this::tabla() ,
                                          '###buscar###'  => null] , 'usuarios/usuarios.html') ;
    }

    private function tabla(){
        $arr = $this::tr() ;

        $data = ['###tr###' => $arr['tr'] , '###total-recs###' => $arr['total-recs']] ;
        return $this::despliegueTemplate( $data , 'usuarios/tabla.html' ) ;
    }

    private function tr(){
    
        $usuarios = $this->consultas->usuariosSistema() ;
        $counter  = 0 ;
        
        $code = "" ;
        //echo $usuarios['sql'] ;

        foreach ($usuarios['process'] as $key => $value) {
            # code...
            $usuario = "{$value['nombres']} {$value['apellidos']}" ;
            $fono = (!is_null($value['fono'])) ? $value['fono'] : 'NO DATA' ;

            $data = ['###num###'            => $counter+1 , 
                     '###email###'          => $value['email'] ,
                     '###fono###'           => $fono,
                     '###usuario###'        => $usuario,
                     '###tipo_usuario###'   => $value['nombreTipoUsuario'],
                     '###centro###'         => $value['nombreCentro'],
                     '###empresa###'        => $value['nombreEmpresa'],
                     '###id###'             => $value['id']
            ] ;
            $code .= $this::despliegueTemplate( $data , 'usuarios/tr.html' ) ;
            $counter ++;
        }


        $out['tr'] = $code ;
        $out['total-recs'] = $usuarios['total-recs'] ;

        return $out ;
    }


    private function cambiaClave1(){
       // print_r($_POST)  ;
    

        switch ($this->id) {
            case 'cambiaClave1':
                
                $query = $this->consultas->cambiaClaveImatek( $_POST['clave'] , $this->yo );
                break;
            
            case 'cambiaClave2':
               
                $query = $this->consultas->cambiaClaveCliente( $_POST['clave'] , $this->yo );
                break;

            default:
                # code...
                break;
        }

        if( $query )
        {
            $icon = '<i class="fas fa-thumbs-up"></i>';
            $msg  = "La contraseña ha sido cambiada con éxito" ;
            $color = 'success';  
        }
        else {
            $icon = '<i class="fas fa-thumbs-down"></i>';
            $msg  = "Error al cambiar la contraseña" ;
            $color = 'danger';  
        }

        return $this::notificaciones( $color , $icon , $msg ) ;

    }


    private function formCambiaClaveImatek(){

        //target de 'home'
        switch ($this->tipo_usuario ) {
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



        switch ($this->id) {
            case 'cambiar-clave-imatek':
                # code...
                $btn_id = 'send' ;
                break;

            case 'cambiar-clave-cliente':
                # code...
                $btn_id = 'send-client' ;
                break;
            
            default:
                # code...
                break;
        }

        //return "{$this->id} en construccion";
        $data = ['###mi-id###' => $this->yo , '###button-id###' => $btn_id , '###target###' => $target] ;
        return $this::despliegueTemplate($data,'usuarios/form-imatek.html') ;
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