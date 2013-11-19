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
class Ejecucion_proyectos extends CI_Controller {
  function __construct() 
  {
     parent::__construct();
     $this->load->helper('form');
     $this->load->library('Comunes');
  //   $this->load->model('Usuarios');
     $this->load->model('Estructura');
     $this->load->model('Proyectos');
     $this->load->model('Crud');     
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
    $data['titulo']='Ejecución de Proyectos';
    $data['subtitulo']='Registro de Ejecución Presupuestaria del Año:';
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
    
    $data['contenido']='ejecucion_proyectos/ejecucion_proyectos';    
    $data['script']='<!-- Cargamos CSS de DataTables -->'."\n";    
    $data['script'].="\t".'<link rel="stylesheet" type="text/css" media="all" href="'.base_url().'css/dataTables.css"/>'."\n";
    $data['script'].='<!-- Cargamos JS para DataTables -->'."\n";
    $data['script'].="\t".'<script type="text/javascript" src="'.base_url().'js/jquery.dataTables.js"></script>'."\n";
    $data['script'].='<!-- Cargamos Nuestro JS -->'."\n";
    $data['script'].="\t".'<script type="text/javascript" src="'.base_url().'js/ejecucion_proyectos.js"></script>'."\n";
       
     $data['tabla']=$this->listar_proyectos($year_poa);
    
 // CARGAMOS LA VISTA   
    $this->load->view('plantillas/plantilla_general',$data);  
  }  
  
  //////////////////////////////////////////////////////////////////////////////////////////
  
