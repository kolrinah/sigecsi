<?php if (!defined('BASEPATH')) exit('Sin Acceso Directo al Script');      
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *  SISTEMA DE GESTION Y CONTROL DEL SERVICIO INTERNO                *
 *  DESARROLLADO POR: ING.REIZA GARCÍA                               *
 *                    ING.HÉCTOR MARTÍNEZ                            *
 *  PARA:  MINISTERIO DEL PODER POPULAR PARA RELACIONES EXTERIORES   *
 *  FECHA: OCTUBRE DE 2013                                             *
 *  FRAMEWORK PHP UTILIZADO: CodeIgniter Version 2.1.3               *
 *                           http://ellislab.com/codeigniter         *
 *  TELEFONOS PARA SOPORTE: 0416-9052533 / 0212-5153033              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
class Reportes_ejecucion extends CI_Controller {
  function __construct() 
  {
     parent::__construct();
     $this->load->helper('form');
     $this->load->library('Comunes');
     $this->load->model('Estructura');
     $this->load->model('Proyectos');
  }
  
  function index()
  {
    // VERIFICAMOS SI EXISTE SESION ABIERTA    
    if (!$this->session->userdata('aprobado')) {redirect ('acceso', 'refresh'); exit();}
    // RECUPERA LA FECHA Y HORA DEL SISTEMA
    date_default_timezone_set('America/Caracas'); // Establece la Hora de Venezuela para funciones de fecha y hora
    $ahora=  getdate(time());
    $year_poa=$ahora['year']; // El año inicial del POA es el año en curso
    
    $data=array();
    $data['titulo']='Reportes de Ejecución de Proyectos';
    $data['subtitulo']='Ejecución Presupuestaria del Año:';
    $data['year_poa']= array(      
                        'name' => 'year_poa',
                        'id' => 'year_poa',
                        'class'=>'Titulos',
                        'value' => $year_poa,
                        'maxlength' => '4',
                        'title'=>'Año del POA',
                        'readonly'=>'readonly',
                        'size' => 4);
    
    $data['flecha_sum']= array(
          'src' => base_url().'imagenes/forward_enabled.png',
          'alt' => 'Adelante',
          'class' => 'BotonIco',
          'width' => '',
          'height' => '',
          'title' => 'Clic para avanzar',
          'onclick'=> "javascript:
                       if ($('#year_poa').val()<$year_poa)
                       {
                         document.getElementById('year_poa').value ++;
                       }
                       actualiza();"
            );
    $data['flecha_sus']= array(
          'src' => base_url().'imagenes/back_enabled.png',
          'alt' => 'Atrás',
          'class' => 'BotonIco',
          'width' => '',
          'height' => '',
          'title' => 'Clic para retroceder',
          'onclick'=> "javascript:document.getElementById('year_poa').value --; actualiza();"
            ); 
    
     $data['pdf']= array(
          'src' => base_url().'imagenes/pdf.png',
          'alt' => 'pdf',
          'class' => 'BotonIco',
          'width' => '',
          'height' => '',
          'title' => 'Exportar a formato pdf',
          'onclick'=> "javascript:exportarPDF();"
            );
      
    $data['xls']= array(
          'src' => base_url().'imagenes/xls.png',
          'alt' => 'xls',
          'class' => 'BotonIco',
          'width' => '',
          'height' => '',
          'title' => 'Exportar a formato xls',
          'onclick'=> "javascript:exportarXLS();"
            );
    
    $data['contenido']='reportes/ejecucion';    
    $data['script']='<!-- Cargamos CSS de DataTables -->'."\n";    
    $data['script'].="\t".'<link rel="stylesheet" type="text/css" media="all" href="'.base_url().'css/dataTables.css"/>'."\n";
    $data['script'].='<!-- Cargamos JS para DataTables -->'."\n";
    $data['script'].="\t".'<script type="text/javascript" src="'.base_url().'js/jquery.dataTables.js"></script>'."\n";
    $data['script'].='<!-- Cargamos Nuestro JS -->'."\n";
    $data['script'].="\t".'<script type="text/javascript" src="'.base_url().'js/reportes_ejecucion.js"></script>'."\n";
       
     $data['tabla']=$this->listar_proyectos($year_poa);
    
 // CARGAMOS LA VISTA   
    $this->load->view('plantillas/plantilla_general',$data);  
  }  
  
  //////////////////////////////////////////////////////////////////////////////////////////
  
