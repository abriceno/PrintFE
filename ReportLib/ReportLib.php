<?php

/*
 * Clase ReportLib: Llama a la creación de reportes en PHP,
 * para impresoras matriciales que usen ESC/P.
 *
 */

require_once 'autoload.php';

class ReportLib extends MainObject {

    public $nameReport;
    public $pagenum = 0;
    public $datetimenow;
    public $datenow;
    public $totalpages = 0;
    public $timestamp;
    public $inVariables = array();
    public $formatSource = array();
    public $validacion = true;
    public $resultados_validacion = array();
    public $sourceLocation;
    public $Informacion = array();
    public $EscLib;
    public $reporte = array();
    public $DataObjects = array();
    public $endReport = false;

    public function __construct() {
        $this->timestamp = time();
        $this->datetimenow = date('Y-m-d H:i:s', $this->timestamp);
        $this->datenow = date('Y-m-d', $this->timestamp);
        $this->EscLib = new EscLib();
    }

    public function CargarFormato() {
        if (isset($this->formatSource["tipo"]) and isset($this->formatSource["sourcelocation"])) {
            switch ($this->formatSource["tipo"]) {
                case "file":
                    $this->CargarFormatoDesdeArchivo();
                    $this->formVariables();
                    break;
                case "database":
                    $this->CargarFormatoDesdeDB();
                    break;
            }
        } else {
            echo "Error: No está establecido el origen del formato\n";
            exit;
        }
        // Evaluamos los parametros de sourceLocation, validamos
        if ($this->sourceLocation) {
            $this->validacion = true;
            $this->resultados_validacion = array();
            if (isset($this->sourceLocation->nameReport)) {
                $this->nameReport = $this->sourceLocation->nameReport;
            } else {
                $this->validacion = false;
                $this->resultados_validacion["nameReport"] = "nameReport no declarado!";
            }
            $this->ValidarParametrosPagina();
            $this->ValidarComponentesPagina();
            $this->ValidarInformacionPagina();
            $this->ImprimirResultadosValidacion();
        }
    }

    public function ImprimirResultadosValidacion() {
        if (!$this->validacion) {
            foreach ($this->resultados_validacion as $key => $resultado) {
                echo $resultado . "\n";
            }
            exit;
        }
    }

    public function ValidarParametrosPagina() {
        // Validamos parámetros de Página
        $parametros = array("LongitudPagina", "MargenSuperior", "MargenInferior", "SangriaDeImpresora", "ColumnaMargenDerecho");

        foreach ($parametros as $parametro) {
            if (isset($this->sourceLocation->$parametro)) {
                
            } else {
                $this->validacion = false;
                $this->resultados_validacion[$parametro] = $parametro . " no declarado!";
            }
        }
    }

    public function ValidarComponentesPagina() {
        $componentes = array("EncabezadoPagina", "DetallePagina", "PiePagina", "TituloPagina", "ResumenPagina");

        foreach ($componentes as $componente) {
            if (isset($this->sourceLocation->$componente)) {

                $longitud_componente = "Longitud" . $componente;
                if (isset($this->sourceLocation->$componente->$longitud_componente)) {
                    
                } else {
                    $this->validacion = false;
                    $this->resultados_validacion[$longitud_componente] = $longitud_componente . " no declarado!";
                }

                if (isset($this->sourceLocation->$componente->Estructura)) {
                    
                } else {
                    $this->validacion = false;
                    $this->resultados_validacion[$componente . ".Estructura"] = $componente . ".Estructura no declarado!";
                }
            } else {
                $this->validacion = false;
                $this->resultados_validacion[$componente] = $componente . " no declarado!";
            }
        }
    }

    public function ValidarInformacionPagina() {
        if (isset($this->sourceLocation->Informacion)) {
            
        } else {
            $this->validacion = false;
            $this->resultados_validacion["Informacion"] = "Informacion no declarado!";
        }
    }

