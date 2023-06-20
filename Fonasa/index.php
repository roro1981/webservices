<?php

/**
 * @abstract archivo carga menu inicial de portal abogados
 * 
 * @author Rodrigo Panes Fuentes <rpanes@rentanac.cl>
 * @version 1.0 - Creacion - 05/01/2021
 * 
 */

	error_reporting(E_ALL);
	include("base.adj");
	include("funciones.adj");
	include("funciones_cobranza.adj");
	noCache();
	echo getHtmlInicio("Intranet Corporativa - Renta Nacional Compañia de Seguros");
	is_logged();
	try {
		$conn_sisgen = TraeCon("sisgen");
		$conn_db = TraeCon("siniestro");
		$conn=TraeCon("produccion");
		$sis_id = $_GET['sis_id'];
		$prog_id = $_GET['prog_id'];
		foreach($conn_sisgen->query("select sis_descr from sistema where sis_id='$sis_id'") as $row_sistema){
			$sis_descr = $row_sistema['SIS_DESCR'];
		}
	}

	catch(PDOException $e){ 
		print "<p>Error: No puede conectarse con la base de datos.</p>\n";
		print "<p>Error: ".$e->getMessage()."</p>\n";
	}
	$perfil = "";
	$user_log = $_SESSION['Usuario'];
	$perfil_aux = nombrePerfilBdCli($conn_sisgen, $user_log);
	if($perfil_aux != ""){
		$perfil = $perfil_aux;
	}
?>
<link rel="stylesheet" type='text/css' href="<?=$URL_HOME?>/librerias/nightly/jquery.qtip.min.css"/>
<link rel="stylesheet" type='text/css' href="<?=$URL_HOME?>/librerias/jquery.alerts/jquery.alerts.css">
<link rel="stylesheet" type='text/css' href="<?=$URL_HOME?>/librerias/fancybox/css/jquery.fancybox-buttons.css">
<link rel="stylesheet" type='text/css' href="<?=$URL_HOME?>/librerias/fancybox/css/jquery.fancybox-thumbs.css">
<link rel="stylesheet" type='text/css' href="<?=$URL_HOME?>/librerias/fancybox/css/jquery.fancybox.css">
<link rel="stylesheet" type='text/css' href="css/estilo.css">
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src="<?=$URL_HOME?>/librerias/jquery.alerts/jquery.alerts.js" type='text/javascript'></script>
<script src="<?=$URL_HOME?>/librerias/nightly/jquery.qtip.min.js" type='text/javascript'></script>
<script src="<?=$URL_HOME?>/js/validaciones.js" type='text/javascript'></script>
<script src="<?=$URL_HOME?>/librerias/jquery-ui-1.8.17/development-bundle/ui/jquery-ui-1.8.17.custom.js" type='text/javascript'></script>
<script src="<?=$URL_HOME?>/librerias/jquery.data-table/jquery.dataTables.js" type='text/javascript'></script>
<script src="<?=$URL_HOME?>/librerias/fancybox/jquery.fancybox.js"></script>
<script src="<?=$URL_HOME?>/librerias/fancybox/jquery.fancybox-buttons.js"></script>
<script src="<?=$URL_HOME?>/librerias/fancybox/jquery.fancybox-thumbs.js"></script>
<script src="<?=$URL_HOME?>/lib_prop/upd_docs.js"></script>
<script type="text/javascript" src="<?=$URL_HOME?>/librerias/jquery-ui-1.8.17/development-bundle/ui/jquery.ui.tabs.js"></script>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" integrity="sha512-uto9mlQzrs59VwILcLiRYeLKPPbS/bT71da/OEBYEwcdNUk8jYIy+D176RYoop1Da+f9mvkYrmj5MCLZWEtQuA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="text/javascript">
	$(document).ready(function(){
		// Mostrar las pestañas
             $("#pestanas").tabs();
		//$("img[title]").qtip();
		$("#d_Cargando").css('display', 'none');
	});
	
$(document).on('click','#trae_datos',function(e){
		var bono=$("#num_bono").val();
		if(bono=="" || bono==0){
			jAlert("Ingrese numero de bono");
			return false;
		}
		
	$.ajax({
			type	: "POST",
			url		: "consumo_WS.php",
			async	: true,
			data	: {
			bono 	: bono
			},

			success: function(r){
				var sep=r.split("***");
				if(sep[0] != 'NO'){
					$("#dat1").html(sep[0]);
					$("#dat2").html(sep[1]);
					$("#dat3").html(sep[2]);
				}else{
					jAlert(sep[1],"Respuesta desde fonasa");
					$("#dat1").html("");
					$("#dat2").html("");
					$("#dat3").html("");
				}
			}
	});
		
});

