<?php if (!defined('BASEPATH')) exit('Sin Acceso Directo al Script');      
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *  SISTEMA DE GESTION Y CONTROL DEL SERVICIO INTERNO                *
 *  DESARROLLADO POR: ING.REIZA GARCÍA                               *
 *                    ING.HÉCTOR MARTÍNEZ                            *
 *  PARA:  MINISTERIO DEL PODER POPULAR PARA RELACIONES EXTERIORES   *
 *  FECHA: AGOSTO DE 2013                                             *
 *  FRAMEWORK PHP UTILIZADO: CodeIgniter Version 2.1.3               *
 *                           http://ellislab.com/codeigniter         *
 *  TELEFONOS PARA SOPORTE: 0416-9052533 / 0212-5153033              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
class Reportes_consolidados extends CI_Controller {
  function __construct() 
  {
     parent::__construct();
     $this->load->helper('form');
     $this->load->model('Insumos');
     $this->load->model('Personal');
     $this->load->model('Proyectos');
     $this->load->library('Comunes');
  }
  
  function index()
  {
    // VERIFICAMOS SI EXISTE SESION ABIERTA    
    if (!$this->session->userdata('aprobado')) {redirect ('acceso', 'refresh'); exit();}
    
    // VERIFICAMOS SI EL USARIO ES ADMIN O DE PLANES
    if (!($this->session->userdata('administrador') ||
          $this->session->userdata('id_estructura')=='48'))exit('Sin Acceso al Script');
    
    // RECUPERA LA FECHA Y HORA DEL SISTEMA
    date_default_timezone_set('America/Caracas'); // Establece la Hora de Venezuela para funciones de fecha y hora
    $ahora=  getdate(time());
    $yearpoa=$ahora['year']+1; // El año inicial del POA es el año siguiente al año en curso
    
    $data=array();
    $data['titulo']='Reportes Consolidados';
    $data['subtitulo']='Planificación Operativa del Año:';
    $data['year_poa']= array(      
                        'name' => 'yearpoa',
                        'id' => 'yearpoa',
                        'class'=>'Titulos',
                        'value' => $yearpoa,
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
                       if ($('#yearpoa').val()<$yearpoa)
                       {
                         document.getElementById('yearpoa').value ++;
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
          'onclick'=> "javascript:document.getElementById('yearpoa').value --; actualiza();"
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
    
    $data['contenido']='reportes/consolidados';
    $data['script']='<!-- Cargamos CSS de DataTables -->'."\n";    
    $data['script'].="\t".'<link rel="stylesheet" type="text/css" media="all" href="'.base_url().'css/dataTables.css"/>'."\n";
    $data['script'].='<!-- Cargamos JS para DataTables -->'."\n";
    $data['script'].="\t".'<script type="text/javascript" src="'.base_url().'js/jquery.dataTables.js"></script>'."\n";
    $data['script'].='<!-- Cargamos Nuestro JS -->'."\n";
    $data['script'].="\t".'<script type="text/javascript" src="'.base_url().'js/reportes_consolidados.js"></script>'."\n";
       
    // SELECTOR DE REPORTE
    $verUnidad=1;
    $selector=array('1'=>'','2'=>'','3'=>'','4'=>'','5'=>'');
    $selector[$this->session->userdata('selector')]='checked="checked"';
    $radio=' <input type="radio" name="selector" onchange="actualizaSelector($(this).val(),'.
                 $verUnidad.')" ';
      
      $selectores='<div title="Seleccione su opción">';
      $selectores.=/*$radio.$selector['1'].' value="1" id="selector1"/>'.
              '<label for="selector1" title="Productos Administrativos"></label>'.*/
              $radio.$selector['2'].' value="2" id="selector2" />'.
              '<label for="selector2" title="Proyectos"></label>'.
              $radio.$selector['3'].' value="3" id="selector3" />'.
              '<label for="selector3" title="Requerimientos de Personal"></label>'.
              $radio.$selector['4'].' value="4" id="selector4" />'.
              '<label for="selector4" title="Requerimientos de Insumos"></label>'.
              $radio.$selector['5'].' value="5" id="selector5" />'.
              '<label for="selector5" title="Presupuesto por Partidas"></label>';      
      $selectores.='</div>';        
    $data['selectores']=$selectores;
    
    $data['tabla']=$this->consolidar($yearpoa);
         
   // CARGAMOS LA VISTA   
    $this->load->view('plantillas/plantilla_general',$data);      

  }  
  
  //////////////////////////////////////////////////////////////////////////////////////////
  function consolidar($yearpoa=0)
  {
     // VERIFICAMOS SI EXISTE SESION ABIERTA    
     if (!$this->session->userdata('aprobado')) {redirect ('acceso', 'refresh'); exit();}
    
     // VERIFICAMOS SI EL USARIO ES ADMIN O DE PLANES
     if (!($this->session->userdata('administrador') ||
          $this->session->userdata('id_estructura')=='48'))exit('Sin Acceso al Script');
      
     if ($this->input->is_ajax_request())  // Si la peticion vino por AJAX
         $yearpoa=$this->input->post('yearpoa');
    
     // VERIFICAMOS EL SELECTOR DE REPORTE
     switch ($this->session->userdata('selector'))
     {
        case 1:  // PRODUCTOS ADMINISTRATIVOS
            $consolidado='';
            break;
        case 2:  // PROYECTOS
            $consolidado=$this->_consolidar_proyectos($yearpoa);  
            break;
        case 3:  // PERSONAL
            $consolidado=$this->_consolidar_personal($yearpoa);  
            break;
        case 4: // INSUMOS
            $consolidado=$this->_consolidar_insumos($yearpoa);
            break;
        case 5: // PRESUPUESTO
            $consolidado=$this->_consolidar_presupuesto($yearpoa);
            break;        
        default :
            $consolidado='ERROR';
     }            
    
     if ($this->input->is_ajax_request()) die($consolidado); // Si la peticion vino por AJAX     
     return $consolidado;
  }
      
  function exportarPDF($yearpoa)
  {  
     // VERIFICAMOS SI EXISTE SESION ABIERTA    
     if (!$this->session->userdata('aprobado')) {redirect ('acceso', 'refresh'); exit();}
    
     // VERIFICAMOS SI EL USARIO ES ADMIN O DE PLANES
     if (!($this->session->userdata('administrador') ||
          $this->session->userdata('id_estructura')=='48'))exit('Sin Acceso al Script'); 
      
     // VERIFICAMOS EL SELECTOR DE REPORTE
     switch ($this->session->userdata('selector'))
     {
        case 1:  // PRODUCTOS ADMINISTRATIVOS
            die('');
            break;
        case 2:  // PROYECTOS
            redirect('reportes_consolidados/proyectosPDF/'.$yearpoa,'refresh');
            break;
        case 3:  // PERSONAL
            redirect('reportes_consolidados/personalPDF/'.$yearpoa,'refresh');            
            break;
        case 4: // INSUMOS
            redirect('reportes_consolidados/insumosPDF/'.$yearpoa,'refresh');            
            break;
        case 5: // PARTIDAS
            redirect('reportes_consolidados/presupuestoPDF/'.$yearpoa,'refresh');   
            break;        
        default :
            die('ERROR');
     }   
     exit(0);
  }
  
  function insumosPDF($yearpoa)
  {
     // VERIFICAMOS SI EXISTE SESION ABIERTA    
     if (!$this->session->userdata('aprobado')) {redirect ('acceso', 'refresh'); exit();}
    
     // VERIFICAMOS SI EL USARIO ES ADMIN O DE PLANES
     if (!($this->session->userdata('administrador') ||
          $this->session->userdata('id_estructura')=='48'))exit('Sin Acceso al Script');
     
     $insumos=$this->Insumos->consolidar_requerimiento_insumos($yearpoa);  
     if ($insumos->num_rows() === 0) // SI NO HAY REQUERIMIENTO DE INSUMOS
     {       
       exit('El MPPRE No Posee Requerimiento de Insumos para el año indicado');
     } 
     require_once 'application/libraries/InsumosPDF.php';     
     
     $pdf= new InsumosPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false); 
     
     $pdf->cargarData($insumos, $yearpoa);
     
     $pdf->Output('RequerimientosInsumos.pdf', 'I');
  }
  
  function personalPDF($yearpoa)
  {
     // VERIFICAMOS SI EXISTE SESION ABIERTA    
     if (!$this->session->userdata('aprobado')) {redirect ('acceso', 'refresh'); exit();}
    
     // VERIFICAMOS SI EL USARIO ES ADMIN O DE PLANES
     if (!($this->session->userdata('administrador') ||
          $this->session->userdata('id_estructura')=='48'))exit('Sin Acceso al Script');
     
     $personal=$this->Personal->consolidar_requerimiento_personal($yearpoa);  
     if ($personal->num_rows() === 0) // SI NO HAY REQUERIMIENTO DE PERSONAL
     {       
       exit('El MPPRE No Posee Requerimiento de Personal para el año indicado');
     } 
     require_once 'application/libraries/PersonalPDF.php';     
     
     $pdf= new PersonalPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false); 
     
     $pdf->cargarData($personal, $yearpoa);
     
     //var_dump($pdf->getMargins());
     $pdf->Output('RequerimientosPersonal.pdf', 'I');
  }