    public function ExecSQLQuery($link, $SQLString, $dbms) {
        switch ($dbms) {
            case "mysqli":
                return mysqli_query($link, $SQLString);
                break;
            case "pgsql":
                return pg_query($link, $SQLString);
                break;
            case "mssql":
                return mssql_query($SQLString, $link);
                break;
        }
    }

    public function FetchSQLObject(&$result, $dbms) {
        switch ($dbms) {
            case "mysqli":
                return mysqli_fetch_object($result);
                break;
            case "pgsql":
                return pg_fetch_object($result);
                break;
            case "mssql":
                return mssql_fetch_object($result);
                break;
        }
    }

    public function LoadData($link) {
        foreach ($this->sourceLocation->Informacion as $objeto => $componentes) {
            $this->ValidarInformacion($componentes);
            $this->ImprimirResultadosValidacion();
            $this->Informacion[$objeto] = $this->ExecSQLQuery($link, $componentes->Sentencia, $componentes->dbms);
        }
    }

    public function ValidarInformacion($componentes) {
        $esperados = array('tipo', 'Origen', 'Sentencia', 'dbms');
        foreach ($componentes as $key => $componente) {
            if (!in_array($key, $esperados)) {
                $this->validacion = false;
                $this->resultados_validacion["Informacion." . $key] = "Informacion.Lista.$key no declarado!";
            } else {
                if (empty($componente)) {
                    $this->validacion = false;
                    $this->resultados_validacion["Informacion." . $key . ".value"] = "Informacion.Lista.$key.value no contiene valor!";
                }
            }
        }
    }

    public function MapData() {
        if ($this->validacion) {
            
        } else {
            $this->ImprimirResultadosValidacion();
        }
    }

    public function EscribirEncabezado(&$reportePagina) {
        foreach ($this->sourceLocation->EncabezadoPagina->Estructura as $objeto => $design) {
            if (isset($design->tipo)) {
                switch ($design->tipo) {
                    case "texto":
                        if (isset($design->repeate2end)) {
                            if ($design->repeate2end == "yes") {
                                $reportePagina->EncabezadoPagina->EscribirContenido($design->fila, $design->columna, $design->longitud, $reportePagina->EncabezadoPagina->repeate2end($design->longitud, $design->contenido));
                            }
                        } else {
                            $reportePagina->EncabezadoPagina->EscribirContenido($design->fila, $design->columna, $design->longitud, $design->contenido);
                        }
                        break;
                    case "datenow":
                        $reportePagina->EncabezadoPagina->EscribirContenido($design->fila, $design->columna, $design->longitud, $this->datenow);
                        break;
                    case "pagenum":
                        $reportePagina->EncabezadoPagina->EscribirContenido($design->fila, $design->columna, $design->longitud, $this->pagenum);
                        break;
                    case "data":
                        break;
                }
            } else {
                
            }

            if (isset($design->formato)) {
                $this->formatearContenido($design, $reportePagina->EncabezadoPagina->Contenido);
            }
        }
    }

    public function formatearContenido(&$design, &$contenido) {
        if (isset($design->formato)) {
            foreach ($design->formato as $formato) {
                $this->EscLib->$formato($contenido, $design->fila, $design->columna, $design->longitud);
            }
        }
    }

    public function TablasInformacionDesdeDetallePagina(&$tablas) {
        foreach ($this->sourceLocation->DetallePagina->Estructura as $tabla => $design) {
            if (isset(explode(".", $tabla)[0])) {
                if (!in_array(explode(".", $tabla)[0], $tablas)) {
                    array_push($tablas, explode(".", $tabla)[0]);
                }
            }
        }
    }

    public function EstructuraDataContenido(&$arreglo, $contenido) {
        list($arreglo["tabla"], $arreglo["campo"]) = explode(".", $contenido);
    }

