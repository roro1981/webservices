<?php
error_reporting(E_ALL & ~E_NOTICE);
ini_set('error_reporting',E_ALL);
ini_set('display_errors',1);
require_once('../../../librerias/nusoap-0.9.5/lib/nusoap.php');
function rutBloqueado($rut,$dv)
{   
    $result=array();
      $wsdl = 'https://www.rentanacional.cl/rnws/per_blq/actu_estado.php?wsdl';
  
    $client = new nusoap_client($wsdl, 'wsdl');
    $err = $client->getError();
    if ($err) {
       echo '<h2>Constructor error</h2>' . $err;
       exit();
    }
     
    $parametros=Array ( 'rut' => $rut, 'dv' => $dv, 'id_emp' => 1, 'id_trx' => 32, 'llave' => 'kjoiojio34343okjo2j2o2j2oj' );

    $result=$client->call('fcnactualizaRut', $parametros);
    print_r($result);
}
rutBloqueado('14164297','9');
?>