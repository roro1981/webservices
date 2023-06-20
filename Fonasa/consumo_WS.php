<?php
/*error_reporting(E_ALL & ~E_NOTICE);
ini_set('error_reporting',E_ALL);
ini_set('display_errors',1);
ini_set('auto_detect_line_endings',TRUE);*/

/**
 * @abstract Archivo que consulta servicios bonos fonasa.
 * 
 * @author Rodrigo Panes Fuentes <rpanes@rentanac.cl>
 * @version 1.0 - Creacion - 07/10/2021
 * 
 */
function mungXML($xml) //funcion que transforma en array los datos extraidos desde el servicio fonasa
{
    $obj = SimpleXML_Load_String($xml);
    if ($obj === FALSE) return $xml;

    // GET NAMESPACES, IF ANY
    $nss = $obj->getNamespaces(TRUE);
    if (empty($nss)) return $xml;

    // CHANGE ns: INTO ns_
    $nsm = array_keys($nss);
    foreach ($nsm as $key)
    {
        // A REGULAR EXPRESSION TO MUNG THE XML
        $rgx
        = '#'               // REGEX DELIMITER
        . '('               // GROUP PATTERN 1
        . '\<'              // LOCATE A LEFT WICKET
        . '/?'              // MAYBE FOLLOWED BY A SLASH
        . preg_quote($key)  // THE NAMESPACE
        . ')'               // END GROUP PATTERN
        . '('               // GROUP PATTERN 2
        . ':{1}'            // A COLON (EXACTLY ONE)
        . ')'               // END GROUP PATTERN
        . '#'               // REGEX DELIMITER
        ;
        // INSERT THE UNDERSCORE INTO THE TAG NAME
        $rep
        = '$1'          // BACKREFERENCE TO GROUP 1
        . '_'           // LITERAL UNDERSCORE IN PLACE OF GROUP 2
        ;
        // PERFORM THE REPLACEMENT
        $xml =  preg_replace($rgx, $rep, $xml);
    }

    return $xml;

} // End :: mungXML()

//consulta de servicio
$bono=$_POST['bono'];
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://svm.aach.cl/wsSvmConsultaBono/wsSvmConsultaBono.asmx?op=ConsultaBonoXML',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'<?xml version="1.0" encoding="utf-8"?>
<soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
  <soap12:Body>
    <ConsultaBonoXML xmlns="http://svm.aach.cl/">
      <Usuario>FN02004141</Usuario>
      <Clave>R3nt$.nac21</Clave>
      <Bono>'.$bono.'</Bono>
    </ConsultaBonoXML>
  </soap12:Body>
</soap12:Envelope>',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/soap+xml'
  ),
));

$response = curl_exec($curl);

curl_close($curl);

$plainXML = mungXML( trim($response) );
$arrayResult = json_decode(json_encode(SimpleXML_Load_String($plainXML, 'SimpleXMLElement', LIBXML_NOCDATA)), true);

