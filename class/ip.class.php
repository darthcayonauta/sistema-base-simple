<?php

class Ip{

    private $ip ;

    function __construct(){

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {		    
            $this->ip = $_SERVER['HTTP_CLIENT_IP'];

        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $this->ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

        } else {
            $this->ip = $_SERVER['REMOTE_ADDR'];
        }
    }

    public function myIp(){
        return $this->ip ;
    }
}


?>