  function proyectosPDF($yearpoa)
  {
     // VERIFICAMOS SI EXISTE SESION ABIERTA    
     if (!$this->session->userdata('aprobado')) {redirect ('acceso', 'refresh'); exit();}
    
     // VERIFICAMOS SI EL USARIO ES ADMIN O DE PLANES
     if (!($this->session->userdata('administrador') ||
          $this->session->userdata('id_estructura')=='48'))exit('Sin Acceso al Script');
     
     $proyectos=$this->Proyectos->listar_proyectos(' where id_estructura>1 ',$yearpoa);  
     if ($proyectos->num_rows() === 0) // SI NO HAY PROYECTOS
     {       
       exit('El MPPRE No Posee Proyectos Registrados para el año indicado');
     } 
     require_once 'application/libraries/ProyectosPDF.php';     
     
     $pdf= new ProyectosPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false); 
     
     $pdf->cargarData($proyectos, $yearpoa);
     
     $pdf->Output('Proyectos.pdf', 'I');
  }
  
  function presupuestoPDF($yearpoa)
  {
     // VERIFICAMOS SI EXISTE SESION ABIERTA    
     if (!$this->session->userdata('aprobado')) {redirect ('acceso', 'refresh'); exit();}
    
     // VERIFICAMOS SI EL USARIO ES ADMIN O DE PLANES
     if (!($this->session->userdata('administrador') ||
          $this->session->userdata('id_estructura')=='48'))exit('Sin Acceso al Script');
     
     $partidas=$this->Proyectos->obtener_presupuesto_yearpoa_x_partidas($yearpoa);  
     if ($partidas->num_rows() === 0) // SI NO HAY PROYECTOS
     {       
       exit('El MPPRE No Posee Proyectos Registrados para el año indicado');
     } 
     require_once 'application/libraries/PresupuestoPDF.php';     
     
     $pdf= new PresupuestoPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false); 
     
     $pdf->cargarData($partidas, $yearpoa);
     
     $pdf->Output('Presupuesto.pdf', 'I');
  }  
  
  function _consolidar_insumos($yearpoa)
  {      
    $insumos=$this->Insumos->consolidar_requerimiento_insumos($yearpoa);  
    
    if ($insumos->num_rows() === 0) // SI NO HAY REQUERIMIENTO DE INSUMOS EN LA UNIDAD
    { 
      $tabla='<h2><center>El MPPRE No Posee Requerimiento de Insumos para el año indicado</center></h2>';      
      return $tabla;
    }  

    $n=$insumos->num_rows();
    $insumos=$insumos->result();
    
    // CONSTRUIMOS LA TABLA     
    $i=0;    
    $pg=$insumos[$i]->partida_generica;
    
    $tabla='<h1>Requerimientos de Insumos para el año '.$yearpoa.'</h1>';

    while($i<$n)
    {
        // ENCABEZADOS DE TABLA
        $tabla.='<table width="100%"><tr>
                    <td style="vertical-align:middle; text-align:left" colspan="6">';
        $tabla.='<h4>'.$insumos[$i]->partida_generica.' - '. 
                mb_convert_case($insumos[$i]->tipo_insumo,MB_CASE_UPPER).'</h4>';
        $tabla.='</td>';
        $tabla.='</tr></table>'; 
        $tabla.='<table class="TablaNivel1 Zebrado">';
        $tabla.='<thead>';
        $tabla.='<tr>';          
        $tabla.='<th width="160px">';
        $tabla.='CODIGO';
        $tabla.='</th>';
        $tabla.='<th width="80px">';
        $tabla.='PARTIDA';        
        $tabla.='</th>';             
        $tabla.='<th style="text-align:left!important">';
        $tabla.='REQUERIMIENTO';
        $tabla.='</th>';
        $tabla.='<th width="80px">';
        $tabla.='CANT';        
        $tabla.='</th>';          
        $tabla.='<th width="130px" style="text-align:left!important">';
        $tabla.='UNIDAD DE MEDIDA';
        $tabla.='</th>';
        $tabla.='<th width="80px">';
        $tabla.='EXISTENCIA';
        $tabla.='</th>';
        $tabla.='</tr>';
        $tabla.='</thead>';
        $tabla.='<tbody>';
        
        while($pg==($insumos[$i]->partida_generica) && $i<$n)
        {         
             $tabla.='<tr class="Resaltado" >';
             $tabla.='<td title="Código Sigesp">';
             $tabla.=trim($insumos[$i]->codart);
             $tabla.='</td>';
             $tabla.='<td title="Partida Presupuestaria">';
             $tabla.=$insumos[$i]->spg_cuenta;
             $tabla.='</td>';              
             $tabla.='<td title="Nombre del Insumo" style="text-align:left!important">';
             $tabla.=trim($insumos[$i]->denart);
             $tabla.='</td>'; 
             $tabla.='<td title="Cantidad Requerida">';
             $tabla.=$insumos[$i]->requerido;
             $tabla.='</td>';                
             $tabla.='<td style="text-align:left!important" title="Unidad de Medida">';
             $tabla.=trim($insumos[$i]->denunimed);
             $tabla.='</td>';                
             $tabla.='<td title="Cantidad en Existencia">';
             $tabla.=$insumos[$i]->existencia;             
             $tabla.='</td>';                  
             $tabla.='</tr>'; 
             $i++;
             if ($i==$n) break;
          }
          @$pg=$insumos[$i]->partida_generica;
          //PIE DE TABLA
          $tabla.='</tbody>';
          $tabla.='<tfoot>';
          $tabla.='<tr>';          
          $tabla.='<td>';
          $tabla.='CODIGO';
          $tabla.='</td>';
          $tabla.='<td>';
          $tabla.='PARTIDA';        
          $tabla.='</td>';             
          $tabla.='<th style="text-align:left!important">';
          $tabla.='REQUERIMIENTO';
          $tabla.='</td>';
          $tabla.='<td>';
          $tabla.='CANT';        
          $tabla.='</td>';          
          $tabla.='<td style="text-align:left!important">';
          $tabla.='UNIDAD DE MEDIDA';
          $tabla.='</td>';
          $tabla.='<td>';
          $tabla.='EXISTENCIA';
          $tabla.='</td>';
          $tabla.='</tr>';
          $tabla.='</tfoot>';
          $tabla.='</table><br/><br/>';   
          if ($i==$n) break;               
    }   
    return $tabla;
  }  
          
  function _consolidar_personal($yearpoa)
  {      
    $query=$this->Personal->consolidar_requerimiento_personal($yearpoa);  
    
    if ($query->num_rows() === 0) // SI NO HAY REQUERIMIENTO DE PERSONAL
    { 
      $tabla='<h2><center>El MPPRE No Posee Requerimiento de Personal para el año indicado</center></h2>';      
      return $tabla;
    }  

    $n=$query->num_rows();
    $personal=$query->result();
    
    // CONSTRUIMOS LA TABLA     
    $i=0;    
    $ac=$personal[$i]->accion_centralizada;
    
    $tabla='<h1>Requerimientos de Personal para el año '.$yearpoa.'</h1>';

    while($i<$n)
    {
        // ENCABEZADOS DE TABLA
        $tabla.='<table width="100%"><tr>
                 <td style="vertical-align:middle; text-align:left" colspan="6">';
        $tabla.='<h4>'.(($personal[$i]->accion_centralizada=='t')?
                            'Personal Requerido por Acción Centralizada':
                            'Personal Requerido por Proyectos').'</h4>';
        $tabla.='</td>';
        $tabla.='</tr></table>';         
        $tabla.='<table class="TablaNivel1 Zebrado">';
        $tabla.='<thead>';
        $tabla.='<tr>';
        $tabla.='<th width="30px">';
        $tabla.='</th>';        
        $tabla.='<th width="220px" style="text-align:left!important">';
        $tabla.='TIPO DE PERSONAL';
        $tabla.='</th>';
        $tabla.='<th style="text-align:left!important">';
        $tabla.='PERSONAL REQUERIDO';
        $tabla.='</th>';
        $tabla.='<th width="50px">';
        $tabla.='<img src="'.base_url().'imagenes/femenino.png" ';
        $tabla.='title="Personal Femenino"';
        $tabla.='/>';
        $tabla.='</th>';
        $tabla.='<th width="50px">';
        $tabla.='<img src="'.base_url().'imagenes/masculino.png" ';
        $tabla.='title="Personal Masculino"';
        $tabla.='/>';          
        $tabla.='</th>';
        $tabla.='<th width="100px">';     
        $tabla.='TOTAL';
        $tabla.='</th>';          
        $tabla.='<th width="5px">';
        $tabla.='</th>';

        $tabla.='</tr>';
        $tabla.='</thead>';
        $tabla.='<tbody>';
        
        $machos=0;
        $hembras=0;
        while (($ac==$personal[$i]->accion_centralizada) && $i<$n)
        {    
           $tabla.='<tr class="Resaltado" >';
           $tabla.='<td>';
           $tabla.='</td>';
           $tabla.='<td title="Tipo de Personal" style="text-align:left!important">';
           $tabla.=trim($personal[$i]->tipo_personal);
           $tabla.='</td>'; 
             
           $tabla.='<td style="text-align:left!important" title="Personal">';
           $tabla.=trim($personal[$i]->personal);
           $tabla.='</td>';          
           $tabla.='<td>';
           $tabla.=$personal[$i]->femenino;             
           $tabla.='</td>';
           $tabla.='<td>';
           $tabla.=$personal[$i]->masculino;
           $tabla.='</td>';        
           $tabla.='<td>';         
           $tabla.=$personal[$i]->femenino + $personal[$i]->masculino;
           $tabla.='</td>';
           $tabla.='<td>';         
           $tabla.='</td>'; 
           $tabla.='</tr>'; 
            
           $hembras+=$personal[$i]->femenino;
           $machos+=$personal[$i]->masculino;
           $i++;
           if ($i==$n) break;
          }
          @$ac=$personal[$i]->accion_centralizada;
          //PIE DE TABLA
          $tabla.='</tbody>';
          $tabla.='<tfoot>';
          $tabla.='<tr>';
          $tabla.='<td>';
          $tabla.='</td>';
          $tabla.='<td>';        
          $tabla.='</td>';
          $tabla.='<td>';
          $tabla.='<strong>T O T A L E S</strong>';
          $tabla.='</td>';
          $tabla.='<td>';
          $tabla.=$hembras;
          $tabla.='</td>';
          $tabla.='<td>';        
          $tabla.=$machos;
          $tabla.='</td>';
          $tabla.='<td>'; 
          $tabla.=$hembras+$machos;
          $tabla.='</td>';          
          $tabla.='<td>'; 
          $tabla.='</td>'; 
          $tabla.='</tr>';
          $tabla.='</tfoot>';
          $tabla.='</table><br/><br/>';   
          if ($i==$n) break;          
        }      
    return $tabla;
  }    
  
  function _consolidar_proyectos($yearpoa)
  {      
    $query=$this->Proyectos->listar_proyectos(' where id_estructura >1 ', $yearpoa);  
    
    if ($query->num_rows() === 0) // SI NO HAY PROYECTOS
    { 
      $tabla='<h2><center>El MPPRE No Posee Proyectos Registrados para el año indicado</center></h2>';      
      return $tabla;
    }  

    $proyectos=$query->result();
    
    // Campo oculto con el tipo de reporte
    $tabla='<input type="hidden" id="tipoReporte" value="1"/>';
    $tabla.='<h1>Reporte de Proyectos para el año '.$yearpoa.'</h1>';
    // CONSTRUIMOS LA TABLA
    // ENCABEZADOS DE TABLA
    $tabla.='<table class="TablaNivel1 Zebrado">';
    $tabla.='<thead>';
    $tabla.='<tr>'; 
    $tabla.='<th width="50px">';
    $tabla.='</th>'; 
    $tabla.='<th width="200px">';
    $tabla.='UNIDAD ADMINISTRATIVA';
    $tabla.='</th>';
    $tabla.='<th width="70px">';
    $tabla.='CODIGO';
    $tabla.='</th>';
    $tabla.='<th>';
    $tabla.='NOMBRE DEL PROYECTO';
    $tabla.='</th>';
    $tabla.='<th style="text-align:right!important; width:120px;">';
    $tabla.='APROBADO (Bs.)';
    $tabla.='</th>';
    $tabla.='<th style="text-align:right!important; width:120px; padding-right:5px">';
    $tabla.='PLANIFICADO (Bs.)';    
    $tabla.='</th>';      
    $tabla.='</tr>';
    $tabla.='</thead>';
    $tabla.='<tbody>';
    $aprobado=0;
    $planificado=0;
      
    foreach ($proyectos as $p)
    { 
      $r=$this->comunes->analisis_proyecto($p->monto_aprobado, $p->total, $p->estatus); 
   
      $tabla.='<tr class="Resaltado" onclick="javascript:menuReportePlan('.$p->id_proyecto.');">';
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
      $tabla.=number_format($p->monto_aprobado, 2, ',','.');
      $tabla.='</td>';
      $tabla.='<td style="text-align:right!important; ">';
      $tabla.=number_format($p->total, 2, ',','.');
      $tabla.='</td>';        
      $tabla.='</tr>';
      $aprobado+=$p->monto_aprobado;
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
    $tabla.=number_format($aprobado, 2, ',', '.'); 
    $tabla.='</td>';    
    $tabla.='<td style="text-align:right!important;">'; 
    $tabla.=number_format($planificado, 2, ',', '.'); 
    $tabla.='</td>';         
    $tabla.='</tr>';
    $tabla.='</tfoot>';
    $tabla.='</table><br/><br/>';   
    return $tabla;
  }
  
  function _consolidar_presupuesto($yearpoa)
  {      
    $query=$this->Proyectos->obtener_presupuesto_yearpoa_x_partidas($yearpoa);
    
    if ($query->num_rows() === 0) // SI NO HAY PRESUPUESTO
    { 
      $tabla='<h2><center>El MPPRE No Posee Proyectos Planificados para el año indicado</center></h2>';      
      return $tabla;
    }  

    $presupuesto=$query->result();
    
    // CONSTRUIMOS LA TABLA     
   
    $tabla='<h1>Presupuesto de Proyectos para el año '.$yearpoa.'</h1>';

    // ENCABEZADOS DE TABLA       
    $tabla.='<table class="TablaNivel1">';
    $tabla.='<thead>';
    $tabla.='<tr>';       
    $tabla.='<th width="120px" >';
    $tabla.='PARTIDA';
    $tabla.='</th>';
    $tabla.='<th>';
    $tabla.='DENOMINACIÓN';
    $tabla.='</th>';
    $tabla.='<th width="200px" style="text-align:right">';
    $tabla.='MONTO (Bs.)';   
    $tabla.='</th>';
    $tabla.='<th width="30px">';
    $tabla.='</th>';     
    $tabla.='</tr>';
    $tabla.='</thead>';
    $tabla.='<tbody>';
        
    foreach ($presupuesto as $p)
    {  
       if ($p->id_partida == 'TOTAL')
       {   // PIE DE TABLA CON TOTALES
           $tabla.='</tbody>';
           $tabla.='<tfoot>';
           $tabla.='<tr>';
           $tabla.='<td>';
           $tabla.='</td>';
           $tabla.='<td style="text-align: right; text-">';
           $tabla.='PRESUPUESTO TOTAL (Bs.)';
           $tabla.='</td>';
           $tabla.='<td style="text-align: right">';
           $tabla.=number_format($p->monto, 2,',', '.');
           $tabla.='</td>';
           $tabla.='<td>';
           $tabla.='</td>'; 
           $tabla.='</tr>';
           $tabla.='</tfoot>';           
       }
       else
       {   // DETERMINAMOS EL TIPO DE PARTIDA
           $partida='';           
           if (substr($p->id_partida, -2) != '00' ) $partida='pSubEspecifica';
           else $partida='pEspecifica';
           if (substr($p->id_partida, -5) == '00.00') $partida='pGenerica';
           if (substr($p->id_partida, -8) == '00.00.00') $partida='pGeneral';
    
           $tabla.='<tr class="Resaltado '.$partida.'">';      
           $tabla.='<td>';
           $tabla.=$p->id_partida;
           $tabla.='</td>';
           $tabla.='<td style="text-align:left">';
           $tabla.=$p->denominacion;
           $tabla.='</td>';
           $tabla.='<td style="text-align:right">';
           $tabla.=number_format($p->monto, 2,',', '.');
           $tabla.='</td>';
           $tabla.='<td>';
           $tabla.='</td>';       
           $tabla.='</tr>';            
       }
    }    
    $tabla.='</table><br/><br/>';
     
    return $tabla;
  }      
  
  function presupuestoProyecto()
  {      
     if (!$this->input->is_ajax_request()) die('Acceso Denegado'); 
     
     $id_proyecto= intval($this->input->post('id_proyecto'));

     $p = $this->Proyectos->obtener_proyecto($id_proyecto);     
     if (!$p)die('ERROR. PROYECTO NO EXISTE');
          
     // Campo oculto con el tipo de Reporte
     $tabla='<input type="hidden" id="tipoReporte" value="2"/>';
     // CONSTRUIMOS LA TABLA
     $tabla.='<h1>Presupuesto por Partidas de Proyecto</h1>';
     $tabla.='<table style="font-size:1.2em">';
     $tabla.='<tr>';
     $tabla.='<td>';     
     $tabla.='<img class="BotonIco" src="'.base_url().'imagenes/back.png" ';
     $tabla.=' onclick="actualiza();" ';
     $tabla.=' title="clic para regresar al listado de Proyectos" ';
     $tabla.='/>&nbsp;';
     $tabla.='</td>';
     $tabla.='<td style="text-align:right; vertical-align:top"> UNIDAD:&nbsp; </td>';
     $tabla.='<td style="text-align:right; padding-right:5px; vertical-align:top"> '.
                    $p->codigo.'</td>';
     $tabla.='<td colspan="3">'.$p->descripcion.'<br/>'.$p->superior.'</td>';     
     $tabla.='</tr>';
     $tabla.='<tr>';
     $tabla.='<td colspan="2" style="text-align:right; font-weight:bold; vertical-align:top">';
     $tabla.='PROYECTO:&nbsp; </td>';
     $tabla.='<td style="text-align:right;font-weight:bold; padding-right:5px; vertical-align:top">'.
             $p->cod_proy.'</td>';
     $tabla.='<td style="font-weight:bold" colspan="3"> '.$p->obj_esp.'</td>';
     $tabla.='</tr>';
     $tabla.='</table><br/>';
   
     $query=$this->Proyectos->obtener_presupuesto_proyecto_x_partidas($id_proyecto);
    
     if ($query->num_rows() === 0) // SI NO HAY PRESUPUESTO
     { 
       $tabla.='<h2><center>Proyecto sin Programación Presupuestaria</center></h2>';      
       die($tabla);
     }       
     
     $presupuesto=$query->result();
     
     // ENCABEZADOS DE TABLA       
     $tabla.='<table class="TablaNivel1">';
     $tabla.='<thead>';
     $tabla.='<tr>';       
     $tabla.='<th width="120px" >';
     $tabla.='PARTIDA';
     $tabla.='</th>';
     $tabla.='<th colspan="3">';
     $tabla.='DENOMINACIÓN';
     $tabla.='</th>';
     $tabla.='<th width="200px" style="text-align:right" >';
     $tabla.='MONTO (Bs.)';   
     $tabla.='</th>';
     $tabla.='<th width="30px">';
     $tabla.='</th>';     
     $tabla.='</tr>';
     $tabla.='</thead>';
     $tabla.='<tbody>';
        
     foreach ($presupuesto as $p)
     {  
       if ($p->id_partida == 'TOTAL')
       {   // PIE DE TABLA CON TOTALES
           $tabla.='</tbody>';
           $tabla.='<tfoot>';
           $tabla.='<tr>';
           $tabla.='<td>';
           $tabla.='</td>';
           $tabla.='<td style="text-align: right;" colspan="3">';
           $tabla.='PRESUPUESTO TOTAL (Bs.)';
           $tabla.='</td>';
           $tabla.='<td style="text-align: right" >';
           $tabla.=number_format($p->monto, 2,',', '.');
           $tabla.='</td>';
           $tabla.='<td>';
           $tabla.='</td>'; 
           $tabla.='</tr>';
           $tabla.='</tfoot>';           
       }
       else
       {   // DETERMINAMOS EL TIPO DE PARTIDA
           $partida='';           
           if (substr($p->id_partida, -2) != '00' ) $partida='pSubEspecifica';
           else $partida='pEspecifica';
           if (substr($p->id_partida, -5) == '00.00') $partida='pGenerica';
           if (substr($p->id_partida, -8) == '00.00.00') $partida='pGeneral';
    
           $tabla.='<tr class="Resaltado '.$partida.'">';      
           $tabla.='<td>';
           $tabla.=$p->id_partida;
           $tabla.='</td>';
           $tabla.='<td style="text-align:left" colspan="3">';
           $tabla.=$p->denominacion;
           $tabla.='</td>';
           $tabla.='<td style="text-align:right" >';
           $tabla.=number_format($p->monto, 2,',', '.');
           $tabla.='</td>';
           $tabla.='<td>';
           $tabla.='</td>';       
           $tabla.='</tr>';            
       }
    }    
    $tabla.='</table><br/><br/>';
     
    die($tabla);
  }  
  
  function metasActividadesProyecto()
  {      
     if (!$this->input->is_ajax_request()) die('Acceso Denegado'); 
     
     $id_proyecto= intval($this->input->post('id_proyecto'));
     
     $p = $this->Proyectos->obtener_proyecto($id_proyecto);     
     if (!$p)die('ERROR. PROYECTO NO EXISTE');
        
     // Campo oculto con el tipo de Reporte
     $tabla='<input type="hidden" id="tipoReporte" value="3"/>';
     // CONSTRUIMOS LA TABLA
     $tabla.='<h1>Metas Físicas y Financieras de Actividades de Proyecto</h1>';
     $tabla.='<table style="font-size:1.2em">';
     $tabla.='<tr>';
     $tabla.='<td>';     
     $tabla.='<img class="BotonIco" src="'.base_url().'imagenes/back.png" ';
     $tabla.=' onclick="actualiza();" ';
     $tabla.=' title="clic para regresar al listado de Proyectos" ';
     $tabla.='/>&nbsp;';
     $tabla.='</td>';
     $tabla.='<td style="text-align:right; vertical-align:top"> UNIDAD:&nbsp; </td>';
     $tabla.='<td style="text-align:right; padding-right:5px; vertical-align:top"> '.
             $p->codigo.'</td>';
     $tabla.='<td colspan="13">'.$p->descripcion.'<br/>'.$p->superior.'</td>';     
     $tabla.='</tr>';
     $tabla.='<tr>';
     $tabla.='<td colspan="2" style="text-align:right; font-weight:bold ">';
     $tabla.='PROYECTO:&nbsp; </td>';
     $tabla.='<td style="text-align:right;font-weight:bold; padding-right:5px"">'.
             $p->cod_proy.'</td>';
     $tabla.='<td style="font-weight:bold"  colspan="13"> '.$p->obj_esp.'</td>';
     $tabla.='</tr>';
     $tabla.='</table><br/>';

     $query=$this->Proyectos->obtener_metas_actividades_proyecto($id_proyecto);
    
     if ($query->num_rows() === 0) // SI NO HAY DATOS
     { 
       $tabla.='<h2><center>Proyecto sin Programación de Actividades</center></h2>';      
       die($tabla);
     }            
     $datos=$query->result();
     
     // ENCABEZADOS DE TABLA     
     $tabla.='<table class="TablaNivel1" style="font-size:.8em; max-width:1200px;" >';
     $tabla.='<thead>';
     $tabla.='<tr>';       
     $tabla.='<th style="width:30px;" >';     
     $tabla.='</th>';
     $tabla.='<th style="width:120px; text-align:left">';
     $tabla.='ACTIVIDAD';
     $tabla.='</th>';
     $tabla.='<th style="width:50px; text-align:left" >';     
     $tabla.='UNIDAD MEDIDA';
     $tabla.='</th>';
     $tabla.='<th style="width:80px; text-align:right" >';     
     $tabla.='ENE';
     $tabla.='</th>';
     $tabla.='<th style="width:80px; text-align:right" >';
     $tabla.='FEB';
     $tabla.='</th>';          
     $tabla.='<th style="width:80px; text-align:right" >';     
     $tabla.='MAR';
     $tabla.='</th>';          
     $tabla.='<th style="width:80px; text-align:right" >';     
     $tabla.='ABR';     
     $tabla.='</th>';
     $tabla.='<th style="width:80px; text-align:right" >';     
     $tabla.='MAY';     
     $tabla.='</th>';     
     $tabla.='<th style="width:80px; text-align:right" >';     
     $tabla.='JUN';     
     $tabla.='</th>';     
     $tabla.='<th style="width:80px; text-align:right" >';     
     $tabla.='JUL';     
     $tabla.='</th>';     
     $tabla.='<th style="width:80px; text-align:right" >';     
     $tabla.='AGO';     
     $tabla.='</th>';     
     $tabla.='<th style="width:80px; text-align:right" >';     
     $tabla.='SEP';     
     $tabla.='</th>';     
     $tabla.='<th style="width:80px; text-align:right" >';     
     $tabla.='OCT';     
     $tabla.='</th>';     
     $tabla.='<th style="width:80px; text-align:right" >';     
     $tabla.='NOV';     
     $tabla.='</th>';     
     $tabla.='<th style="width:80px; text-align:right" >';     
     $tabla.='DIC';     
     $tabla.='</th>';     
     $tabla.='<th style="width:80px; text-align:right" >';     
     $tabla.='ANUAL';     
     $tabla.='</th>';     
     $tabla.='</tr>';
     $tabla.='</thead>';
     $tabla.='<tbody >';
        
     foreach ($datos as $p)
     {  
       if ($p->cod_act == 'TOTAL')
       {   // PIE DE TABLA CON TOTALES
           $tabla.='</tbody>';
           $tabla.='<tfoot>';
           $tabla.='<tr>';
           $tabla.='<td colspan="2" style="vertical-align:top">';
           $tabla.='T O T A L E S';
           $tabla.='</td>';
           $tabla.='<td>';
           $tabla.='Metas <br/> Bs.';
           $tabla.='</td>';           
           $tabla.='<td style="text-align: right">';
           $tabla.=number_format($p->ene1, 0);
           $tabla.='<br/>'.number_format($p->ene, 2,',','.');;           
           $tabla.='</td>';           
           $tabla.='<td style="text-align: right">';
           $tabla.=number_format($p->feb1, 0);
           $tabla.='<br/>'.number_format($p->feb, 2,',','.');;           
           $tabla.='</td>';           
           $tabla.='<td style="text-align: right">';
           $tabla.=number_format($p->mar1, 0);
           $tabla.='<br/>'.number_format($p->mar, 2,',','.');;           
           $tabla.='</td>';                     
           $tabla.='<td style="text-align: right">';
           $tabla.=number_format($p->abr1, 0);
           $tabla.='<br/>'.number_format($p->abr, 2,',','.');;           
           $tabla.='</td>';                     
           $tabla.='<td style="text-align: right">';
           $tabla.=number_format($p->may1, 0);
           $tabla.='<br/>'.number_format($p->may, 2,',','.');;           
           $tabla.='</td>';                     
           $tabla.='<td style="text-align: right">';
           $tabla.=number_format($p->jun1, 0);
           $tabla.='<br/>'.number_format($p->jun, 2,',','.');;           
           $tabla.='</td>';                     
           $tabla.='<td style="text-align: right">';
           $tabla.=number_format($p->jul1, 0);
           $tabla.='<br/>'.number_format($p->jul, 2,',','.');;           
           $tabla.='</td>';                     
           $tabla.='<td style="text-align: right">';
           $tabla.=number_format($p->ago1, 0);
           $tabla.='<br/>'.number_format($p->ago, 2,',','.');;           
           $tabla.='</td>';                     
           $tabla.='<td style="text-align: right">';
           $tabla.=number_format($p->sep1, 0);
           $tabla.='<br/>'.number_format($p->sep, 2,',','.');;           
           $tabla.='</td>';           
           $tabla.='<td style="text-align: right">';
           $tabla.=number_format($p->oct1, 0);
           $tabla.='<br/>'.number_format($p->oct, 2,',','.');;           
           $tabla.='</td>';                     
           $tabla.='<td style="text-align: right">';
           $tabla.=number_format($p->nov1, 0);
           $tabla.='<br/>'.number_format($p->nov, 2,',','.');;           
           $tabla.='</td>';                     
           $tabla.='<td style="text-align: right">';
           $tabla.=number_format($p->dic1, 0);
           $tabla.='<br/>'.number_format($p->dic, 2,',','.');;           
           $tabla.='</td>';                     
           $tabla.='<td style="text-align: right">';
           $tabla.=number_format($p->total1, 0);
           $tabla.='<br/>'.number_format($p->total, 2,',','.');;           
           $tabla.='</td>';                      
           $tabla.='</tr>';
           $tabla.='</tfoot>';           
       }
       else
       {      
           $tabla.='<tr class="Resaltado">';      
           $tabla.='<td rowspan="2" style="padding-right:0px; vertical-align:top">';
           $tabla.=$p->cod_act;
           $tabla.='</td>';
           $tabla.='<td rowspan="2" style="text-align:left; vertical-align:top">';
           $tabla.=$p->actividad;
           $tabla.='</td>';
           $tabla.='<td style="text-align:left">';
           $tabla.=$p->um;
           $tabla.='</td>';
           $tabla.='<td style="text-align:right; padding-left:1px">';
           $tabla.=number_format($p->ene1, 0);
           $tabla.='</td>';            
           $tabla.='<td style="text-align:right; padding-left:1px">';
           $tabla.=number_format($p->feb1, 0);
           $tabla.='</td>';             
           $tabla.='<td style="text-align:right; padding-left:1px">';
           $tabla.=number_format($p->mar1, 0);
           $tabla.='</td>';             
           $tabla.='<td style="text-align:right; padding-left:1px">';
           $tabla.=number_format($p->abr1, 0);
           $tabla.='</td>';             
           $tabla.='<td style="text-align:right; padding-left:1px">';
           $tabla.=number_format($p->may1, 0);
           $tabla.='</td>';             
           $tabla.='<td style="text-align:right; padding-left:1px">';
           $tabla.=number_format($p->jun1, 0);
           $tabla.='</td>';             
           $tabla.='<td style="text-align:right; padding-left:1px">';
           $tabla.=number_format($p->jul1, 0);
           $tabla.='</td>';             
           $tabla.='<td style="text-align:right; padding-left:1px">';
           $tabla.=number_format($p->ago1, 0);
           $tabla.='</td>';             
           $tabla.='<td style="text-align:right; padding-left:1px">';
           $tabla.=number_format($p->sep1, 0);
           $tabla.='</td>';             
           $tabla.='<td style="text-align:right; padding-left:1px">';
           $tabla.=number_format($p->oct1, 0);
           $tabla.='</td>';             
           $tabla.='<td style="text-align:right; padding-left:1px">';
           $tabla.=number_format($p->nov1, 0);
           $tabla.='</td>';             
           $tabla.='<td style="text-align:right; padding-left:1px">';
           $tabla.=number_format($p->dic1, 0);
           $tabla.='</td>';             
           $tabla.='<td style="text-align:right; padding-left:1px">';
           $tabla.=number_format($p->total1, 0);
           $tabla.='</td>';
           $tabla.='</tr>';
           $tabla.='<tr>';
           $tabla.='<td>';
           $tabla.='Bs.';
           $tabla.='</td>';               
           $tabla.='<td style="text-align:right; padding-left:1px">';
           $tabla.=number_format($p->ene, 2,',', '.');
           $tabla.='</td>';
           $tabla.='<td style="text-align:right; padding-left:1px">';
           $tabla.=number_format($p->feb, 2,',', '.');
           $tabla.='</td>';
           $tabla.='<td style="text-align:right; padding-left:1px">';
           $tabla.=number_format($p->mar, 2,',', '.');
           $tabla.='</td>';
           $tabla.='<td style="text-align:right; padding-left:1px">';
           $tabla.=number_format($p->abr, 2,',', '.');
           $tabla.='</td>';
           $tabla.='<td style="text-align:right; padding-left:1px">';
           $tabla.=number_format($p->may, 2,',', '.');
           $tabla.='</td>';
           $tabla.='<td style="text-align:right; padding-left:1px">';
           $tabla.=number_format($p->jun, 2,',', '.');
           $tabla.='</td>';
           $tabla.='<td style="text-align:right; padding-left:1px">';
           $tabla.=number_format($p->jul, 2,',', '.');
           $tabla.='</td>';
           $tabla.='<td style="text-align:right; padding-left:1px">';
           $tabla.=number_format($p->ago, 2,',', '.');
           $tabla.='</td>';
           $tabla.='<td style="text-align:right; padding-left:1px">';
           $tabla.=number_format($p->sep, 2,',', '.');
           $tabla.='</td>';
           $tabla.='<td style="text-align:right; padding-left:1px">';
           $tabla.=number_format($p->oct, 2,',', '.');
           $tabla.='</td>';
           $tabla.='<td style="text-align:right; padding-left:1px">';
           $tabla.=number_format($p->nov, 2,',', '.');
           $tabla.='</td>';
           $tabla.='<td style="text-align:right; padding-left:1px">';
           $tabla.=number_format($p->dic, 2,',', '.');
           $tabla.='</td>';           
           $tabla.='<td style="text-align:right; padding-left:1px">';
           $tabla.=number_format($p->total, 2,',', '.');
           $tabla.='</td>';       
           $tabla.='</tr>'; 
       }
    }    
    $tabla.='</table><br/><br/>';
    
    die($tabla);
  }    
  
  function menuReportePlan()
  {
    if (!$this->input->is_ajax_request()) die('Acceso Denegado');//Si la peticion NO vino por AJAX
     
    $id_proyecto = $this->input->post('id_proyecto');
    
    $proyecto = $this->Proyectos->obtener_proyecto($id_proyecto);
    if (!$proyecto) die('ERROR: PROYECTO NO EXISTE');
    
    $data['p'] = $proyecto;   
    
    $this->load->view('reportes/menuReporteProyPlan',$data);      
  }
  
}