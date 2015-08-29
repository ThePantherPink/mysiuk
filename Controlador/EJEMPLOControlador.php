<?php
	require_once("../../modelo/ModeloEmpleado.php");
	
	/*
		ControladorCliente es el intermediario entre la vista y el modo que gestiona todas las operaciones sobre
		el empleado.
	*/
	
	class ControladorEmpleado
	{
		private $ModeloEmpleado=NULL; //Este es el ModeloEmpleado , se configura una vez creado el objeto del controlador
		
		/* Este metodo toma del modelo (ModeloEmpleado) la instancia */
		function __construct()
		{
			$this->ModeloEmpleado=ModeloEmpleado::getInstance(); //a partir de un singleton,obtengo el objeto del modelo y lo guardo en $ModeloEmpleado
		}
		/*
			Este metodo registra un empleado dado un arreglo $_REQUEST[] que llega a partir de la vista.
			$arreglo = $_REQUEST[]
		*/
		function registrar($arreglo)
		{
			$empleado=new Empleado($arreglo); //Creo un empleado
			return $this->ModeloEmpleado->registrar($empleado); //Registro los clientes
		}
		
		function obtener_todos()
		{
			return $this->ModeloEmpleado->ObtenerTodos();
		}
		function modificar($dni,$arreglo)
		{
			return $this->ModeloEmpleado->modificar($dni,$arreglo); //modifico el empleado
		}
		function asistencia($dni)
		{
			return $this->ModeloEmpleado->asistenciaEmpleado($dni); //modifico el empleado
		}
		function Despedirse($dni)
		{
			return $this->ModeloEmpleado->despedirseEmpleado($dni); //modifico el empleado
		}
	}
?>
