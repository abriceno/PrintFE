<?php

Class Pagina extends MainObject {

    public $LongitudPagina = 66; // Filas
    public $MargenSuperior = 0; // Filas
    public $MargenInferior = 0; // Filas
    public $SangriaDeImpresora = 0; // Columnas
    public $ColumnaMargenDerecho = 80; // Columnas
    public $EncabezadoPagina;
    public $LongitudEncabezadoPagina = 10;
    public $DetallePagina;
    public $LongitudDetallePagina = 50;
    public $PiePagina;
    public $LongitudPiePagina = 6;
    public $TituloPagina;
    public $LongitudTituloPagina = 0;
    public $ResumenPagina;
    public $LongitudResumenPagina = 0;
    public $FFantesImprimir = false;
    public $FFdespuesImprimir = false;
    public $InformeResumen = false;
    public $inVariables = array();

    public function __construct(array $propiedades_pagina = array()) {
        foreach ($propiedades_pagina as $propiedad => $valor) {
            $this->$propiedad = $valor;
        }

        $this->formVariables();

        if (!$this->VerificarLongitudTotal()) {
            echo "Error: La suma de longitud de componentes excede la longitud total del reporte!\n";
            exit;
        }

        $this->ConstruirEstructura();
    }

    public function ConstruirEstructura() {
        $this->EncabezadoPagina = new EncabezadoPagina($this->LongitudEncabezadoPagina, $this->ColumnaMargenDerecho);
        $this->DetallePagina = new DetallePagina($this->LongitudDetallePagina, $this->ColumnaMargenDerecho);
        $this->PiePagina = new PiePagina($this->LongitudPiePagina, $this->ColumnaMargenDerecho);
        $this->TituloPagina = new TituloPagina($this->LongitudTituloPagina, $this->ColumnaMargenDerecho);
        $this->ResumenPagina = new ResumenPagina($this->LongitudResumenPagina, $this->ColumnaMargenDerecho);
    }

    public function VerificarLongitudTotal() {
        if ($this->LongitudPagina >= ($this->MargenSuperior + $this->LongitudEncabezadoPagina + $this->LongitudDetallePagina + $this->LongitudPiePagina + $this->MargenInferior)) {
            if (($this->LongitudDetallePagina - $this->MargenSuperior - $this->MargenInferior) > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function TestContenido() {
        
    }

    public function RellenarPaginaAleatorio() {
        $this->EncabezadoPagina->RellenarAleatorio();
        $this->DetallePagina->RellenarAleatorio();
        $this->PiePagina->RellenarAleatorio();
        $this->TituloPagina->RellenarAleatorio();
        $this->ResumenPagina->RellenarAleatorio();
    }

    public function ImprimirPagina() {
        $this->EncabezadoPagina->ImprimirBloque();
        $this->DetallePagina->ImprimirBloque();
        $this->PiePagina->ImprimirBloque();
        $this->TituloPagina->ImprimirBloque();
        $this->ResumenPagina->ImprimirBloque();
    }

}