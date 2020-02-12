<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Barco
 *
 * @author jpastor
 */
class Barco {

    private $TiposDeBarcos;
    private $posicion;
    private $tamanio;
    private $impactos;
    private $nombre;
    private $caracteres;

    function getNombre() {
        return $this->nombre;
    }

    function __construct($nombre) {
        $this->nombre = $nombre;
        $this->caracteres = str_split('ABCDEFGHIJ');
        $this->TiposDeBarcos = [
            "destructor" => 2,
            "submarino" => 3,
            "crucero" => 3,
            "acorazado" => 4,
            "portaaviones" => 5
        ];
    }

    /** Posiciona el barco
     * @return Posicion
     */
    public function posiciona() {
        $this->tamanio = $this->TiposDeBarcos[$this->nombre];
        $this->impactos = array_fill(0, $this->tamanio, false);
        $orientacion = rand(0, 1);
        // si la orientacion es 0 se toma como horizontal
        if ($orientacion == 0) {
            $fila = $this->caracteres[rand(0, 9)];
            // para evitar que el barco salga del tablero se resta su 
            // tamaño 
            $columna = rand(1, 10 - $this->tamanio);
        } else {
            // lo mismo pero en vertical
            $fila = $this->caracteres[rand(0, 9 - $this->tamanio)];
            $columna = rand(1, 10);
        }

        $this->posicion = new Posicion($fila, $columna, $orientacion);
        return $this->posicion;
    }

    private function verificaColumna($columna) {
        $barco_columna = $this->posicion->getColumna();
        $longitud = count($this->impactos);
        return ($columna == $barco_columna || $columna > $barco_columna && $columna < ($barco_columna + $longitud));
    }

    private function verificaFila($fila) {
        $barco = $this->posicion->getFila();
        $longitud = count($this->impactos);
        $inicio = array_search($barco, $this->caracteres);
        $valorFila = array_search($fila, $this->caracteres);
        return ($inicio == $valorFila || $valorFila > $inicio && $valorFila < ($inicio + $longitud));
    }

    public function verifica($fila, $columna) {
        $posicion = $this->posicion;
        if ($this->posicion->getOrientacion() == 0) {
            $principal = $this->posicion->getFila();
            if ($fila == $principal && $this->verificaColumna($columna)) {
                return true;
            }
        } else {
            $principal = $this->posicion->getColumna();
            if ($columna == $principal && $this->verificaFila($fila)) {
                return true;
            }
        }
        return false;
    }

    public function disparo($fila, $columna) {
        if ($this->verifica($fila, $columna)) {
            $noImpacto = array_search(false, $this->impactos);
            if ($noImpacto >= 0) {
                $this->impactos[$noImpacto] = true;
                return true;
            }
        }
        return false;
    }

    public function hundido() {
        // si ahi un valor a true, tiene al menos un impacto
        if (in_array(true, $this->impactos)) {
            $contador = 0;
            foreach ($this->impactos as $tocado) {
                if ($tocado) {
                    $contador++;
                }
            }
            // si contamos los impacto iguales y coninciden con el tamaño de impactos
            if ($contador == count($this->impactos)) {
                return true;
            }
        }
        return false;
    }

    function getTiposDeBarcos() {
        return $this->TiposDeBarcos;
    }

    function getPosicion() {
        return $this->posicion;
    }

    function getTamanio() {
        return $this->tamanio;
    }

    function getImpactos() {
        return $this->impactos;
    }

}
