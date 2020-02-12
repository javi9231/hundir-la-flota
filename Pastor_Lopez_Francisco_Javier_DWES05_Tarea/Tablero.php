<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Tablero
 *
 * @author jpastor
 */
class Tablero {

    private $barcos = array();
    private $tabla = array();
    private $caracteres;

    function __construct() {
        $this->caracteres = str_split('ABCDEFGHIJ');
    }

    private function crearBarco($nombre) {
        $correcto = false;
        $barco = new Barco($nombre);
        while (!$correcto) {
            $posicion = $barco->posiciona();
            if (empty($this->barcos)) {
                array_push($this->barcos, $barco);
                return;
            }
            $tamanio = $barco->getTiposDeBarcos()[$nombre];
            $colision = false;
            if ($posicion->getOrientacion() == 0) {
                $maxColumna = $posicion->getColumna() + $tamanio;
                for ($i = $posicion->getColumna(); $i < $maxColumna; $i++) {
                    foreach ($this->barcos as $b) {
                        if ($b->verifica($posicion->getFila(), $i)) {
                            $colision = true;
                            break;
                        }
                    }
                    if ($colision) {
                        break;
                    }
                }
            } else {
                $columna = $posicion->getColumna();
                $valorFila = array_search($posicion->getFila(), $this->caracteres);
                $maxFila = $valorFila + $tamanio;
                for ($i = $valorFila; $i < $maxFila; $i++) {
                    foreach ($this->barcos as $b) {
                        if ($b->verifica($this->caracteres[$i], $columna)) {
                            $colision = true;
                            break;
                        }
                    }
                    if ($colision) {
                        break;
                    }
                }
            }
            if (!$colision) {
                $this->barcos[] = $barco;
                $correcto = true;
            }
        }
    }

    private function pintaNumeros() {
        $resultado = "";
        for ($i = 0; $i < 10; $i++) {
            $resultado .= '<div class="numero">' . ($i + 1) . '</div>';
        }
        return $resultado;
    }

    private function pintarCasillas($fila) {
        $resultado = "";
        $clase = "bcasilla";
        for ($i = 1; $i < 11; $i++) {
            $clase = $this->tabla[$fila][$i];
            if ($clase != "agua" && $clase != "bcasilla") {

                $resultado .= '<a href="" class="' . $clase . '"></a>';
            } else {
                $resultado .= '<a href="index.php?fila=' . $fila . '&columna=' . $i . '" class="' . $clase . '"></a>';
            }
        }
        return $resultado;
    }

    private function compruebaDisparo($fila, $columna) {
        foreach ($this->barcos as $barco) {
            if ($barco->disparo($fila, $columna)) {
                if ($barco->hundido()) {
                    if($this->fin()){
                        return "FIN";
                    }
                    return ' ' . $barco->getNombre() . ' Hundido';
                }
                return ' ' . $barco->getNombre() . ' Tocado';
            }
        }
        return " agua";
    }

    public function disparo($fila, $columna) {
        $this->tabla[$fila][$columna] = $this->compruebaDisparo($fila, $columna);
        return $this->tabla[$fila][$columna];
    }

    private function pintarTablero() {
        if (is_null($this->tabla) || empty($this->tabla)) {
            foreach ($this->caracteres as $letra) {
                $this->tabla[$letra] = array_fill(1, 10, "bcasilla");
            }
        }
        $html = '<div class="tablero">';
        $html .= '<div></div>';
        $html .= $this->pintaNumeros();

        foreach ($this->caracteres as $fila) {
            $html .= '<div>' . $fila . '</div>';
            $html .= $this->pintarCasillas($fila);
        }
        $html .= '<div></div>';
        $html .= '</div>';
        echo $html;
    }

    public function inicia() {
        if (empty($this->barcos)) {
            $this->crearBarco("destructor");
            $this->crearBarco("submarino");
            $this->crearBarco("crucero");
            $this->crearBarco("acorazado");
            $this->crearBarco("portaaviones");
        }
        $disparo = null;
        if (isset($_GET["fila"]) && isset($_GET["columna"]) && !is_null($this->tabla)) {
            $disparo = $this->disparo($_GET["fila"], $_GET["columna"]);
        }
        echo '<div class="contenedor">';
        $this->pintarTablero();
        if (!is_null($disparo)) {
            echo '<br><div class="resultado">' . $disparo . '</div>';
        }
        echo '</div>';
    }
    
    private function fin(){
        if(isset($this->barcos)){
            foreach ($this->barcos as $barco) {
                if(!$barco->hundido()){
                    return false;
                }
                //var_dump($barco);
            }
            return true;
        }
    }

}