  function listar_proyectos($yearpoa=0)
  {
    if ($this->input->is_ajax_request()) $yearpoa=$this->input->post('yearpoa'); // Si la peticion vino por AJAX
    date_default_timezone_set('America/Caracas');

    
    $query=$this->Proyectos->listar_proyectos(' where id_estructura >1 ', $yearpoa);  
    
    if ($query->num_rows() === 0) // SI NO HAY PROYECTOS
    { 
      $tabla='<h2><center>El MPPRE No Posee Proyectos Registrados para el año indicado</center></h2>';      
      if ($this->input->is_ajax_request()) die($tabla);
      return $tabla;
    }  

    $proyectos=$query->result();
    
    $tabla='<h1>Ejecución de Proyectos para el año '.$yearpoa.'</h1>';
    // CONSTRUIMOS LA TABLA
    // ENCABEZADOS DE TABLA
    $tabla.='<table class="TablaNivel1 Zebrado">';
    $tabla.='<thead>';
    $tabla.='<tr>'; 
    $tabla.='<th width="50px">';
    $tabla.='</th>'; 
    $tabla.='<th width="180px">';
    $tabla.='UNIDAD ADMINISTRATIVA';
    $tabla.='</th>';
    $tabla.='<th width="70px">';
    $tabla.='CODIGO';
    $tabla.='</th>';
    $tabla.='<th>';
    $tabla.='NOMBRE DEL PROYECTO';
    $tabla.='</th>';
    $tabla.='<th style="text-align:right!important; width:120px;">';
    $tabla.='PLANIFICADO (Bs.)';
    $tabla.='</th>';
    $tabla.='<th style="text-align:right!important; width:120px;">';
    $tabla.='EJECUTADO (Bs.)';    
    $tabla.='</th>';  
    $tabla.='<th style="width:120px;">';
    $tabla.='METAS FISICAS (%)';    
    $tabla.='</th>'; 
    $tabla.='</tr>';
    $tabla.='</thead>';
    $tabla.='<tbody>';
    $ejecutado=0;
    $planificado=0;
      
    foreach ($proyectos as $p)
    {          
      $tabla.='<tr class="Resaltado" title="Clic para ver detalles de Ejecución"
                   onclick="javascript:revisarEjecucion('.$p->id_proyecto.')" >';
      $tabla.='<td>';      
      $tabla.=$p->codigo;
      $tabla.='</td>'; 
      $tabla.='<td style="text-align:left">';      
      $tabla.=$p->descripcion;
      $tabla.='</td>';       
      $tabla.='<td>';
      $tabla.=$p->cod_proy;
      $tabla.='</td>'; 
      $tabla.='<td style="text-align:left!important">';
      $tabla.=mb_convert_case($p->obj_esp, MB_CASE_UPPER);
      $tabla.='</td>'; 
      $tabla.='<td style="text-align:right!important;">';
      $tabla.=number_format($p->total, 2, ',','.');
      $tabla.='</td>';
      $tabla.='<td style="text-align:right!important; ">';
      $tabla.=number_format($p->ejecutado, 2, ',','.');
      $tabla.='</td>';
      $tabla.='<td>';
      $mp=isset($p->meta_planificada)?$p->meta_planificada:1;
      $ma=isset($p->meta_alcanzada)?$p->meta_alcanzada:0;
      $meta=0;
      if ($mp!=0) $meta=$ma/$mp*100;
      
      $tabla.=number_format($meta, 2, ',','.').' %';
      $tabla.='</td>';
      $tabla.='</tr>';
      $ejecutado+=$p->ejecutado;
      $planificado+=$p->total;
    }
    //PIE DE TABLA
    $tabla.='</tbody>';
    $tabla.='<tfoot>';
    $tabla.='<tr>';
    $tabla.='<td colspan="4" style="text-align:right!important">';
    $tabla.='<strong>T O T A L E S (Bs.)</strong>';
    $tabla.='</td>';
    $tabla.='<td style="text-align:right!important;">';
    $tabla.=number_format($planificado, 2, ',', '.'); 
    $tabla.='</td>';    
    $tabla.='<td style="text-align:right!important;">'; 
    $tabla.=number_format($ejecutado, 2, ',', '.'); 
    $tabla.='</td>';   
    $tabla.='<td>'; 
    $tabla.='</td>';      
    $tabla.='</tr>';
    $tabla.='</tfoot>';
    $tabla.='</table><br/><br/>';   
    
    if ($this->input->is_ajax_request()) die($tabla); // Si la peticion vino por AJAX
    return $tabla;
  }
  
