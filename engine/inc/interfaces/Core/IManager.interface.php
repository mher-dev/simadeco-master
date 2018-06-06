<?php
namespace Core
{
    //------- CONTROL DE ACCESO -------//
    require_once dirname(dirname(dirname(__FILE__))).'/AccessControl.php';
    AccessControl(__FILE__);
    //--- FIN DEL CONTROL DE ACCESO ---//

    interface IManager extends \Serializable
    {
        /**
         * Manejador de variables globales
         */
        public function __construct();

        /**
         * Trabaja con variables globales, que estan fuera de la clave de sistema
         * @param string $name Nombre de la variable a tratar
         * @param mixed $value El valor a asignar (Segъn ello: get|set)
         * @return mixed Valor de almacenado
         */
        public function overall ($name, $value = '__FALSE__');

        /**
         * Devuelve el valor <b>LOCAL</b> requerido dentro de la clave de sistema
         * @param string $name Valor a recoger
         * @return null|mixed
         */
        public function get($name);


        /**
         * Asignaciуn de valores <b>LOCALES</b> dentro de la clave de sistema.
         * @param string $name Nombre del valor
         * @param mixed $value Valor a asignar
         * @return null|mixed Valor asignado
         */
        public function set($name, $value);

        /**
         * Asignaciуn de valores <b>TEMPORALES</b> dentro de la clave de sistema.
         * @param string $name Nombre del valor
         * @param mixed $value Valor a asignar
         * @return null|mixed Valor asignado
         */
        public function setTemporal($name, $value);

        /**
         * Devuelve el valor  <b>TEMPORAL</b> requerido dentro de la clave de sistema
         * @param string $name Valor a recoger
         * @return null|mixed
         */

        public function getTemporal($name);

        /**
         * Se destruyen los valores temporales
         */
        public function __destruct();

        /**
         * Devuelve el valor <b>LOCAL</b> requerido dentro de la clave de sistema
         * @param string $name Valor a recoger
         * @return null|mixed
         */
        public function __get($name);

            /**
         * Asignaciуn de valores <b>LOCALES</b> dentro de la clave de sistema.
         * @param string $name Nombre del valor
         * @param mixed $value Valor a asignar
         */
        public function __set($name, $value);

       /**
         * Intenta encontrar el valor <b>LOCAL</b> indicado, y sino le asigna el valor $value
         * y lo devuelve
         * @param string $name
         * @param mixed $value
         */
        public function TryGet($name, $value);



    }

    
}