    public function EscribirDetalle(&$reportePagina) {
        $tablas = array();
        $this->TablasInformacionDesdeDetallePagina($tablas);

        for ($fila = 0; $fila < $this->sourceLocation->DetallePagina->LongitudDetallePagina; $fila++) {
            foreach ($tablas as $tabla) {
                if ($this->DataObjects[$tabla] = $this->FetchSQLObject($this->Informacion[$tabla], $this->sourceLocation->Informacion->$tabla->dbms)) {
                    // Bien seguimos
                } else {
                    // Salimos 
                    $this->endReport = true;
                }
            }
            if ($this->endReport) {
                break;
            }
            foreach ($this->sourceLocation->DetallePagina->Estructura as $objeto => $design) {

                if (isset($design->tipo)) {
                    switch ($design->tipo) {
                        case "texto":
                            if (isset($design->repeate2end)) {
                                if ($design->repeate2end == "yes") {
                                    $reportePagina->DetallePagina->EscribirContenido($fila, $design->columna, $design->longitud, $reportePagina->DetallePagina->repeate2end($design->longitud, $design->contenido));
                                }
                            } else {
                                $reportePagina->DetallePagina->EscribirContenido($fila, $design->columna, $design->longitud, $design->contenido);
                            }
                            break;
                        case "datenow":
                            $reportePagina->DetallePagina->EscribirContenido($fila, $design->columna, $design->longitud, $this->datenow);
                            break;
                        case "pagenum":
                            $reportePagina->DetallePagina->EscribirContenido($fila, $design->columna, $design->longitud, $this->pagenum);
                            break;
                        case "data":
                            $contenido = array();
                            $this->EstructuraDataContenido($contenido, $design->contenido);
                            $campo = $contenido["campo"];
                            $reportePagina->DetallePagina->EscribirContenido($fila, $design->columna, $design->longitud, $this->DataObjects[$contenido["tabla"]]->$campo);
                            break;
                    }
                } else {
                    
                }
                if (isset($design->formato)) {
                    $design->fila = $fila;
                    $this->formatearContenido($design, $reportePagina->DetallePagina->Contenido);
                }
            }
        }
    }

    public function EscribirPie(&$reportePagina) {
        foreach ($this->sourceLocation->PiePagina->Estructura as $objeto => $design) {
            if (isset($design->tipo)) {
                switch ($design->tipo) {
                    case "texto":
                        if (isset($design->repeate2end)) {
                            if ($design->repeate2end == "yes") {
                                $reportePagina->PiePagina->EscribirContenido($design->fila, $design->columna, $design->longitud, $reportePagina->PiePagina->repeate2end($design->longitud, $design->contenido));
                            }
                        } else {
                            $reportePagina->PiePagina->EscribirContenido($design->fila, $design->columna, $design->longitud, $design->contenido);
                        }
                        break;
                    case "datenow":
                        $reportePagina->PiePagina->EscribirContenido($design->fila, $design->columna, $design->longitud, $this->datenow);
                        break;
                    case "pagenum":
                        $reportePagina->PiePagina->EscribirContenido($design->fila, $design->columna, $design->longitud, $this->pagenum);
                        break;
                    case "data":
                        break;
                }
            } else {
                
            }

            if (isset($design->formato)) {
                $this->formatearContenido($design, $reportePagina->PiePagina->Contenido);
            }
        }
    }

    public function EscribirTitulo(&$reportePagina) {
        foreach ($this->sourceLocation->TituloPagina->Estructura as $objeto => $design) {
            if (isset($design->tipo)) {
                switch ($design->tipo) {
                    case "texto":
                        if (isset($design->repeate2end)) {
                            if ($design->repeate2end == "yes") {
                                $reportePagina->TituloPagina->EscribirContenido($design->fila, $design->columna, $design->longitud, $reportePagina->TituloPagina->repeate2end($design->longitud, $design->contenido));
                            }
                        } else {
                            $reportePagina->TituloPagina->EscribirContenido($design->fila, $design->columna, $design->longitud, $design->contenido);
                        }
                        break;
                    case "datenow":
                        $reportePagina->TituloPagina->EscribirContenido($design->fila, $design->columna, $design->longitud, $this->datenow);
                        break;
                    case "pagenum":
                        $reportePagina->TituloPagina->EscribirContenido($design->fila, $design->columna, $design->longitud, $this->pagenum);
                        break;
                    case "data":
                        break;
                }
            } else {
                
            }
            if (isset($design->formato)) {
                $this->formatearContenido($design, $reportePagina->TituloPagina->Contenido);
            }
        }
    }

