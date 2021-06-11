<?php

// libreria nusoap
require_once 'lib/nusoap.php';

// url del servicio
$endpoint = 'http://localhost/Projects/NUSOAP/service.php';

//! instanciar nuevo objeto nusoap
// nusoap_client(urlServicio, false)    -> sin wsdl como parametro (urlServicio?wsdl)
// nusoap_client(urlServicio, true)     -> con wsdl como parametro (urlServicio?wsdl)
$client = new nusoap_client($endpoint, false);

// capturar el codigo del curso
$id = isset($_POST['id']) ? $_POST['id'] : '';

// guardamos los datos en un array
$param = array('id' => $id);

//! capturar error de creacion de cliente
$err = $client->getError();
// mostrar error si hay
if ($err) {
	echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
}

//! ejecutar servicio
//* si id vacio, listar cursos
if ($id == '') {
	// call(nombreFuncionServicio, parametros)
	$result = $client->call('listarCursos', '');
//* si id tiene valor, trear curso por id
} else {
	// call(nombreFuncionServicio, parametros)
	$result = $client->call('getCurso', $param);
}

//! control de errores
if ($client->fault) {
	echo '<h2>Fault</h2><pre>';
	print_r($result);
	echo '</pre>';
} else {	
    // capturar error de creacion de cliente
	$err = $client->getError();
    // si hay error, mostrarlo
	if ($err) {	
		echo '<p><b>Error: ' . $err . '</b></p>';
    // si no hay error, mostrar resultado
	} else {
		echo 'Resultado: ';
		print_r ($result);
	}
}