  function revisar_ejecucion()
  {
      if (!$this->input->is_ajax_request()) die('Acceso Denegado');
      date_default_timezone_set('America/Caracas'); // Establece la Hora de Venezuela para funciones de fecha y hora
      
      $id_proyecto = intval($this->input->post('id_proyecto'));
            
      $proy = $this->Proyectos->obtener_proyecto($id_proyecto);
      if (!$proy)die('error consultando proyecto');
      
      // Validamos el código del proyecto
      $mre =  substr(trim($proy->cod_proy), 0, 2);      
      if ($mre!='06' or strlen(trim($proy->cod_proy))!=9 ) die('Error. Código de Proyecto no válido');
      
      // Igualamos los códigos al formato de SIGESP
      $uel = str_repeat("0", 19).$proy->codigo;
      $proyecto = str_repeat("0", 21).substr(trim($proy->cod_proy), 2, 4);
      $ae = str_repeat("0", 22).substr(trim($proy->cod_proy), 6);
      
      $ejecucion = $this->Proyectos->getEjecucionSigesp($proyecto, $ae, $uel);
      if (count($ejecucion)==0) // NO HAY MOVIMIENTOS
      {
         $accion=' onclick="javascript:actualiza();" ';        
         $tabla='<h2><center>';
         $tabla.='<img src="'.base_url().'imagenes/back.png"'.$accion;
         $tabla.='title="clic para regresar" class="BotonIco" ';
         $tabla.='/> &nbsp; '; 
         $tabla.='Proyecto sin Ejecución Registrada en SIGESP</center></h2>';
         die($tabla);
      }      
      
      $retorno='<img src="'.base_url().'imagenes/back.png" 
                     onclick="javascript:actualiza();"
                     title="clic para regresar" class="BotonIco" />'; 
      
      // IDENTIFICACION      
      $tabla="<h4>$retorno Ejecución Presupuestaria del Proyecto</h4>";
      $tabla.="<h4>$proy->cod_proy $proy->obj_esp</h4>";
      // LISTADO
      $tabla.='<table class="TablaNivel1 Zebrado">';
      $tabla.='<thead>';
      $tabla.='<tr>';
      $tabla.='<th>'; 
      $tabla.='AE';
      $tabla.='</th>';
      $tabla.='<th>'; 
      $tabla.='Act';
      $tabla.='</th>';
      $tabla.='<th>'; 
      $tabla.='Partida';
      $tabla.='</th>';
      $tabla.='<th>'; 
      $tabla.='Monto(Bs.)';
      $tabla.='</th>';
      $tabla.='<th>'; 
      $tabla.='Fecha';
      $tabla.='</th>';
      $tabla.='<th>'; 
      $tabla.='Detalles de la Operación';
      $tabla.='</th>';
      $tabla.='<th style="width:100px">'; 
      $tabla.='Actividad';
      $tabla.='</th>';
      $tabla.='</tr>';
      $tabla.='</thead>';
      $tabla.='<tfoot>';
      $tabla.='<tr>';
      $tabla.='<td>'; 
      $tabla.='AE';
      $tabla.='</td>';
      $tabla.='<td>'; 
      $tabla.='Act';
      $tabla.='</td>';
      $tabla.='<td>'; 
      $tabla.='Partida';
      $tabla.='</td>';
      $tabla.='<td>'; 
      $tabla.='Monto(Bs.)';
      $tabla.='</td>';
      $tabla.='<td>'; 
      $tabla.='Fecha';
      $tabla.='</td>';
      $tabla.='<td>'; 
      $tabla.='Detalles de la Operación';
      $tabla.='</td>';
      $tabla.='<td>'; 
      $tabla.='Actividad';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='</tfoot>';      
      $tabla.='<tbody>';
      
      //die(json_encode($ejecucion));
      
      foreach ($ejecucion as $e)
      {
        $id=$e->documento.$e->spg_cuenta;
        $id_partida =  substr($e->spg_cuenta, 0, 1).'.'.
                       substr($e->spg_cuenta, 1, 2).'.'.
                       substr($e->spg_cuenta, 3, 2).'.'.
                       substr($e->spg_cuenta, 5, 2).'.'.
                       substr($e->spg_cuenta, -2);
        
        $tabla.='<tr class="Resaltado" >';
        $tabla.='<td>';
        $tabla.=substr($e->codestpro4,-3);
        $tabla.='</td>';
        $tabla.='<td>';
        $tabla.=substr($e->codestpro5,-3);
        $tabla.='</td>';
        $tabla.='<td>';
        $tabla.=$id_partida;
        $tabla.='</td>';
        $tabla.='<td style="text-align:right">';
        $tabla.=number_format($e->compromiso, 2, ',','.');
        $tabla.='</td>';
        $tabla.='<td>';
        $tabla.=date("d/m/Y",strtotime($e->fecha));
        $tabla.='</td>';        
        $tabla.='<td style="text-align:left">';
        $tabla.='<strong>Documento: </strong>'.
                    (isset($e->codact)?$e->documento:utf8_encode($e->documento)).'<br/>';
        $tabla.='<strong>Descripción: </strong>'.
                    (isset($e->codact)?$e->nombre_prog:utf8_encode($e->nombre_prog)).'<br/>';
        $tabla.='<strong>Proveedor: </strong>'.
                    (isset($e->codact)?$e->nompro:utf8_encode($e->nompro));
        $tabla.='</td>';
        $tabla.='<td>';
        if (isset($e->codact))
        {
            $tabla.=$e->codact;
        }
        else
        {            
            $tabla.='( ? )';
        }        
        $tabla.='</td>';
        $tabla.='</tr>';
      }      
      $tabla.='</tbody>';
      $tabla.='</table>';  
      
      die ($tabla);
  }    
  
}