</script>
<style type="text/css">
	.btnfiltros img {
		border: none;
	}

	.btnfiltros {
		text-align: right;
	}

	#dtodos {
		text-align: right;
		padding: 0;
		margin: 0;
		padding-top: 11px;
	}

	.dataTables_length {
		text-align: left;
	}

	.none {
		display: none;
	}

	.cargando {
		display: none;
		text-align: center !important;
	}

	.d_filtros {
		width: 80%;
		display: inline-block;
		float: left;
		text-align: left;
		margin: 0 0 0 5px;
	}

	.colx2 label:nth-child(3) {
		margin-left: 25px;
	}

	.colx2 label {
		width: 22% !important;
	}

	.ui-autocomplete-input .ui-widget .ui-widget-content .ui-corner-left {
		text-align: left !important;
		
	}

	.btn_info {
		width	: 56px;
		height	: 56px;
		cursor	: pointer; 
		background-color:transparent;
	}

	button {
		    -moz-box-shadow:inset 0px 1px 0px 0px #ffffff;
		    -webkit-box-shadow:inset 0px 1px 0px 0px #ffffff;
		    box-shadow:inset 0px 1px 0px 0px #ffffff;
		    background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #f1f1f1), color-stop(1, #cccccc) );
		    background:-moz-linear-gradient( center top, #f1f1f1 5%, #cccccc 100% );
		    filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#f1f1f1', endColorstr='#cccccc');
		    background-color:#f1f1f1;
		    padding-left: 0px;
		    padding-right: 0px;
		    -moz-border-radius:3px;
		    -webkit-border-radius:3px;
		    border-radius:3px;
		    border:1px solid #999999;
		    display:inline-block;
		    color:#666666;
		    font-family:arial;
		    font-size:11px !important;
		    font-weight:bold;
		    height: 30px;
		    text-decoration:none;
		}

	button:hover {
	    background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #cccccc), color-stop(1, #f1f1f1) );
	    background:-moz-linear-gradient( center top, #cccccc 5%, #f1f1f1 100% );
	    filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#cccccc', endColorstr='#f1f1f1');
	    background-color:#cccccc;
	}

	button:active {
	    position:relative;
	    top:1px;
	}

	button:active disabled{
	    position:relative;
	}
	.dvMenu{
		border: 1px solid #a6c9e2;
		border-radius:10px;
	  	top:50%;
		left:50%;
		background: linear-gradient( #fcfdfd,#e8f3ff);
		margin: 0px auto;
		width:300px;
		text-align: center;
	}
	.btnMenu {
	    margin: 10px 0 10px 0;
	    width: 200px;
	    height: 40px;
	}
	.table {     
    font-family: "Lucida Sans Unicode", "Lucida Grande", Sans-Serif;
    font-size: 12px;    
    width: 480px; 
    text-align: left;    
    border-collapse: collapse; 
    margin-top: 5px;
    
}

.td {    
    padding: 5px;        
    color: #669;    
    border: 1px solid; 
    text-align: left;
}
.tr:hover .td { 
    background: #d0dafd; 
    color: #339; 
}
.col1{
    width:100%;
    text-align:left;
    padding:0;
}
.col1 p{
    margin:0;
    padding:0;
    width:100%;
    padding-bottom: 3px;
}
.col1 label{
    margin:0;
    padding:0;
    width:25%;
    display:inline-block;
    font-size: 9pt;
}

.col1 span{
    margin:0;
    padding:0;
    width:55%;
    display:inline-block;
    color: #465552;
    font-family: Arial,sans-serif,serif;
    font-size: 9pt;
    font-weight: bold;
}
.dospuntos{
    margin:0 !important;
    padding:0 !important;
    width: 20px !important;
	text-align: center !important;
    display:inline-block;
}
</style> 
</head>
<body>
<div id="d_imgs_tabla" style="display:none;"></div>
<div id="d_Principal" class="container_12" style="font-size: 13px !important;">
	<div class="grid_12" id="d_Sistema"><?php echo GetHeader($sis_descr,$_SESSION['Usuario_Nombre'],$prog_id); ?></div>
		<div class="grid_12" id="d_Filtro_02" style="display: inline;">
			<br>
			<div id="dialog-form" title="" style="display: none;">
				<form>
					<div id="d_Confirma_insp" style="display: none;"></div>
				</form>
			</div> 
			<div id="d_Info" style='display: inline;overflow: auto'>
				<fieldset style='width:920px; margin: 0 -55;height:550px'>
				<legend id="le_Titulo_2" align="left" style="text-align:center; padding-bottom: 20px;">Consulta bono fonasa</legend>
					<table cellpadding='0' cellspacing='0' style='display: inline;'>
						<tr id="menu" style="">
							<td style='width: 400px;'>
								<table style="display: inline;" cellpadding="0" cellspacing="0">
									<tr>
										<td style='width: 700px; text-align: center;'>
											<div id="d_Lista_Plns" align="center">
												<div class="dvMenu" id="dvMenu">
						                        	Ingrese bono <input type="text" style="width: 150px;height: 20px;" id="num_bono">
						                        	<button id="trae_datos"><img src="http://dti20.rentanac.cl/imagenes/buscar2_24x24.png" alt="P"></button>
						     					</div>
											</div>
											<div id="d_Cargando" style="display: inline;"><img src="<?=$URL_HOME?>/imagenes/spinner.gif"> CARGANDO...</div>
										</td>
									</tr>
									<tr>
                                        <td style='width: 700px; text-align: left;'>
                                        <div id="pestanas" style='width: 600px;margin-top:10px'>
									        <ul>
									            <li><a href="#dat1">Datos bono</a></li>
									            <li><a href="#dat2">Prestaciones valorizadas</a></li>
									            <li><a href="#dat3">Consulta compañías</a></li>

									        </ul>
									        <div id="dat1"></div>
									        <div id="dat2"></div>
									        <div id="dat3"></div>
									        
								      </div>
								  </td>
                                    </tr>
								</table>
							</td>
						</tr>
						
                       </table>
                              

                           
				</fieldset>
			</div>
		</div>
	</div>
<?php echo GetHtml2(); $conn_sisgen = null; $conn= null; ?>
</div>