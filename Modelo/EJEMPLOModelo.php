
<?php
	require_once('Conexion.php');
	require_once('Entidades/empleado.php');
	//Clase afectada por singleton
	class ModeloEmpleado	
	{
		private $empleado; // arreglo que tiene todos los empleados.
		static private $instancia=NULL;
		
		private function __construct(){}	
		
		
		static public function getInstance() 
		{
			if (self::$instancia == NULL) 
			{
				self::$instancia = new ModeloEmpleado();
			}
			return self::$instancia;
		}
		
		function faltaAsistencia($dni) //funcion que revisa que halla una entrada sin salida del empleado.
		{
			$fecha_ahora = date('Y-m-d')."%";	//fecha que usaremos para comprarar

			$query = "SELECT ID_TRABAJO FROM `trabajo` WHERE DNI_EMPLEADO = ? AND H_ENTRADA LIKE ? AND H_SALIDA IS NULL";	//formulamos consulta
			$preparando=Conexion::getInstance()->prepare($query);					//preparamos consulta
			$preparando->bind_param("ss", $dni, $fecha_ahora);						//pasamos parametros de consulta
			$preparando->bind_result($id_trabajo);									//elegimos donde guardar el resultado
			$preparando->execute();													//si-> consulta ejecutada
			
			if(!$preparando->fetch()){
				$preparando->close();
				return false;														//no existe asistencia sin salida marcada.
			}
			$preparando->close();
			return true;															//aun no se ha marcado la salida para la asistencia.
			
		}
		
		function faltaDespedida($dni) //funcion que revisa que NO halla una entrada sin salida del empleado.
		{
			$fecha_ahora = date('Y-m-d')."%";	//fecha que usaremos para comprarar

			$query = "SELECT ID_TRABAJO FROM `trabajo` WHERE DNI_EMPLEADO = ? AND H_ENTRADA LIKE ? AND H_SALIDA IS NULL";	//formulamos consulta
			$preparando=Conexion::getInstance()->prepare($query);					//preparamos consulta
			$preparando->bind_param("ss", $dni, $fecha_ahora);						//pasamos parametros de consulta
			$preparando->bind_result($id_trabajo);									//elegimos donde guardar el resultado
			$preparando->execute();													//si-> consulta ejecutada
			
			if(!$preparando->fetch()){
				$preparando->close();
				return true;														//no existe asistencia sin salida marcada.
			}
			$preparando->close();
			return false;															//aun no se ha marcado la salida para la asistencia.
			
		}
		
		function existeEmpleado($dni)	//revisa la existencia del empleado.
		{
			$query = "SELECT DNI_EMPLEADO FROM `empleado` WHERE DNI_EMPLEADO = ?";	//formulamos consulta
			$preparando=Conexion::getInstance()->prepare($query);					//preparamos consulta
			$preparando->bind_param("s", $dni);										//pasamos parametros de consulta
			$preparando->bind_result($dni_empleado);								//elegimos donde guardar el resultado
			$preparando->execute();													//si-> consulta ejecutada
			
			if(!$preparando->fetch()){
				$preparando->close();
				return false;														//no existe el empleado.
			}
			$preparando->close();
			return true;
			
		}
		
		function registrar($empleado) //registar un empleado,dado un empleado.
		{	
			$dni1 	=$empleado->getDni();
			$nom1 	=$empleado->getNombre();
			$ape1	=$empleado->getApellido();
			$contr1	=$empleado->getContrasenia();
			$fech1 	=$empleado->getFecha_ini();

			if($this->existeEmpleado($dni1)){return "1";}				//verificamos la existencia del empleado.
			
			$query = "INSERT into empleado (DNI_EMPLEADO, NOMBRE, APELLIDO, CONTRASENIA, FECHA_INICIO) VALUES (?,?,?,?,?)";
			$preparando=Conexion::getInstance()->prepare($query);
			$preparando->bind_param("issss",$dni1,$nom1,$ape1,$contr1,$fech1);
			
			if($preparando->execute())
			{
				
				return "2"; //todo OK
				
			}
			else
			{	
				return "3"; //error en la consulta
			};
		}

		function modificar($dni,$arreglo) //modificar un empleado, dado un empleado y su numero dni.
		{
			$nom1 	= $arreglo['nombrempleado'];
			$ape1	= $arreglo['apellido'];
			$contr 	= $arreglo['password'];
			$fech1 	= $arreglo['Fechadeinicio'];
		
			
			$consulta="UPDATE empleado set FECHA_INICIO=?, NOMBRE=?, APELLIDO=?, CONTRASENIA=? WHERE DNI_EMPLEADO=?";
			$preparando=Conexion::getInstance()->prepare($consulta);
			$preparando->bind_param("sssss",$fech1,$nom1,$ape1,$contr,$dni); //configuramos la consulta
			if($preparando->execute()) //ejecutamos la consulta
			{
				$preparando->close();
				return  true;
			}
			else
			{
				$preparando->close();
				return false;
			}
		}

		function ObtenerTodos() //mostrar todos los empleados.
		{
			$query = "SELECT FECHA_INICIO, FECHA_FIN, DNI_EMPLEADO, NOMBRE, APELLIDO,CONTRASENIA FROM empleado order by APELLIDO";
			$preparando=Conexion::getInstance()->prepare($query);
			$preparando->bind_result($fech1,$fech2,$dni1,$nom1,$ape1,$pass);
			if($preparando->execute()){
				$resultado=Array();
				$contador=0;
			
				while($preparando->fetch())
				{	

					$arreglo=Array();
					$arreglo['fecha_ini']=$fech1;
					$arreglo['fecha_fin']=$fech2;
					$arreglo['dni']=$dni1;
					$arreglo['nombre']=$nom1;
					$arreglo['apellido']=$ape1;
					$arreglo['password']=$pass;
					//$resultado[$contador]=new Empleado($arreglo);
					$resultado[$contador]=$arreglo;
					$contador++;
				}
				return $resultado;
			}
			else
			{
				return false;
			}
		}

		function asistenciaEmpleado($dni) //registrar una asistencia, dado un dni.
		{
		
			$fecha_s = date('Y-m-d H:i:s');  //fecha de entrada

			if(!$this->existeEmpleado($dni))	{return "0";}				//verificamos la existencia del empleado.
			if(!$this->faltaDespedida($dni))	{return "2";}				//verificamos que no halla otra asistencia sin despedida.
			
				
			$query1 = "INSERT INTO trabajo (H_ENTRADA, DNI_EMPLEADO) VALUES (?,?) ";
			$preparando=Conexion::getInstance()->prepare($query1);
			$preparando->bind_param("ss",$fecha_s,$dni);
			if($preparando->execute())									//si-> consulta ejecutada
			{	
				$preparando->close();
				return "1";												//asistencia registrada.
			}	else
					{
					$preparando->close();
					return "3";											//asistencia no registrada
					};
			
			
		}	

		function despedirseEmpleado($dni) //registrar una salida, dado un dni.
		{
			$fecha_s = date('Y-m-d H:i:s');  //fecha de salida
			$fecha_ahora = date('Y-m-d')."%";	//fecha que usaremos para comprarar

			if(!$this->existeEmpleado($dni))	{return "0";}				//verificamos la existencia del empleado.
			if(!$this->faltaAsistencia($dni))	{return "2";}				//verificamos la existencia de la asistencia sin salida.
			
			
			
			
			
			$query = "SELECT ID_TRABAJO FROM `trabajo` WHERE H_ENTRADA LIKE ? AND DNI_EMPLEADO = ? AND H_SALIDA IS NULL";//formulamos consulta
			$preparando=Conexion::getInstance()->prepare($query);		//preparamos consulta
			$preparando->bind_param("ss",$fecha_ahora, $dni);			//pasamos parametros de consulta
			$preparando->bind_result($id_trabajo);						//elegimos donde guardar el resultado
			$preparando->execute();										//ejecutamos la consulta
				
			$preparando->fetch();
			$preparando->close();
			$query1 = "UPDATE trabajo set H_SALIDA=? WHERE ID_TRABAJO = ?";
			$preparando=Conexion::getInstance()->prepare($query1);
			$preparando->bind_param("ss",$fecha_s,$id_trabajo);
				if($preparando->execute())									//si-> consulta ejecutada
				{	
					return "1";												//salida registrada
				} else
					{
					return "3";												//salida no registrada
					};
		}

		function modificarContrasenia($dni,$contrasenia, $nuevacontrasenia) //modificar la contrasenia del empleado, dado un dni, la contrasenia actual y la nueva contrasenia.
		{
			
			$consulta="UPDATE empleado set CONTRASENIA=? WHERE DNI_EMPLEADO=? AND CONTRASENIA = ?";
			$preparando=Conexion::getInstance()->prepare($consulta);
			$preparando->bind_param("sss",$nuevacontrasenia,$dni, $contrasenia); //configuramos la consulta
			if($preparando->execute()) //ejecutamos la consulta
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		
		
		
		
		
		
		
		
		
}		
		
	/*$arreglo=Array();
	$arreglo['dni']="32020244";
	$arreglo['nombre']="Poste";
	$arreglo['apellido']="Undo";
	$arreglo['contrasenia']="1234567890";
	$arreglo['fecha_ini']="2001-12-25";
	$arreglo['fecha_fin']="2009-10-05";
	$empleado1=new Empleado($arreglo); //Creo un empleado*/
	
	//$modeloc=ModeloEmpleado::getInstance();
	//$modeloc->registrar($empleado1);
	//$dni="33669665";
	//$modeloc->registrar($empleado1);
	//$modeloc->modificar($dni, $empleado1);  revisar el modificar contrasenia, no guarda mas de 10 numeros quizas mejor hacerlo string
	/*
	$dni 	= '33669665';
	$actual	= 'edgardo';
	$nueva	= '66825495';*/
	//$modeloc->asistenciaEmpleado($dni);
	//$modeloc->despedirseEmpleado($dni);
?>
