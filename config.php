<?php

include_once("class/mysqldb.class.php");

/**
 * config
 *
 * @author Claudio Guzman Herrera
 * @version 0.0.1
 * @package dissa
 */
class config
{
    private $data;
    /**
     *
     */
    function __construct()
    {
       // @ session_start();
        $conf = array();
        if ( isset($_SESSION['config']) ){ //si la configuracion esta en memoria
            $conf = $_SESSION['config'];
        } else { // sino leer configuraciones y cargarlas en memoria

          $home = null;

            $conf['base']['template']   =  $home.'templates/';
            $conf['base']['lang']       = 'es';

            $conf['base']['dbdata']     = 'generic';
            $conf['base']['dbuser']     = 'claudio';
            $conf['base']['dbpass']     = 'cayofilo';

   
            //$conf['base']['mail_imatek'] = 'admin-ticket@imatek.cl' ;

            $conf['base']['dbpref']     = '';
            $conf['base']['error']      = 'Error inesperado';


            $conf['base']['ftpHost']    = 'localhost';
            $conf['base']['ftpPort']    = 21;



            if ( isset($_SESSION['base']['template'])) {
                $conf['base']['template'] = $_SESSION['base']['template'];
            } else {
                $_SESSION['base']['template'] = $conf['base']['template'];
            }

            $_SESSION['base']   = $conf['base'];
            $_SESSION['config'] = $conf;
        }
        $this->data = $conf;
    }

    public function getConfig()
    {
        return $this->data;
    }
}
?>
