<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Posicion
 *
 * @author jpastor
 */
class Posicion {

    private $fila;
    private $columna;
    private $orientacion;

    function __construct($fila, $columna, $orientacion) {
        $this->fila = $fila;
        $this->columna = $columna;
        $this->orientacion = $orientacion;
    }

    function getFila() {
        return $this->fila;
    }

    function getColumna() {
        return $this->columna;
    }

    function getOrientacion() {
        return $this->orientacion;
    }

    function setFila($fila) {
        $this->fila = $fila;
    }

    function setColumna($columna) {
        $this->columna = $columna;
    }

    function setOrientacion($orientacion) {
        $this->orientacion = $orientacion;
    }

}
