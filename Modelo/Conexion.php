<?php
//Clase afectada por singleton
class Conexion
{
  static private $instancia = NULL;
  private function __construct(){}
	

  static public function getInstance() 
  {
    if (self::$instancia == NULL)self::$instancia = new mysqli("localhost","root","","mysiuk");
    return self::$instancia;
  }
		
}
?>
