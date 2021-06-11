<?php
// conexion base de datos
require_once 'config/Conection.php';
// libreria nusoap
require_once 'lib/nusoap.php';
// respuestas
require_once 'classes/errors/Answers.php';

// evitar XML error parsing SOAP payload on line X: Invalid document end
ini_set('display_errors', 0);

//! instanciar un nuevo objeto soap_server
$server = new soap_server();

// namespace
$ns = 'http://localhost/Projects/NUSOAP/service.php';
//! configurar servicio
// configureWSDL(nombreWebService, namespace)
$server->configureWSDL('consulta', $ns);
// almacena el espacio de nombre destino
$server->schemaTargetNamespace = $ns;
// decodificar caracteres mensaje saliente
$server->soap_defencoding = 'utf-8';
$client->encode_utf8 = false;
$client->decode_utf8 = false;

//! registrar la funcion que va a utilizar nuestro servicio
// register(nombreFuncion, parametrosEntrada, queRetorno, namespace)
// parametrosEntrada = indicar que tipo de dato es
// array(nombreParametro => tipoDato)
// xsd = define la estructura de un documento XML
$server->register(
    'listarCursos',
    array('codigo' => 'xsd:string'),
    array('return' => 'xsd:string'),
    $ns
);
$server->register(
    'getCurso',
    array('codigo' => 'xsd:string'),
    array('return' => 'xsd:string'),
    $ns
);

//! funcion listar cursos
function listarCursos()
{
    // nuevo objeto respuesta
    $respuestas = new Answers;
    //! conectar la bd
    $conn = Conection::conectar();
    //! consulta sql
    $sql = "SELECT nombre FROM cursos";
    //! guardar la consulta en memoria para ser analizada 
    $stmt = $conn->prepare($sql);
    //! ejecutar consulta
    if ($stmt->execute()) {
        // traer el curso en un array asociativo
        $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // error interno del servidor
        return $respuestas->error_500();
    }

    //! crear estructura xml y devolverla
    return cadenaXML($cursos);
}

//! funcion listar curso por id
function getCurso($id)
{
    // nuevo objeto respuesta
    $respuestas = new Answers;
    //! conectar la bd
    $conn = Conection::conectar();
    //! consulta sql
    $sql = "SELECT nombre FROM cursos WHERE id = :id";
    //! guardar la consulta en memoria para ser analizada 
    $stmt = $conn->prepare($sql);
    //! bindear parametros
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    //! ejecutar consulta
    if ($stmt->execute()) {
        // traer el curso en un array asociativo
        $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // error interno del servidor
        return $respuestas->error_500();
    }

    //! crear estructura xml y devolverla
    return cadenaXML($cursos);
}

//! armar cadena XML
function cadenaXML($cursos)
{
    //! crear estructura xml
    $cadena = "<?xml version?'1.0' encoding='utf-8'?>";
    $cadena .= '<cursos>';

    // si hay cursos
    if (count($cursos) > 0) {
        // recorrer resultados
        foreach ($cursos as $curso => $value) {
            $cadena .= '<curso>';
            $cadena .= '<br>';
            $cadena .= '<nombre>' . $value['nombre'] . '</nombre>';
            $cadena .= '<br>';
            $cadena .= '</curso>';
        }
    } else {
        $cadena .= '<error>No hay cursos</error>';
    }

    $cadena .= '</cursos>';

    // devolver respuesta
    return $cadena;
}

//! valida lo que ingresa por post
// operador ternario -> (condicion) ? verdadero : falso
// $HTTP_RAW_POST_DATA = fue deprecated en PHP 7
// se reemplaza por ejemplor -> $postdata = file_get_contents("php://input");
// $HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$peticion = file_get_contents("php://input");

//! ejecutar servicio segun lo que se envia
$server->service($peticion);
