<?php
/**
* @class : response , llama las funciones de determinadas clases PHP , mediante
*          AJAX
* @author Claudio, cguzmanherr@gmail.com
* @version : 1.1 
* @package : Tickets
* @date    : MARZO - 2023
*/
class response
{
	private $id;
	private $id_user;

	function __construct($id=null )
	{
		$this->id = $id;
	}

	private function cargaModulos(){

		switch ($this->id)
		{
			case 'instruccion':
				return $this::obtenerContenidoClaseOption('some.class.php','Someclass');
				break;

			default:
				# code...
				return "<div class='principal'>MODULO NO DEFINIDO / TIMEOUT DE CARGA</div>";
				break;
		}
	}

/**
 * obtenerContenidoClaseOption(), obtiene un despliegue de resultados de una clase cualquiera para el metodo anterior, Alex aprende a programar
 *
 * @param  String file_class
 * @param  String class
 * @return String
 */
	private function obtenerContenidoClaseOption($file_class=null,$class=null){
	   try {

			require_once($file_class);

			$obj_class  = new $class( $this->id);
			return $obj_class->getCode();
	   } catch (\Throwable $th) {
		//throw $th;
		return null ;
	   }
	}

	public function getCode(){

		return $this->cargaModulos();
	}
}
?>