  function listar_proyectos($yearpoa=0)
  {
    if ($this->input->is_ajax_request()) $yearpoa=$this->input->post('yearpoa'); // Si la peticion vino por AJAX
    date_default_timezone_set('America/Caracas');
    $id_estructura=$this->session->userdata('id_estructura');
    $estructura=$this->Estructura->obtener_estructura($id_estructura);
    if (!$estructura)die('error consultando estructuras');
    $tope=strtotime($estructura['fecha_tope']);
    $ahora=time();
    $fecha=getdate(time());
    
    $consulta=$this->Estructura->obtener_estructuras_inferiores($id_estructura);
    if (!$consulta)die('error consultando estructuras inferiores');
    $estructuras='where';
    foreach ($consulta as $fila)
    {
        $estructuras.= ' id_estructura='.$fila['id_estructura'].' or';
    }
    $estructuras=  substr($estructuras, 0, -3);
    unset($fila);    
    $proyectos=$this->Proyectos->listar_proyectos($estructuras,$yearpoa);  
    
    if ($proyectos->num_rows()==0) // SI NO HAY PROYECTOS EN LA UNIDAD
    { 
      $tabla='<h2><center>La Unidad No Posee Proyectos para el año indicado</center></h2>';
      
      if ($this->input->is_ajax_request()) die($tabla); // Si la peticion vino por AJAX
      else return $tabla;
    }  
    
    $proyectos=$proyectos->result_array();
    
    // CONSTRUIMOS LA TABLA 
    $id_estructura=$proyectos[0]['id_estructura']; // OBTENGO LA PRIMERA ESTRUCTURA
    $i=0;
    $n=count($proyectos);
    $tabla='';
    while($i<$n)
    {
        // ENCABEZADOS DE TABLA
        $tabla.='<table width="100%"><tr><td style="vertical-align:middle; text-align:left">';
        $tabla.='<h4>'.$proyectos[$i]['codigo'].' - '.$proyectos[$i]['descripcion'].'</h4>';
        $tabla.='</td>';        
        $tabla.='</tr></table>';
        $tabla.='<table class="TablaNivel1 Zebrado">';
        $tabla.='<thead>';
        $tabla.='<tr>';
        $tabla.='<th width="100px">';
        $tabla.='CODIGO';
        $tabla.='</th>';
        $tabla.='<th>';
        $tabla.='NOMBRE DEL PROYECTO';
        $tabla.='</th>';
        $tabla.='<th style="text-align:right; width:130px;">';
        $tabla.='PLANIFICADO (Bs.)';
        $tabla.='</th>';
        $tabla.='<th style="text-align:right; width:130px;">';
        $tabla.='EJECUTADO (Bs.)';
        $tabla.='</th>';                
        $tabla.='<th width="150px">';
        $tabla.='METAS FÍSICAS (%)'; 
        $tabla.='</th>';                
        $tabla.='</tr>';
        $tabla.='</thead>';
        $tabla.='<tbody>';
        while(($proyectos[$i]['id_estructura']==$id_estructura))
        {
           $tope=strtotime($proyectos[$i]['fecha_tope']);
           $ahora=time();
           $editable=0;
           if (($ahora<$tope) && ($yearpoa>=$fecha['year']))
           {
              $editable=1;
           }
           $tabla.='<tr class="Resaltado" onclick="revisarMetas('.
                            $proyectos[$i]['id_proyecto'].');" >';
           $tabla.='<td title="Código del Proyecto">';
           $tabla.=trim($proyectos[$i]['cod_proy']);
           $tabla.='</td>';            
           $tabla.='<td style="text-align:left!important" title="Objetivo Específico del Proyecto">';
           $tabla.=trim($proyectos[$i]['obj_esp']);
           $tabla.='</td>';            
           $tabla.='<td title="Presupuesto Planificado" style="text-align:right;">';
           $tabla.=number_format($proyectos[$i]['total'], 2, ',','.');
           $tabla.='</td>'; 
           $tabla.='<td title="Presupuesto Ejecutado" style="text-align:right;" >';
           $tabla.=number_format($proyectos[$i]['ejecutado'], 2, ',','.');
           $tabla.='</td>';                                 
           $tabla.='<td title="% de Cumplimiento de la Metas Físicas">';
           
           $mp=isset($proyectos[$i]['meta_planificada'])?$proyectos[$i]['meta_planificada']:1;
           $ma=isset($proyectos[$i]['meta_alcanzada'])?$proyectos[$i]['meta_alcanzada']:0;
           $meta=0;
           if ($mp!=0) $meta=$ma/$mp*100;
           
           $tabla.=number_format($meta, 2, ',','.');
           $tabla.='</td>';                 
           $tabla.='</tr>';        
           $i++;
           if ($i==$n) break;
        }
        //PIE DE TABLA
        $tabla.='</tbody>';
        $tabla.='<tfoot>';
        $tabla.='<tr>';
        $tabla.='<td>';
        $tabla.='CODIGO';
        $tabla.='</td>';
        $tabla.='<td>';
        $tabla.='NOMBRE DEL PROYECTO';
        $tabla.='</td>';
        $tabla.='<td style="text-align:right;">';
        $tabla.='PLANIFICADO (Bs.)';
        $tabla.='</td>';
        $tabla.='<td style="text-align:right;">';
        $tabla.='EJECUTADO (Bs.)';
        $tabla.='</td>';                
        $tabla.='<td>';
        $tabla.='METAS FÍSICAS (%)'; 
        $tabla.='</td>';        
        $tabla.='</tr>';
        $tabla.='</tfoot>';
        $tabla.='</table><br/><br/>';   
        if ($i==$n) break;
        $id_estructura=$proyectos[$i]['id_estructura'];                         
    }    
    
    if ($this->input->is_ajax_request()) die($tabla); // Si la peticion vino por AJAX
    else return $tabla;
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
      
      // IDENTIFICACION
      $tabla='<table width="100%"><tr><td style="vertical-align:middle; text-align:left">';
      $tabla.='<h4>'.$proy->codigo.' - '.$proy->descripcion.'</h4>';
      $tabla.='</td>';
      $tabla.='<td width="10%" style="vertical-align:middle; text-align:right;">';    
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='</table>';
      $tabla.='<table width="100%">';
      $tabla.='<tr>';
      $tabla.='<td width="30px" class="BotonIco">';
      $accion=' onclick="javascript:actualiza();" ';        
      $tabla.='<img src="'.base_url().'imagenes/back.png"'.$accion;
      $tabla.='title="clic para regresar"';
      $tabla.='/>'; 
      $tabla.='</td>';
      $tabla.='<td width="150px" style="vertical-align:top; text-align:right">';
      $tabla.='<strong>PROYECTO: '.$proy->cod_proy.'&nbsp;-&nbsp;</strong>';
      $tabla.='</td>';
      $tabla.='<td style="vertical-align:top; text-align:left; padding:0 10px 10px 0">';
      $tabla.=$proy->obj_esp;
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>';
      $tabla.='<td colspan="3" style="height:40px">';
      $tabla.='<h4>EJECUCIÓN PRESUPUESTARIA &nbsp;&nbsp;&nbsp;&nbsp;
                <img src="'.base_url().'imagenes/meta.png" '.'
                 onclick="javascript:revisarMetas('.$id_proyecto.')"
                 class="BotonIco botoncito" title="Clic para revisar las metas físicas"
                 /></h4>';
      $tabla.='</td>';
      $tabla.='</tr>';      
      $tabla.='</table>';
      
      // EN CASO DE NO HABER REGISTROS DE EJECUCION EN SIGESP
      if (count($ejecucion)==0) // NO HAY MOVIMIENTOS
      {
         $tabla.='<h2><center>Proyecto sin Ejecución Registrada en SIGESP</center></h2>';
         die($tabla);
      }        
      
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
        
        $tabla.='<tr class="Resaltado" onclick="revisarAsociacion('.'\''.$id.'\''.')">';
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
            $img='desconocido.png';
            $tabla.='<img src="'.base_url().'imagenes/'.$img.'" ';
            $tabla.='title="Sin Actividad Asociada"';
            $tabla.='/>';            
        }
        