$data=$arrayResult['soap_Body']['ConsultaBonoXMLResponse']['ConsultaBonoXMLResult']['Root'];
$respuesta=$data['codigoRespuesta'];
$glosa=$data['glosaRespuesta'];
//se valida si la respuesta es bono encontrado se procesa para mostrarlo por pantalla : sino se muestra la descripcion del error
if($respuesta==0){
  //datos bono
  $folio=$data['folio'];
  $fec_emision=date("d-m-Y", strtotime($data['fechaEmision']));
  $rut_tit=$data['runTitular'];
  $rut_benef=$data['runBeneficiario'];
  $nom_benef=$data['nombreBeneficiario'];
  $rut_prest=$data['runPrestador'];
  $nom_prest=$data['nombrePrestador'];
  $estado=$data['estado'];
  switch ($estado) {
    case '1':
      $est="Creado";
    break;
    case '2':
      $est="Emitido";
    break;
    case '3':
      $est="Anulado";
    break;
    case '4':
      $est="Devuelto";
    break;
    case '6':
      $est="Devuelto total";
    break;
  }
  $copago=number_format($data['montoCopago'], 0, ',', '.');
  $bonificacion=number_format($data['montoBonificacion'], 0, ',', '.');
  $total=number_format($data['montoTotal'], 0, ',', '.');

  $datos="";
  $datos .='<div class="col1"><p><label>Folio</label><label class="dospuntos">:</label><span>'.$folio.'</span></p></div>';
  $datos .='<div class="col1"><p><label>Fecha emisión</label><label class="dospuntos">:</label><span>'.$fec_emision.'</span></p></div>';
  $datos .='<div class="col1"><p><label>Rut titular</label><label class="dospuntos">:</label><span>'.$rut_tit.'</span></p></div>';
  $datos .='<div class="col1"><p><label>Rut beneficiario</label><label class="dospuntos">:</label><span>'.$rut_benef.'</span></p></div>';
  $datos .='<div class="col1"><p><label>Beneficiario</label><label class="dospuntos">:</label><span>'.$nom_benef.'</span></p></div>';
  $datos .='<div class="col1"><p><label>Rut prestador</label><label class="dospuntos">:</label><span>'.$rut_prest.'</span></p></div>';
  $datos .='<div class="col1"><p><label>Prestador</label><label class="dospuntos">:</label><span>'.$nom_prest.'</span></p></div>';
  $datos .='<div class="col1"><p><label>Estado</label><label class="dospuntos">:</label><span>'.$est.'</span></p></div>';
  $datos .='<div class="col1"><p><label>Monto copago</label><label class="dospuntos">:</label><span>'.$copago.'</span></p></div>';
  $datos .='<div class="col1"><p><label>Monto bonificación</label><label class="dospuntos">:</label><span>'.$bonificacion.'</span></p></div>';
  $datos .='<div class="col1"><p><label>Total</label><label class="dospuntos">:</label><span>'.$total.'</span></p></div>';

  //prestaciones valorizadas
  $consultas=json_encode($data['prestacionesValorizadas']['PrestacionesValorizada'], true);
  $array=json_decode($consultas,true);
  if(count($array)==5){
    $tabla2="";
    $tabla2 = '<table class="table">';
    $tabla2 .= '<tr><th>Código</th><th>Copago</th><th>Bonificación</th><th>Total</th><th>Cantidad</th></tr>';
    $tabla2 .="<tr><td class='td'>".$array['codigo']."</td>";
    $tabla2 .="<td class='td'>".$array['montoCopago']."</td>";
    $tabla2 .="<td class='td'>".$array['montoBonificacion']."</td>";
    $tabla2 .="<td class='td'>".$array['montoTotal']."</td>";
    $tabla2 .="<td class='td'>".$array['cantidad']."</td></tr></table>";
  }else{
    $tabla2="";
    $tabla2 = '<table class="table">';
    $tabla2 .= '<tr><th>Código</th><th>Copago</th><th>Bonificación</th><th>Total</th><th>Cantidad</th></tr>';
    for ($x=0;$x<count($array); $x++){
      $tabla2 .="<tr>";
      if(count($array)>1){
        foreach ($array[$x] as $k => $v) {
            $tabla2 .="<td class='td'>".$v."</td>";
        }
        $tabla2 .="</tr>";
      }
    }
    $tabla2 .='</table>';
  }  

  //consulta de compañias
  $consultas2=json_encode($data['consultaCias']['ConsultaCia'], true);
  $array2=json_decode($consultas2, true);
  $cuenta=count($array2);
  //var_dump($array2['consultas']);
  //echo is_null($array2['consultas']);
  $tabla3="";
  $tabla3 = '<table class="table">';
  $tabla3 .= '<tr><th>Compañía</th><th>Consultas</th></tr>';
  for ($x=0;$x<count($array2); $x++){
    $tabla3 .="<tr>";
    if(is_null($array2['consultas'])){
      foreach ($array2[$x] as $k => $v) {
          $tabla3 .="<td class='td'>".$v."</td>";
      }
      $tabla3 .="</tr>";
    }
  }
  $tabla3 .='</table>';
  echo $datos."***".$tabla2."***".$tabla3;
}else{
  echo "NO***".$glosa;
}
?>