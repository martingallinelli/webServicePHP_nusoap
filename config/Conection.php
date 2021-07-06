<?php

class Conection 
{
    //* DATOS
    private static $dsn;   // mysql
    private static $host;  // localhost
    private static $db;    // blog 
    private static $user;  // root
    private static $pass;  // ""

    // constructor
    public function __construct() {}

    //! abrir conexion a la BD    
    /**
     * conectar
     *
     * @return object
     */
    static function conectar() 
    {
        // guardar los datos de la conexion
        $listaDatos = self::datosConexion();
        // recorrer la lista con los datos de la conexion y guardarlos
        foreach ($listaDatos as $key => $value) {
            self::$dsn = $value['dsn'];
            self::$host = $value['host'];
            self::$db = $value['database'];
            self::$user = $value['user'];
            self::$pass = $value['password'];
        }

        try {
            //! conectar a la BD
            // 'mysql:host=localhost;dbname=blog','root',''
            $conn = new PDO(
                self::$dsn . ":host=" . self::$host . ";dbname=" . self::$db,
                self::$user, 
                self::$pass
            );

            //* control de errores
            // muestra los errores comunes y las advertencias
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //* devolver la conexion
            return $conn;
        // capturar error  
        } catch (PDOException $exception) {
            exit($exception->getMessage());
        }
    }

    //! obtener datos de la conexion
    // private: solo se puede ejecutar en Conection.php
    private static function datosConexion()
    {
        // dirname(): guardar el directorio de este archivo (Conection.php -> config)
        $direccion = dirname(__FILE__);
        /**
         * file_get_contents: convertir a string el contenido de un archivo
         * $direccion = config
         */
        $jsondata = file_get_contents($direccion . "/" . "config");
        // devolver un array con los datos de conexion
        return json_decode($jsondata, true);
    }
}