<?php
/**
 * @author Ing. Claudio Guzman Herrera
 * @version 1.0
 */
class FTP
{
  private $ftpUser;
  private $ftpPass;
  private $ftpFolder;
  private $ftpPort;
  private $ftpHost;
  private $nombreObjeto;
  private $token;

  function __construct( $nombreObjeto = null, $token = null  )
  {
    $this->ftpPort        = 21;
    $this->ftpUser        = "tickets";
    $this->ftpPass        = "x";
    $this->ftpFolder      = "/public_html/dox"; //entorno de desarrollo
   // $this->ftpUser        = "imatekcl";
    //$this->ftpPass        = "@Id270121";
    //$this->ftpFolder      = "/public_html/tickets/dox";
    $this->ftpHost        = "localhost";
    $this->nombreObjeto   = $nombreObjeto;
    $this->token          = $token;
  }

  /**
   * procesaFTP() :  proceso de subida de archivo mediante FTP
   * @params
   * @return boolean
   */
  public function procesaFTP()
  {
    $conn = ftp_connect($this->ftpHost,$this->ftpPort);

    if ( $conn )
      if( ftp_login( $conn , $this->ftpUser, $this->ftpPass ) )
        if( ftp_chdir($conn, $this->ftpFolder ))
            if(ftp_put($conn , $this::changeNameFile(  $_FILES[$this->nombreObjeto]["name"] ),
                                                       $_FILES[$this->nombreObjeto]["tmp_name"],
                                                       FTP_BINARY))
                  return  true;
            else 	return false;
            else 	return false;
            else 	return false;
            else 	return false;
  }

  /**
     * validaFile(): validacion del tipo de archivo que es ingresado al sistema. sólo acepta los desplegados en el switch
     * @param string fileName
     * @return boolean
     */
	public function validaFile( $fileName=null )
  	{
      $arr = $this::separa( $fileName,".");
      $tam = count($arr);

	    if(  count( $arr ) > 1 )
	    {
        //agarras la última parte del DIV
	      switch ( $arr[ $tam - 1 ] ) {
                case 'ogg':
                case 'OGG':
                case 'DOCX':
                case 'DOC':
                case 'PNG':
                case 'JPEG':
                case 'JPG':
                case 'PDF':
                case 'pdf':
                case 'jpg':
                case 'jpeg':
                case 'png':
                case 'xls':
                case 'xlsx':
                case 'doc':
                case 'docx':
                return true;
                break;

              default:
                return false;
                break;
	            }
	    }else
	        return false;
  }

    /**
      * changeNameFile(): cambia el nombre de una cadena añadiendo el token definido en el constructor
      * @param string fileName
      * @return string
      *  */
     public function changeNameFile( $fileName = null )
     {
         $div = $this::separa( $fileName,'.' );
         $tam = count($div);

         if( $tam > 1)
            return $div[0].'-'.$this->token.'.'.$div[$tam-1];
         else
            return $fileName;
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
}
 ?>
