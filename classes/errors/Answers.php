<?php 

class Answers
{
    /**
     * mensaje
     *
     * @param  mixed $codigo
     * @param  mixed $mensaje
     * @return array
     */
    public static function mensaje($codigo, $mensaje)
    {
        $response['success'] = $codigo;
        $response['result'] = $mensaje;
        return $response;
    }
}