        $tabla.='<div class="Oculto">';        
        $tabla.='<form id="'.$id.'" >';
        
        $tabla.='<input type="text" ';
        $tabla.='name="id_proyecto" ';
        $tabla.='value="'.$id_proyecto.'" ';
        $tabla.='/>';        
        
        $tabla.='<input type="text" ';
        $tabla.='name="id_partida" ';
        $tabla.='value="'.$id_partida.'" ';
        $tabla.='/>'; 
                
        foreach ($e as $clave=>$valor)
        {            
            $tabla.='<input type="text" ';
            $tabla.='name="'.$clave.'" ';
            $tabla.='value="'.(isset($e->codact)?$valor:utf8_encode($valor)).'" ';
            $tabla.='/>';
        }
        $tabla.='</form>';
        $tabla.='</div>';
        $tabla.='</td>';
        $tabla.='</tr>';
      }      
      $tabla.='</tbody>';
      $tabla.='</table>';  
      
      die ($tabla);
  }

  function revisarMetas()
  {
      if (!$this->input->is_ajax_request()) die('Acceso Denegado');
      date_default_timezone_set('America/Caracas'); // Establece la Hora de Venezuela para funciones de fecha y hora
      
      $id_proyecto = intval($this->input->post('id_proyecto'));
            
      $proy = $this->Proyectos->obtener_proyecto($id_proyecto);
      if (!$proy)die('error consultando proyecto');
      
     
      // IDENTIFICACION
      $tabla='<table width="100%"><tr><td style="vertical-align:middle; text-align:left">';
      $tabla.='<h4>'.$proy->codigo.' - '.$proy->descripcion.'</h4>';
      $tabla.='</td>';
      $tabla.='<td width="10%" style="vertical-align:middle; text-align:right;">';    
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='</table>';
      $tabla.='<table width="100%">';
      $tabla.='<tr>';
      $tabla.='<td width="30px" class="BotonIco">';
      $accion=' onclick="javascript:actualiza();" ';        
      $tabla.='<img src="'.base_url().'imagenes/back.png"'.$accion;
      $tabla.='title="clic para regresar"';
      $tabla.='/>'; 
      $tabla.='</td>';
      $tabla.='<td width="150px" style="vertical-align:top; text-align:right">';
      $tabla.='<strong>PROYECTO: '.$proy->cod_proy.'&nbsp;-&nbsp;</strong>';
      $tabla.='</td>';
      $tabla.='<td style="vertical-align:top; text-align:left; padding:0 10px 10px 0">';
      $tabla.=$proy->obj_esp;
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>';
      $tabla.='<td colspan="3" style="height:40px">';
      $tabla.='<h4>EJECUCIÓN DE METAS FÍSICAS &nbsp;&nbsp;&nbsp;&nbsp;
                <img src="'.base_url().'imagenes/money.png" '.'
                 onclick="javascript:revisarEjecucion('.$id_proyecto.')"
                 class="BotonIco botoncito" title="Clic para revisar Ejecución Presupuestaria"
                 /></h4>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='</table>';

  // PLANIFICACION DE ACTIVIDADES   
    $plan=$this->Proyectos->gantt_proyecto($id_proyecto);
    if (!$plan)
    {
       $tabla.='<center><h2>- Proyecto sin Actividades Programadas -</h2></center>';
       return $tabla;
    }
    
    $tabla.='<table class="TablaNivel1 Zebrado">';
    $tabla.=$this->comunes->cabecetabla();
    $tabla.='<tbody>';
    
    $ae=$plan[0]->codigo_ae; //OBTENGO LA PRIMERA ACCION ESPECIFICA    
    $titulo=' title="Acción Específica" ';
    $i=0;
    $n=count($plan);
           
    while($i<$n)
    {
      $titulo=' title="Acción Específica Nº '.$plan[$i]->codigo_ae.'" ';  
      $ae=$plan[$i]->codigo_ae;
      $tabla.='<tr>';
      $tabla.='<td>';
      $tabla.='<strong>';
      $tabla.=$plan[$i]->codigo_ae;
      $tabla.='</strong>';
      $tabla.='</td>';
      $tabla.='<td colspan="2" style="text-align:left!important" '.$titulo.'>';
      $tabla.='<strong>';
      $tabla.=$plan[$i]->descripcion_ae;
      $tabla.='</strong>';
      $tabla.='</td>'; 
      $tabla.='<td colspan="12" class="Cuadricula">';      
      if ($plan[$i]->codigo_act=='')
      {
        $tabla.="*** Acción Específica sin Actividades Programadas ***";
      }      
      $tabla.='</td>';
      
      $tabla.='<td style="vertical-align:middle">';
   
      $tabla.='</td>';
     
      $tabla.='</tr>';      
      
      while ($ae==$plan[$i]->codigo_ae)
      {
         $tabla.='<tr>';
         $tabla.='<td>';
         $tabla.='</td>';
         
         if ($plan[$i]->codigo_act!='')
         {
           $titulo=' title="Actividad Nº '.$plan[$i]->codigo_ae.'.'.$plan[$i]->codigo_act.'" '; 
           $duracion=$this->comunes->diferenciaEntreFechas($plan[$i]->fecha_ini, $plan[$i]->fecha_fin, 'DIAS', true);
           
           $tabla.='<td width="20px">';
           $tabla.=$plan[$i]->codigo_ae.'.'.$plan[$i]->codigo_act;
           $tabla.='</td>';
           $tabla.='<td style="text-align:left!important"'.$titulo.'>';         
           $tabla.='<strong>';
           $tabla.=$plan[$i]->descripcion_act;
           $tabla.='</strong>';
           $tabla.='<div>';
           
           $metaAlcanzada=isset($plan[$i]->cantidad_meta)?$plan[$i]->cantidad_meta:'0';
           if ($metaAlcanzada<$plan[$i]->cantidad_act) $color='red';
           elseif ($metaAlcanzada>$plan[$i]->cantidad_act) $color='blue';
           else $color='black';
           
           $tabla.='Meta Física: ';
           $tabla.='<span title="Meta Alcanzada" style="color:'.$color.'">';
           $tabla.=$metaAlcanzada;
           $tabla.='</span>';
           $tabla.='&nbsp;/&nbsp;';
           $tabla.='<span title="Meta Planificada">';
           $tabla.=$plan[$i]->cantidad_act;
           $tabla.='</span>';
           $tabla.='<span title="Unidad de Medida" >';
           $tabla.='&nbsp;'.$plan[$i]->um_act;;
           $tabla.='</span>';          
           $tabla.='</div>';
           $tabla.='</td>';
           
           $gantt=$this->comunes->mini_gantt($plan[$i]->fecha_ini, $plan[$i]->fecha_fin, 40);
           $titulo=' title="Desde: ';
           $titulo.=date("d/m/Y",strtotime($plan[$i]->fecha_ini));
           $titulo.=' Hasta: ';
           $titulo.=date("d/m/Y",strtotime($plan[$i]->fecha_fin));
           $titulo.='" ';
           
           $tabla.='<td colspan="12" class="Cuadricula" '.$titulo.' >';           
           $tabla.='<div class="'.(($plan[$i]->total>0)?'gantt'.$plan[$i]->cod_fuente:'ganttPobre').'">'.$gantt.'</div>';           
           $tabla.='</td>';
           
           $tabla.='<td style="vertical-align:middle">';  
           $tabla.='<img src="'.base_url().'imagenes/meta.png" '.'
                     style="width:20px"
                     onclick="javascript:registrarMeta('.$plan[$i]->id_actividad.')"
                     class="BotonIco botoncito" title="Clic para Registrar metas físicas"
                     />';
           $tabla.='</td>';          
         }
         else
         {
           $tabla.='<td colspan="2">';            
           $tabla.='</td>';
           $tabla.='<td colspan="12" class="Cuadricula">';
           
           $tabla.='</td>'; 
           $tabla.='<td>';
           $tabla.='</td>';
         }         

         $tabla.='</tr>';
         $i++;
         if ($i==$n) break;      
      }      
    }
       
    $tabla.='</tbody>'; 
    $tabla.='</table>';   
    die($tabla);
  }
  
  function revisar_asociacion()
  {
      if (!$this->input->is_ajax_request()) die('Acceso Denegado');
      
      $data = $this->input->post();
      $actividades=$this->_getArrayActividades($data['id_proyecto']);
      
      $tabla='<div class="EntraDatos">';
      $tabla.='<table>';
      $tabla.='<thead>';
      $tabla.='<tr><th colspan="2">';            
      $tabla.='Asociación de Ejecución con Actividad';  
      $tabla.='</th></tr>';           
      $tabla.='</thead>';            
      $tabla.='<tbody>';
      $tabla.='<tr>';
      $tabla.='<td colspan="2">';
      $tabla.='<label>Registro de Ejecución:</label>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>';
      $tabla.='<td style="width:150px; vertical-align:top;
                          text-align:right; padding:2px"><i>Documento: </i></td>';
      $tabla.='<td style="vertical-align:top; 
                          padding:2px 25px 2px 5px">'.$data['documento'].'</td>';
      $tabla.='</tr>';
      $tabla.='<tr>';
      $tabla.='<td style="width:150px; vertical-align:top;
                          text-align:right; padding:2px"><i>Descripción: </i></td>';
      $tabla.='<td style="vertical-align:top; 
                          padding:2px 25px 2px 5px">'.$data['nombre_prog'].'</td>';
      $tabla.='</tr>';      
      $tabla.='<tr>';
      $tabla.='<td style="width:150px; vertical-align:top;
                          text-align:right; padding:2px"><i>Proveedor: </i></td>';
      $tabla.='<td style="vertical-align:top; 
                          padding:2px 25px 2px 5px">'.$data['nompro'].'</td>';
      $tabla.='</tr>';        
      $tabla.='<tr>';
      $tabla.='<td style="width:150px; vertical-align:top;
                          text-align:right; padding:2px"><i>Partida: </i></td>';
      $tabla.='<td style="vertical-align:top; 
                          padding:2px 25px 2px 5px">'.$data['id_partida'].'</td>';
      $tabla.='</tr>';
      $tabla.='<tr>';
      $tabla.='<td style="width:150px; vertical-align:top;
                          text-align:right; padding:2px"><i>Fecha: </i></td>';
      $tabla.='<td style="vertical-align:top; 
                          padding:2px 25px 2px 5px">'.
                          date("d/m/Y",strtotime($data['fecha'])).'</td>';
      $tabla.='</tr>';      
      $tabla.='<tr>';
      $tabla.='<td style="width:150px; vertical-align:top;
                          text-align:right; padding:2px"><i>Monto(Bs.) :</i></td>';
      $tabla.='<td style="vertical-align:top; 
                          padding:2px 25px 2px 5px">'.
                          number_format($data['compromiso'], 2, ',','.').'</td>';
      $tabla.='</tr>';       
      
      $tabla.='<tr>';
      $tabla.='<td colspan="2">';
      $tabla.='<form id="asociacion">';
      $tabla.='<label>Actividad Planificada Asociada al registro de Ejecución:</label>';    
      $tabla.='<select class="Campos" id="nva_actividad"
                       name="nva_actividad" title="Seleccione la Actividad de la lista" >';
      
      $tabla.=$this->comunes->construye_opciones($actividades, 
                                  (isset($data['id_actividad']))?$data['id_actividad']:NULL);
      $tabla.='</select>';  
      
      $tabla.='<div class="Oculto">';  
      foreach ($data as $clave=>$valor)
      {            
        $tabla.='<input type="text" ';
        $tabla.='name="'.$clave.'" ';
        $tabla.='value="'.$valor.'" ';
        $tabla.='/>';
      }
      $tabla.='</div>';
      $tabla.='</form>';
      $tabla.='</td>';
      $tabla.='</tr>';      
      $tabla.='</tbody>';      
      $tabla.='<tfoot>';
      $tabla.='<tr><td colspan="2">';      
      
      // Si no estamos en el año en curso, no puede hacer cambios en la ejecución
      date_default_timezone_set('America/Caracas'); // Establece la Hora de Venezuela para funciones de fecha y hora
      $ahora=  getdate(time()); // año en curso      
      $yearEjec=date("Y",strtotime($data['fecha']));
      
      if ($yearEjec==$ahora['year'])        
      {
        $tabla.='<div class="BotonIco" onclick="javascript:asociarActividad()" title="Guardar Cambios">';
        $tabla.='<img src="imagenes/guardar32.png"/>&nbsp;';   
        $tabla.='Guardar';
        $tabla.= '</div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';    
      }      
      $tabla.='<div class="BotonIco" onclick="javascript:CancelarModal()" title="Cerrar">';
      $tabla.='<img src="imagenes/cancel.png"/>&nbsp;';
      $tabla.='Cerrar';
      $tabla.= '</div>';
      $tabla.='</td></tr>';
      $tabla.='</tfoot>';
      $tabla.='</table>';   
      $tabla.='</div>';
        
      die ($tabla);
  }  

  function registrarMeta()
  {
      if (!$this->input->is_ajax_request()) die('Acceso Denegado');
      
      $id_actividad = $this->input->post('id_actividad');
      $actividad = $this->Proyectos->obtener_actividad($id_actividad);
      if (!$actividad )die('Error. Actividad no Existe');
          
      $tabla='<div class="EntraDatos">';
      $tabla.='<table>';
      $tabla.='<thead>';
      $tabla.='<tr><th colspan="2">';
      $tabla.='Ejecución de Metas Físicas';  
      $tabla.='</th></tr>';           
      $tabla.='</thead>';            
      $tabla.='<tbody>';
      $tabla.='<tr>';
      $tabla.='<td colspan="2">';
      $tabla.='<label>Detalles de la Actividad:</label>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>';
      $tabla.='<td style="width:200px; vertical-align:top;
                          text-align:right; padding:2px"><i>Descripción: </i>';
      $tabla.='</td>';
      $tabla.='<td style="vertical-align:top; padding:2px 25px 2px 5px">';
      $tabla.=$actividad->codigo_ae.'.'.$actividad->codigo_act.'&nbsp;'.
              $actividad->descripcion_act;
      $tabla.='</td>';
      $tabla.='</tr>';
      
      $tabla.='<tr>';
      $tabla.='<td style="vertical-align:top;
                          text-align:right; padding:2px"><i>Meta Física Planificada: </i>';
      $tabla.='</td>';
      $tabla.='<td style="vertical-align:top; padding:2px 25px 2px 5px">';
      $tabla.=$actividad->cantidad_act.'&nbsp;'.$actividad->um_act;
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>';
      $tabla.='<td colspan="2">';
      $tabla.='<label>Detalles de Ejecución:</label>';
      $tabla.='</td>';
      $tabla.='</tr>'; 
      $tabla.='<tr>';
      $tabla.='<td style="vertical-align:top;
                          text-align:right; padding:2px"><i>Meta Física Alcanzada: </i>';
      $tabla.='</td>';
      $tabla.='<td style="vertical-align:top; padding:2px 25px 2px 5px">';
      $tabla.='<input type="text" style="text-align:center" size="5" maxlength="10"
                      id="cantidadMeta" class="Editable"';
      $tabla.=' onblur="this.value=formatNumber(this.value,0);"';
      $tabla.=' onkeyup="formatNumber(this.value,0);" ';
      $tabla.=' onkeypress="return onlyDigits(event, this.value, false,false,false,\',\',\'.\',0);" ';      
      $tabla.='value="';
      $tabla.=isset($actividad->cantidad_meta)?$actividad->cantidad_meta:'';
      $tabla.='" />';
      $tabla.='&nbsp;'.$actividad->um_act;
      $tabla.='</td>';
      $tabla.='</tr>';      
      $tabla.='<tr>';
      $tabla.='<td style="vertical-align:top;
                          text-align:right; padding:2px"><i>Fecha: </i>';
      $tabla.='</td>';
      $tabla.='<td style="vertical-align:top; padding:2px 25px 2px 5px">';
      $tabla.='<input type="text" class="Fechas Editable" id="fechaMeta" 
                      title="Fecha de Cumplimiento de la Meta" value="';
      $tabla.=isset($actividad->fecha_meta)?date("d/m/Y",strtotime($actividad->fecha_meta)):'';
      $tabla.='" readonly="readonly"/>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>';
      $tabla.='<td style="vertical-align:top;
                          text-align:right; padding:2px"><i>Observaciones: </i>';
      $tabla.='</td>';
      $tabla.='<td style="vertical-align:top; padding:2px 25px 2px 5px">';
      $tabla.='<textarea class="CampoFicha Editable" id="observaciones" rows="2" >';      
      $tabla.=isset($actividad->observaciones)?$actividad->observaciones:'';
      $tabla.='</textarea>';     
      $tabla.='</td>';
      $tabla.='</tr>';       
      $tabla.='</tbody>';      
      $tabla.='<tfoot>';
      $tabla.='<tr><td colspan="2">';      
      
      // Si no estamos en el año en curso, no puede hacer cambios en la ejecución
      date_default_timezone_set('America/Caracas'); // Establece la Hora de Venezuela para funciones de fecha y hora
      $ahora=  getdate(time()); // año en curso      
      $yearEjec=date("Y",strtotime($actividad->fecha_ini));
      
      if ($yearEjec==$ahora['year'])        
      {
        $tabla.='<div class="BotonIco" onclick="javascript:guardarMeta('.
                      $id_actividad.','.$actividad->id_proyecto.')" 
                      title="Guardar Cambios">';
        $tabla.='<img src="imagenes/guardar32.png"/>&nbsp;';   
        $tabla.='Guardar';
        $tabla.= '</div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';    
      }      
      $tabla.='<div class="BotonIco" onclick="javascript:CancelarModal()" title="Cerrar">';
      $tabla.='<img src="imagenes/cancel.png"/>&nbsp;';
      $tabla.='Cerrar';
      $tabla.= '</div>';
      $tabla.='</td></tr>';
      $tabla.='</tfoot>';
      $tabla.='</table>';   
      $tabla.='</div>';
        
      die ($tabla);
  }  
  
  function asociarActividad()
  {
      if (!$this->input->is_ajax_request()) die('Acceso Denegado');
      
      $data = $this->input->post();
      $id_proyecto=$data['id_proyecto'];
      unset($data['id_proyecto']);      
      
      $data['id_actividad'] = intval($data['nva_actividad']);
      $data['compromiso'] = floatval($data['compromiso']);
      $data['causado'] = floatval($data['causado']);
      $data['precompromiso'] = floatval($data['precompromiso']);
      $data['pago'] = floatval($data['pago']);
      $data['disminucion'] = floatval($data['disminucion']);
      $data['aumento'] = floatval($data['aumento']);
      $data['asignar'] = floatval($data['asignar']);
      //$data['nom_prog'] = utf8_encode($data['nom_prog']);
      //$data['nompro'] = utf8_encode($data['nompro']);
            
      // Verificamos si la asociación ya existe
      $donde = array('documento' => $data['documento'],
                     'spg_cuenta'=> $data['spg_cuenta']  );
      
      $c = $this->Crud->contar_items('p_ejecucion_presupuestaria', $donde);
      
      if ($c > 0) // REGISTRO YA EXISTE, PROCEDEMOS A UPDATE
      {
         $datos = array('id_actividad' => $data['id_actividad']) ;
         $this->Crud->actualizar_registro('p_ejecucion_presupuestaria', $datos, $donde);
         $tipoAccion='UPDATE';
      }
      else // SI REGISTRO NO EXISTE, PROCEDEMOS A INSERT
      { 
        unset($data['nva_actividad']);
        $insertado=$this->Crud->insertar_registro('p_ejecucion_presupuestaria', $data);
        if (!$insertado){die('Error');}        
        $tipoAccion='INSERT';
      } 
        
      $registro='Registro de Ejecución Presupuestaria en Proyecto: ';
      $registro.=$data['codestpro1'];
      $registro.=' (id_proyecto: ';
      $registro.= $id_proyecto.')';
      $registro.=', id_actividad: ';
      $registro.=$data['id_actividad'];
      $registro.='. Registrado por: '.$this->session->userdata('usuario');
      $bitacora=array(
               'direccion_ip'   =>$this->session->userdata('ip_address'),
               'navegador'      =>$this->session->userdata('user_agent'),
               'id_usuario'     =>$this->session->userdata('id_usuario'),
               'controlador'    =>$this->uri->uri_string(),
               'tabla_afectada' =>'p_ejecucion_presupuestaria',
               'tipo_accion'    =>$tipoAccion,
               'registro'       =>$registro
           );
      $this->Crud->insertar_registro('z_bitacora', $bitacora);
      
      die(json_encode(array('id_proyecto'=>$id_proyecto)));
  }
  
  function guardarMeta()
  {
      if (!$this->input->is_ajax_request()) die('Acceso Denegado');
      
      $data = $this->input->post();      
      
      $data['id_actividad'] = intval($data['id_actividad']);
      $data['cantidad_meta'] = intval($data['cantidad_meta']);      
      $data['observaciones'] = $data['observaciones'];
      
      // Verificamos si la ejecución ya existe
      $donde = array('id_actividad' => $data['id_actividad']);
      
      $c = $this->Crud->contar_items('p_ejecucion_fisica', $donde);
      
      if ($c > 0) // REGISTRO YA EXISTE, PROCEDEMOS A UPDATE
      {         
         //unset($data['id_actividad']);         
         $this->Crud->actualizar_registro('p_ejecucion_fisica', $data, $donde);
         $tipoAccion='UPDATE';
      }
      else // SI REGISTRO NO EXISTE, PROCEDEMOS A INSERT
      {         
        $insertado=$this->Crud->insertar_registro('p_ejecucion_fisica', $data);
        if (!$insertado){die('Error');}        
        $tipoAccion='INSERT';
      } 
        
      $registro='Registro de Ejecución de Metas Físicas: ';      
      $registro.=' id_actividad: ';
      $registro.=$data['id_actividad'];
      $registro.='. Registrado por: '.$this->session->userdata('usuario');
      $bitacora=array(
               'direccion_ip'   =>$this->session->userdata('ip_address'),
               'navegador'      =>$this->session->userdata('user_agent'),
               'id_usuario'     =>$this->session->userdata('id_usuario'),
               'controlador'    =>$this->uri->uri_string(),
               'tabla_afectada' =>'p_ejecucion_fisica',
               'tipo_accion'    =>$tipoAccion,
               'registro'       =>$registro
           );
      $this->Crud->insertar_registro('z_bitacora', $bitacora);
           
  }  
  
  private function _getArrayActividades($id_proyecto)
  {
      $gantt = $this->Proyectos->gantt_proyecto($id_proyecto);
      if (!$gantt) die('ERROR. PROYECTO SIN ACTIVIDADES');
      
      $actividades=array('0'=>'[ Seleccione la Actividad ]');
      foreach ($gantt as $g)
      {
          $actividades[$g->id_actividad]=$g->codigo_ae.'.'.$g->codigo_act.' '.$g->descripcion_act;          
      }      
      return $actividades;
  }
  
}