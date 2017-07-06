<?php

class FormatoBloque extends MainObject {
    public $LongitudFilas=0;
    public $LongitudColumnas=0;
    public $Contenido = array();
    public $FilaActual=0;
    public $ColumnaActual=0;
    public $inVariables = array();
    
    public function __construct($LongitudFilas=0,$LongitudColumnas=0) {
        $this->LongitudFilas=$LongitudFilas;
        $this->LongitudColumnas=$LongitudColumnas;
        $this->FormatearCeldas($LongitudFilas, $LongitudColumnas);
    }
    
    public function FormatearCeldas ($LongitudFilas,$LongitudColumnas) {
        for ($fila=0;$fila<$LongitudFilas;$fila++) {
            for ($columna=0;$columna<$LongitudColumnas;$columna++){
                $this->Contenido[$fila][$columna]=" ";
            }
            
        }
    }
    
    public function EscribirCelda($fila,$columna,$dato) {
        $this->Contenido[$fila][$columna]=$dato;
    }
    
    public function EscribirContenido($fila,$columna,$longitud,$contenido) {
        for ($poscol=0;$poscol<$longitud;$poscol++){
            $dato = substr(utf8_decode($contenido), $poscol, 1);
            if ($dato===false or $dato==="") {
                $dato = chr(32);
            }
            $this->EscribirCelda($fila,$columna+$poscol, $dato);
        }
    }
    
    public function EscribirCeldaFormato($fila,$columna,$formato_json) {
        
    }
    
    public function RandomChar() {
        $min = 33;
        $max = 126;
        return chr(rand($min,$max));
        
    }
    
    public function RellenarAleatorio() {
        for ($fila=0;$fila<$this->LongitudFilas;$fila++) {
            for ($columna=0;$columna<$this->LongitudColumnas;$columna++) {
                $this->EscribirCelda($fila, $columna, $this->RandomChar());
            }
        }
    }
    
    public function ImprimirBloque() {
        for ($fila=0;$fila<$this->LongitudFilas;$fila++) {

            for ($columna=0;$columna<$this->LongitudColumnas;$columna++) {
                echo $this->Contenido[$fila][$columna];
            }
            echo "\n";
        }
    }
    

    public function repeate2end($longitud,$contenido) {
        $dato = "";
        for ($i=0;$i<$longitud;$i++) {
            $dato .= $contenido; 
        }
        
        $dato = substr($dato, 0, $longitud);
        return $dato;
    }
    
  
}