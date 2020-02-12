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
            a.boton{
                border-style: solid;
                border-width : 1px 1px 1px 1px;
                -webkit-appearance: button;
                -moz-appearance: button;
                appearance: button;
                text-decoration : none;
                padding : 4px;
                border-color : #000000;
                background-color: red;
            }
            .tablero{
                display: grid;
                grid-template-columns: repeat(11, 30px);
            }

            .agua{
                background-color: yellow;
            }
            .destructor{
                background-color: darkblue; 
            } 
            .submarino{
                background-color: darkgray;  
            }
            .crucero{
                background-color: darkmagenta;  
            } 
            .acorazado{
                background-color: darkgreen;
            } 
            .portaaviones{
                background-color: darkred;                
            }
            
            
        </style>
    </head>
    <body>
        <?php
        session_start();
        error_reporting(E_ALL);
        ini_set('display_errors', '1');
        require_once './Posicion.php';
        require_once './Barco.php';
        require_once './Tablero.php';
        if (!isset($_SESSION["fin"])) {
            $_SESSION["fin"] = true;
        }

        $tablero = null;
        if (isset($_SESSION["Tablero"]) && $_SESSION["Tablero"] != null) {
            $tablero = unserialize($_SESSION["Tablero"]);
        }
        if ($tablero == null) {
            $tablero = new Tablero();
            $_SESSION["Tablero"] = serialize($tablero);
        }

        $tablero->inicia();
        $_SESSION["Tablero"] = serialize($tablero);
        // $_SESSION["Tablero"] = null;
        // print_r(unserialize($_SESSION["Tablero"]));
        ?>
    </body>
</html>
