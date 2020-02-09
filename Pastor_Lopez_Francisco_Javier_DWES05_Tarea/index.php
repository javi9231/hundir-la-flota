<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Hundir la flota</title>
        <style type="text/css">
            *{
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: 'Poppins', sans-serif;
            }
            body{
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
            }


            a {
                -webkit-appearance: button;
                -moz-appearance: button;
                appearance: button;

                text-decoration:none;
                color:white;
                background-color: aquamarine;
                border-style: solid;
            }
            .tablero{
                display: grid;
                grid-template-columns: repeat(11, 30px);
            }

            .agua{
                background-color: yellow;
            }
            .tocado{
                background-color: red;
            }

        </style>
    </head>
    <body>
        <?php
        session_start();
        error_reporting(E_ALL);
        ini_set('display_errors', '1');

        if (!isset($_SESSION["fin"])) {
            $_SESSION["fin"] = true;
        }

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
                $barco = $this->posicion->getColumna();
                $longitud = count($this->impactos);
                return ($columna === $barco || $columna > $barco && $columna < ($barco + $longitud));
            }

            private function verificaFila($fila) {
                $barco = $this->posicion->getFila();
                $longitud = count($this->impactos);
                $inicio = array_search($barco, $this->caracteres);
                $valorFila = array_search($fila, $this->caracteres);
                return ($inicio === $valorFila || $valorFila > $inicio && $valorFila < ($inicio + $longitud));
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
                    if ($noImpacto) {
                        $this->impactos[$noImpacto] = true;
                        return true;
                    }
                }
                return false;
            }

            public function hundido() {
                // si ahi un valor a true, tiene al menos un impacto
                if (in_array(true, $this->impactos)) {
                    // si contamos los impacto iguales y coninciden con el tamaño de impactos
                    if (count(array_unique($this->impactos)) === count($this->impactos)) {
                        // Devolvemos que esta hundido
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
                var_dump($this->$barcos);
                // print_r($this->barcos);
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
                //$clase = "bcasilla";
                for ($i = 1; $i < 11; $i++) {
                    $clase = $this->tabla[$fila][$i];
                    if ($clase != "agua" && $clase != "bcasilla") {
                        $clase = "tocado";
                    }
                    $resultado .= '<a href="index.php?fila=' . $fila . '&columna=' . $i . '" class="' . $clase . '"></a>';
//                    $resultado .= '<div ></div></a>';
                }
                return $resultado;
            }

            private function compruebaDisparo($fila, $columna) {
                foreach ($this->barcos as $barco) {
                    $respuesta = $barco->verifica($fila, $columna);
                    if ($respuesta) {
                        $barco->disparo($fila, $columna);
                        return $barco->getNombre();
                    }
                }
                return "agua";
            }

            public function disparo($fila, $columna) {
                $this->tabla[$fila][$columna] = $this->compruebaDisparo($fila, $columna);
                var_dump($this->barcos[0]->getPosicion()->getOrientacion());
                echo "kk_ ".$this->tabla["I"][9];
            }

            private function pintarTablero() {
                if (empty($this->tabla)) {
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
                if (isset($_GET["fila"]) && isset($_GET["columna"])) {
                    $this->disparo($_GET["fila"], $_GET["columna"]);
                }
                $this->pintarTablero();
            }

        }

        $tablero = null;
        if (isset($_SESSION["Tablero"])) {
            $tablero = unserialize($_SESSION["Tablero"]);
        } else {
            $tablero = new Tablero();
        }
        $tablero->inicia();
        $_SESSION["Tablero"] = serialize($tablero);
        // print_r(unserialize($_SESSION["Tablero"]));
        ?>
    </body>
</html>