    public function EscribirResumen(&$reportePagina) {
        foreach ($this->sourceLocation->ResumenPagina->Estructura as $objeto => $design) {
            if (isset($design->tipo)) {
                switch ($design->tipo) {
                    case "texto":
                        if (isset($design->repeate2end)) {
                            if ($design->repeate2end == "yes") {
                                $reportePagina->ResumenPagina->EscribirContenido($design->fila, $design->columna, $design->longitud, $reportePagina->ResumenPagina->repeate2end($design->longitud, $design->contenido));
                            }
                        } else {
                            $reportePagina->ResumenPagina->EscribirContenido($design->fila, $design->columna, $design->longitud, $design->contenido);
                        }
                        break;
                    case "datenow":
                        $reportePagina->ResumenPagina->EscribirContenido($design->fila, $design->columna, $design->longitud, $this->datenow);
                        break;
                    case "pagenum":
                        $reportePagina->ResumenPagina->EscribirContenido($design->fila, $design->columna, $design->longitud, $this->pagenum);
                        break;
                    case "data":
                        break;
                }
            } else {
                
            }
            if (isset($design->formato)) {
                $this->formatearContenido($design, $reportePagina->ResumenPagina->Contenido);
            }
        }
    }

    public function ToPrint() {


        $propiedades_pagina = array(
            "MargenSuperior" => $this->sourceLocation->MargenSuperior,
            "MargenInferior" => $this->sourceLocation->MargenInferior,
            "SangriaDeImpresora" => $this->sourceLocation->SangriaDeImpresora,
            "LongitudPagina" => $this->sourceLocation->LongitudPagina,
            "ColumnaMargenDerecho" => $this->sourceLocation->ColumnaMargenDerecho,
            "LongitudEncabezadoPagina" => $this->sourceLocation->EncabezadoPagina->LongitudEncabezadoPagina,
            "LongitudDetallePagina" => $this->sourceLocation->DetallePagina->LongitudDetallePagina,
            "LongitudPiePagina" => $this->sourceLocation->PiePagina->LongitudPiePagina,
            "LongitudTituloPagina" => $this->sourceLocation->TituloPagina->LongitudTituloPagina,
            "LongitudResumenPagina" => $this->sourceLocation->ResumenPagina->LongitudResumenPagina
        );
            
        while (true) {
            // Establecemos el numero de página actual
            $this->pagenum += 1;
            // Creamos la página
            $this->reporte[$this->pagenum] = new Pagina($propiedades_pagina);
            // Llenamos contenido de Cabecera
            $this->EscribirTitulo($this->reporte[$this->pagenum]);
            $this->EscribirEncabezado($this->reporte[$this->pagenum]);
            $this->EscribirDetalle($this->reporte[$this->pagenum]);
            $this->EscribirPie($this->reporte[$this->pagenum]);
            $this->EscribirResumen($this->reporte[$this->pagenum]);
            if ($this->endReport) {
                break;
            }
        }
        
        
        $this->InicializaImpresion();
        for ($pagina = 1; $pagina <= $this->pagenum; $pagina++) {
            $this->reporte[$pagina]->ImprimirPagina();
            echo $this->EscLib->FormFeed."\n";
        }
        
    }

    public function InicializaImpresion() {
        echo $this->EscLib->InitPrinter;
    }
    
    
    
    public function CargarFormatoDesdeArchivo() {
        $this->sourceLocation = json_decode(fread(
                        fopen($this->formatSource["sourcelocation"], 'r'), filesize($this->formatSource["sourcelocation"])
                )
        );
    }

    public function CargarFormatoDesdeDB() {
        
    }

    public function Utf8ToCP850($string) {
        return iconv("UTF-8", "CP850", $string);
    }

}


