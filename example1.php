<?php
require_once('ReportLib/autoload.php');

$Reporte = new ReportLib();
$Reporte->formatSource["tipo"] = "file";
$Reporte->formatSource["sourcelocation"] = "Formato1.json";
//$Reporte->setVariables(
//      "clave: contraseña", "campo: valor", "numero: 56", "tema: psicologia"
//);
$Reporte->setVariables("fecha_inicio: $Reporte->datenow");
$Reporte->formVariables();
$Reporte->CargarFormato();
if ($link = mysqli_connect("thedbhost", "userdb", "thepassword", "thedbname", "3306")) {
    $Reporte->LoadData($link);
} else {
    echo "No podemos conectarnos.\n";
    exit;
}

$Reporte->ToPrint();
