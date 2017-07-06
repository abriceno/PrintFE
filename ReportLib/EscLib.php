<?php

class EscLib {

    public function __construct() {

        $load_formats = array(
            'ESC' => chr(27),
            'GS' => chr(29),
            'InitPrinter' => chr(27) . chr(64),
            'IntlChrSet' => array(
                'USA' => chr(27) . chr(82) . "0",
                'France' => chr(27) . chr(82) . "1",
                'Germany' => chr(27) . chr(82) . "2",
                'UK' => chr(27) . chr(82) . "3",
                'DenmarkI' => chr(27) . chr(82) . "4",
                'Sweden' => chr(27) . chr(82) . "5",
                'Italy' => chr(27) . chr(82) . "6",
                'SpainI' => chr(27) . chr(82) . "7",
                'JapanEng' => chr(27) . chr(82) . "8",
                'Norway' => chr(27) . chr(82) . "9",
                'DenmarkII' => chr(27) . chr(82) . "10",
                'SpainII' => chr(27) . chr(82) . "11",
                'LatAmerica' => chr(27) . chr(82) . "12",
                'Korea' => chr(27) . chr(82) . "13",
                'Legal' => chr(27) . chr(82) . "64"
            ),
            'formato' => array(
                'BoldOff' => chr(27) . chr(70),
                'BoldOn' => chr(27) . chr(69),
                'DoubleOn' => chr(29) . chr(14) . chr(17),
                'DoubleOff' => chr(29) . chr(14) . chr(0),
                'ItalicOn' => chr(27) . chr(52),
                'ItalicOff' => chr(27) . chr(53),
                'DoubleStOn' => chr(27) . chr(71),
                'DoubleStOff' => chr(27) . chr(72),
                'UnderlineOn' => chr(27) . chr(45) . chr(49),
                'UnderlineOff' => chr(27) . chr(45) . chr(48)
            ),
            'FormFeed' => chr(12),
            'LineFeed' => chr(10),
            'CarriageReturn' => chr(13),
            'BackSpace' => chr(8),
            'EightForInch' => chr(27) . chr(48),
            'SixForInch' => chr(27) . chr(50),
            'cut' => chr(27) . chr(109)
        );

        foreach ($load_formats as $formatName => $formatValue) {
            $this->$formatName = $formatValue;
        }
    }

    public function normal(&$componente, $fila, $columna, $longitud) {
        $componente[$fila][$columna] = $this->formato['BoldOff'] . $componente[$fila][$columna];
        $componente[$fila][$columna + $longitud - 1] = $componente[$fila][$columna + $longitud - 1] . $this->formato['BoldOff'];
    }

    public function negrita(&$componente, $fila, $columna, $longitud) {
        $componente[$fila][$columna] = $this->formato['BoldOn'] . $componente[$fila][$columna];
        $componente[$fila][$columna + $longitud - 1] = $componente[$fila][$columna + $longitud - 1] . $this->formato['BoldOff'];
    }

    public function italica(&$componente, $fila, $columna, $longitud) {
        $componente[$fila][$columna] = $this->formato['ItalicOn'] . $componente[$fila][$columna];
        $componente[$fila][$columna + $longitud - 1] = $componente[$fila][$columna + $longitud - 1] . $this->formato['ItalicOff'];
    }

    public function doble(&$componente, $fila, $columna, $longitud) {
        $componente[$fila][$columna] = $this->formato['DoubleOn'] . $componente[$fila][$columna];
        $componente[$fila][$columna + $longitud - 1] = $componente[$fila][$columna + $longitud - 1] . $this->formato['DoubleOff'];
    }

    public function doblegolpe(&$componente, $fila, $columna, $longitud) {
        $componente[$fila][$columna] = $this->formato['DoubleStOn'] . $componente[$fila][$columna];
        $componente[$fila][$columna + $longitud - 1] = $componente[$fila][$columna + $longitud - 1] . $this->formato['DoubleStOff'];
    }

    public function subrayado(&$componente, $fila, $columna, $longitud) {
        $componente[$fila][$columna] = $this->formato['UnderlineOn'] . $componente[$fila][$columna];
        $componente[$fila][$columna + $longitud - 1] = $componente[$fila][$columna + $longitud - 1] . $this->formato['UnderlineOff'];
    }

}