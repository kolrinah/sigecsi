<?php if (!defined('BASEPATH')) exit('Sin Acceso Directo al Script');      
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *  SISTEMA DE GESTION Y CONTROL DEL SERVICIO INTERNO                *
 *  DESARROLLADO POR: ING.REIZA GARCÍA                               *
 *                    ING.HÉCTOR MARTÍNEZ                            *
 *  PARA:  MINISTERIO DEL PODER POPULAR PARA RELACIONES EXTERIORES   *
 *  FECHA: JUNIO DE 2013                                             *
 *  FRAMEWORK PHP UTILIZADO: CodeIgniter Version 2.1.3               *
 *                           http://ellislab.com/codeigniter         *
 *  TELEFONOS PARA SOPORTE: 0416-9052533 / 0212-5153033              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
class Supervisor_planes extends CI_Controller {
  function __construct() 
  {
     parent::__construct();
     $this->load->helper('form');
     $this->load->library('Comunes');
     $this->load->model('Usuarios');
     $this->load->model('Estructura');
     $this->load->model('Productos');
     $this->load->model('Proyectos');
     $this->load->model('Crud');
     $this->load->model('Personal');
     $this->load->model('Insumos');
  }
  
  function index()
  {
    // VERIFICAMOS SI EXISTE SESION ABIERTA    
    if (!$this->session->userdata('aprobado')) {redirect ('acceso', 'refresh'); exit();}
  
    // VERIFICACIÓN DE PERMISOS NECESARIOS PARA ACCESAR EL CONTROLADOR:
    // * DEBE PERTENECER AL AREA DE PLANES OPERATIVOS Y ESTRATEGICOS DE LA DIRECCIÓN DE PLANIFICACIÓN 
    //   (id_estructura= 48)
    // * O DEBE TENER ROL DE ADMINISTRADOR     
    if (!($this->session->userdata('administrador') || $this->session->userdata('id_estructura')=='48'))exit('Sin Acceso al Script');        

    // RECUPERA LA FECHA Y HORA DEL SISTEMA
    date_default_timezone_set('America/Caracas'); // Establece la Hora de Venezuela para funciones de fecha y hora              
    $ahora=  getdate(time());
    $yearmax=$ahora['year']+1; // El año máximo del POA es el año siguiente al año en curso
    $yearpoa=$this->session->userdata('yearpoa');
    
    $data=array();
    $data['titulo']='Supervisor de Planificación del POA';
    $data['subtitulo']='Planificación Operativa del Año:';
    $data['year_poa']= array(      
                        'name' => 'year_poa',
                        'id' => 'year_poa',
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
                       if ($('#year_poa').val()<$yearmax)
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
    
    $data['id_unidad']= array(      
                    'type'  => 'hidden',
                    'name'  => 'id_unidad',
                    'id'    => 'id_unidad',                    
                    'value' => 1);  
      
    $data['contenido']='supervisor_planes/supervisor_planes';    
    $data['script']='<!-- Cargamos CSS de DataTables -->'."\n";    
    $data['script'].="\t".'<link rel="stylesheet" type="text/css" media="all" href="'.base_url().'css/dataTables.css"/>'."\n";
    $data['script'].='<!-- Cargamos JS para DataTables -->'."\n";
    $data['script'].="\t".'<script type="text/javascript" src="'.base_url().'js/jquery.dataTables.js"></script>'."\n";
    $data['script'].='<!-- Cargamos Nuestro JS -->'."\n";
    $data['script'].="\t".'<script type="text/javascript" src="'.base_url().'js/supervisor_planes.js"></script>'."\n";
       
 
    $data['tabla']=$this->planificacion_unidades($yearpoa); 
    
 // CARGAMOS LA VISTA   
    $this->load->view('plantillas/plantilla_general',$data);  
  }  
  
  //////////////////////////////////////////////////////////////////////////////////////////
  
  function planificacion_unidades($yearpoa=0,$id_estructura=1,$verUnidad=0)
  {
    if ($this->input->is_ajax_request()) // Si la peticion vino por AJAX
    {
      $yearpoa=$this->input->post('yearpoa'); 
      $id_estructura=$this->input->post('id_estructura');
      $verUnidad=$this->input->post('verUnidad');
      $this->session->set_userdata('yearpoa',$yearpoa);
    }
    date_default_timezone_set('America/Caracas');    
    
    $unidad=$this->Estructura->obtener_estructura($id_estructura);
    if (!$unidad)die('error consultando estructura');
    
    // ENCABEZADOS DE TABLA
    $tabla='<table width="100%"><tr>';

    $accion=' onclick="javascript:cambiaUnidad('.$unidad['id_sup'].');" ';
    $tabla.='<td width="30px" class="BotonIco">'; 
    
    if ($unidad['id_estructura']!=1 || $verUnidad==1)
    {  
        $tabla.='<img src="'.base_url().'imagenes/back.png"'.$accion;
        $tabla.='title="clic para regresar a Unidad Superior"';
        $tabla.='/>';
    }
    
    $tabla.='</td>';
    $tabla.='<td style="text-align:right; width:100px">';
    $tabla.='<h4>'.$unidad['codigo'].'&nbsp;-&nbsp;';
    $tabla.='</td>';
    $tabla.='<td style="text-align:left">';         
    if ($unidad['id_estructura']==1 && $verUnidad==0)
    {    
        $tabla.='<h4>MPPRE SERVICIO INTERNO</h4>';
        $tabla.='</td>';
    }
    else
    {
        $tabla.='<h4>'.$unidad['descripcion'].'</h4>';
        $tabla.='</td>';   
    }        
    $tabla.='<td width="10%" style="text-align:right; padding:0 10px 5px 0">';
    $tope=strtotime($unidad['fecha_tope']);
    $ahora=time();
    $img=($ahora>$tope)?'cerrado.png':'abierto.png';
    $tope=date("d/m/Y",strtotime($unidad['fecha_tope']));
    $accion=' onclick="javascript:estableceFechaTope('.$unidad['id_estructura'].',\''.$tope.'\');" ';
    
    $tabla.='<img class="BotonIco" src="'.base_url().'imagenes/'.$img.'" '.$accion;
    $tabla.='title="Fecha Tope: '.date("d/m/Y",strtotime($unidad['fecha_tope'])).'"';
    $tabla.='/>';
    $tabla.='</td>';
    $tabla.='</tr></table>';
  
    // BUSCAMOS SI LA ESTRUCTURA NO POSEE UNIDADES INFERIORES
    $consulta=$this->Estructura->unidades_inferiores($id_estructura);
    if (!$consulta || $verUnidad==1)
    {
        $tabla.=$this->_planificacion_unidad($id_estructura, $verUnidad);
        
        if ($this->input->is_ajax_request()) die($tabla); // Si la peticion vino por AJAX
        else return $tabla;
    }  
    
    $titulo=' title="Clic para ver Detalles de la Unidad Administrativa"';
    $tabla.='<table class="TablaNivel1 Zebrado">';
    $tabla.=$this->_cabecetablaestado();
    $tabla.='<tbody>';
    
    // MOSTRAMOS LA UNIDAD SUPERIOR
    $accion=' onclick="javascript:cambiaUnidad('.$unidad['id_estructura'].',1)" ';
    $tabla.='<tr class="Resaltado" style="color:gray">';
    $tabla.='<td'.$titulo.$accion.'>';
    $tabla.=trim($unidad['codigo']);
    $tabla.='</td>';
    $tabla.='<td style="text-align:left"'.$titulo.$accion.'>';
    $tabla.=trim($unidad['descripcion']);
    $tabla.='</td>';
    $tabla.='<td'.$accion.'>';
      // PREPARAMOS LA CONSULTA 
      $donde="where id_estructura='".$unidad['id_estructura']."'";
      $plan=$this->Productos->estado_planificacion($donde,$yearpoa);
      if (!$plan)die('error unidad');
    $tabla.=(intval($plan['sp_det'])==0)?'-':round($plan['sp_plan'] / $plan['sp_det']*100).'%';
    $tabla.='</td>';
    $tabla.='<td'.$accion.'>'; // PROYECTOS
    // SI HAY PROYECTOS EN EL AÑO
    if (intval($plan['proyectos'])!=0)
    {
        $tabla.='<img src="'.base_url().'imagenes/gantt20.png" ';
        $tabla.=' title="Hay ';
        $tabla.=intval($plan['proyectos']);
        $tabla.=(intval($plan['proyectos'])>1)?' Proyectos':' Proyecto';
        $tabla.=' en la Unidad"';
        $tabla.='/>';
    }     
    $tabla.='</td>';
    $tabla.='<td'.$accion.'>'; // PERSONAL
    if ($plan['personal']>0)
    {
       $tabla.='<img src="'.base_url().'imagenes/user16.png" ';
       $tabla.=' title="'.$plan['personal'].' Personas Requeridas" ';
       $tabla.='/>';
    }    
    $tabla.='</td>';
    $tabla.='<td'.$accion.'>'; // INSUMOS
    if ($plan['insumos']>0)
    {
       $tabla.='<img src="'.base_url().'imagenes/insumos16.png" ';
       $tabla.=' title="'.$plan['insumos'].' Requerimientos de Insumo" ';
       $tabla.='/>';      
    }    
    $tabla.='</td>';
    $tabla.='<td'.$accion.'>'; // ENVIO
    // SI FUE ENVIADO EL POA
    if ($plan['enviado']=='t')
    {        
       switch ($unidad['id_tipo_estructura'])
       {
          case 1:
               $t=' title="Enviado a Planificación el: ';
               break;
           case 2:
               $t=' title="Enviado a Planificación el: ';
               break;
           default :
               $t=' title="Enviado a Unidad Superior el: ';
               break;
       }
       $tabla.='<img src="'.base_url().'imagenes/enviarpoa22.png" ';
       $tabla.=$t.date("d/m/Y",strtotime($plan['fecha_envio'])).'"';
       $tabla.='/>';
    }    
    if ($plan['enviado']=='f')
    {    
       $tabla.='<img src="'.base_url().'imagenes/poarechazado.png" ';
       $tabla.=' title="POA Rechazado por la unidad superior" ';
       $tabla.='/>';
    }
      
    $tabla.='</td>';
    $tabla.='<td>'; // CANDADO
    $tope=strtotime($unidad['fecha_tope']);
    $img=($ahora>$tope)?'cerrado20.png':'abierto20.png'; 
    $tope=date("d/m/Y",strtotime($unidad['fecha_tope']));
    $accion=' onclick="javascript:estableceFechaTope('.$unidad['id_estructura'].',\''.$tope.'\');" ';
    $tabla.='<img class="BotonIco" src="'.base_url().'imagenes/'.$img.'" '.$accion;
    $tabla.=' title="Fecha Tope: '.date("d/m/Y",strtotime($unidad['fecha_tope'])).'"';
    $tabla.='/>';    
    $tabla.='</td>';
    $tabla.='</tr>';
  
    // MOSTRAMOS LAS UNIDADES INFERIORES
    foreach ($consulta as $unidades)
    {      
      $accion=' onclick="javascript:cambiaUnidad('.$unidades['id_estructura'].');"';
      
      $tabla.='<tr class="Resaltado">';  
      $tabla.='<td '.$accion.$titulo.'>';
      $tabla.=$unidades['codigo'];
      $tabla.='</td>';
      $tabla.='<td style="text-align:left"'.$accion.$titulo.'>'; 
      $tabla.=$unidades['descripcion'];
      $tabla.='</td>';
      $tabla.='<td title="Avance del POA (%)"'.$accion.'>';
      // BUSCAMOS ESTRUCTURAS SUBORDINADAS PARA LA CONSULTA DE PLANES
      $temp=$this->Estructura->obtener_estructuras_inferiores($unidades['id_estructura']);
      if (!$temp)die('error consultando estructuras inferiores');
      $estructuras='where';
      foreach ($temp as $fila)
      {
        $estructuras.= ' id_estructura='.$fila['id_estructura'].' or';
      }
      $estructuras=  substr($estructuras, 0, -3);
      unset($fila); 
      $plan=$this->Productos->estado_planificacion($estructuras,$yearpoa);
      if (!$plan)die('error consultando estructuras inferiores');
      
      $tabla.=(intval($plan['sp_det'])==0)?'-':round($plan['sp_plan'] / $plan['sp_det']*100).'%';
      
      $tabla.='</td>';
      $tabla.='<td'.$accion.'>';
      // SI HAY PROYECTOS EN EL AÑO
      if (intval($plan['proyectos'])!=0)
      {
        $tabla.='<img src="'.base_url().'imagenes/gantt20.png" ';
        $tabla.=' title="Hay ';
        $tabla.=intval($plan['proyectos']);
        $tabla.=(intval($plan['proyectos'])>1)?' Proyectos':' Proyecto';
        $tabla.=' en la Unidad"';
        $tabla.='/>';
      }      
      $tabla.='</td>';
      $tabla.='<td'.$accion.'>'; // RESERVADO PARA PERSONAL
      if ($plan['personal']>0)
      {
         $tabla.='<img src="'.base_url().'imagenes/user16.png" ';
         $tabla.=' title="'.$plan['personal'].' Personas Requeridas" ';
         $tabla.='/>';
      }
      $tabla.='</td>';
      $tabla.='<td'.$accion.'>'; // RESERVADO PARA INSUMOS
      if ($plan['insumos']>0)
      {
         $tabla.='<img src="'.base_url().'imagenes/insumos16.png" ';
         $tabla.=' title="'.$plan['insumos'].' Requerimientos de Insumo" ';
         $tabla.='/>';      
      }
      $tabla.='</td>';
      $tabla.='<td'.$accion.'>';  // RESERVADO PARA ENVIO DE POA    
      // SI FUE ENVIADO EL POA
      if ($plan['enviado']=='t')
      {         
        switch ($unidades['id_tipo_estructura'])
        {
            case 1:
                $t=' title="Enviado a Planificación el: ';
                break;
            case 2:
                $t=' title="Enviado a Planificación el: ';
                break;
            default :
                $t=' title="Enviado a Unidad Superior el: ';
                break;
        }
        $tabla.='<img src="'.base_url().'imagenes/enviarpoa22.png" ';
        $tabla.=$t.date("d/m/Y",strtotime($plan['fecha_envio'])).'"';
        $tabla.='/>';
      }
      
      if ($plan['enviado']=='f')
      {    
        $tabla.='<img src="'.base_url().'imagenes/poarechazado.png" ';
        $tabla.=' title="POA Rechazado por la unidad superior" ';
        $tabla.='/>';
      }
      
      $tabla.='</td>';
      $tabla.='<td>';      
      $tope=strtotime($unidades['fecha_tope']);
      $img=($ahora>$tope)?'cerrado20.png':'abierto20.png'; 
      $tope=date("d/m/Y",strtotime($unidades['fecha_tope']));
      $accion=' onclick="javascript:estableceFechaTope('.$unidades['id_estructura'].',\''.$tope.'\');" ';
      $tabla.='<img class="BotonIco" src="'.base_url().'imagenes/'.$img.'" '.$accion;
      $tabla.=' title="Fecha Tope: '.date("d/m/Y",strtotime($unidades['fecha_tope'])).'"';
      $tabla.='/>';      
      $tabla.='</td>';
      $tabla.='</tr>';
    }

    //PIE DE TABLA
    $tabla.='</tbody></table><br/><br/>';  
   
    if ($this->input->is_ajax_request()) die($tabla); // Si la peticion vino por AJAX
    else return $tabla;
  }
  
  function _cabecetablaestado()
  {    
    $tabla='<thead><tr><th width="80px">CODIGO</th>';
    $tabla.='<th>UNIDAD ADMINISTRATIVA</th>';
    $tabla.='<th width="60px">AVANCE</th>'; // PRODUCTOS ADMIN.
    $tabla.='<th width="60px"></th>';  // PROYECTOS
    $tabla.='<th width="60px"></th>';  // PERSONAL
    $tabla.='<th width="60px"></th>';  // INSUMOS
    $tabla.='<th width="60px"></th>';  // ENVIO
    $tabla.='<th width="50px"></th>';  // CANDADO  
    $tabla.='</tr></thead>';
    $tabla.='<tfoot><tr>';
    $tabla.='<td >CODIGO</td>';
    $tabla.='<td >UNIDAD ADMINISTRATIVA</td>';
    $tabla.='<td>AVANCE</td>';
    $tabla.='<td></td>';
    $tabla.='<td></td>';
    $tabla.='<td></td>';
    $tabla.='<td></td>';    
    $tabla.='<td></td>';    
    
    $tabla.='</tr></tfoot>';
    return $tabla;
  }
  
  function _planificacion_unidad($id_estructura,$verUnidad=0)
  {
      // SELECTOR DE PLANIFICACION
      $selector=array('1'=>'','2'=>'','3'=>'','4'=>'');
      $selector[$this->session->userdata('selector')]='checked="checked"';
      $radio=' <input type="radio" name="selector" onchange="actualizaSelector($(this).val(),'.
                 $verUnidad.')" ';
      
      $tabla='<center title="Seleccione su opción">';
      $tabla.=$radio.$selector['1'].' value="1" id="selector1"/>'.
              '<label for="selector1" title="Productos Administrativos"></label>'.
              $radio.$selector['2'].' value="2" id="selector2" />'.
              '<label for="selector2" title="Planificación de Proyectos"></label>'.
              $radio.$selector['3'].' value="3" id="selector3" />'.
              '<label for="selector3" title="Requerimientos de Personal"></label>'.
              $radio.$selector['4'].' value="4" id="selector4" />'.
              '<label for="selector4" title="Requerimientos de Insumos"></label>';
      $tabla.='</center>';
      
      switch ($this->session->userdata('selector'))
      {
          case 1: 
                 $tabla.=$this->_planificacion_productos($id_estructura, $verUnidad);
                 break;
          case 2:                 
                 $tabla.=$this->_planificacion_proyectos($id_estructura);                 
                 break;
          case 3:
                 $tabla.=$this->_requerimiento_personal($id_estructura);
                 break;
          case 4:
                 $tabla.=$this->_requerimiento_insumos($id_estructura);
                 break;
          deafult:
                 $tabla.='ERROR SELECTOR VACÍO';
      }
      
      return $tabla;
  }
  
  function _planificacion_productos($id_estructura, $verUnidad=0)
  {
      $g=$this->session->userdata('gantt')?' checked="checked" ':'';
      $o=' onchange="actualizaGantt($(this).attr(\'checked\'),'.$verUnidad.');" ';
      $tabla='<table>';
      $tabla.='<tr>';
      $tabla.='<td>';
      $tabla.='<h4>PLANIFICACIÓN DE PRODUCTOS ADMINISTRATIVOS</h4>';
      $tabla.='</td>';      
      $tabla.='<td style="text-align:center;">';
      $tabla.='<input type="checkbox" id="botonGantt" '.$g.$o.' />';
      $tabla.='<label for="botonGantt" title="clic para cambiar">Gantt</label>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='</table>';
      $tabla.= ($this->session->userdata('gantt'))?$this->_gantt_productos($id_estructura):
                                                   $this->_tabla_productos($id_estructura);
      return $tabla;
  }
  
  function _planificacion_proyectos($id_estructura)
  {
      $tabla='<h4>PLANIFICACIÓN DE PROYECTOS </h4>';
      $yearpoa=$this->session->userdata('yearpoa');
      
      $tabla.='<div id="proyecto">';
      $estructura=" where id_estructura='".$id_estructura."' ";
      $proyectos=$this->Proyectos->listar_proyectos($estructura,$yearpoa);
      if ($proyectos->num_rows() === 0) // SI NO HAY PROYECTO EN LA UNIDAD
      { 
        $tabla='<h2><center>La Unidad No Posee Proyectos para el año indicado</center></h2>';        
        return $tabla;
      }       
      // CABECERA DE LA TABLA
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
      $tabla.='APROBADO (Bs.)';
      $tabla.='</th>';
      $tabla.='<th style="text-align:right; width:130px;">';
      $tabla.='PLANIFICADO (Bs.)';
      $tabla.='</th>';
      $tabla.='<th width="50px">';
      $tabla.='</th>';
      $tabla.='<th width="50px">';        
      $tabla.='</th>';
      $tabla.='<th width="50px">';        
      $tabla.='</th>';
      $tabla.='<th width="50px">';        
      $tabla.='</th>';   
      $tabla.='<th width="50px">';        
      $tabla.='</th>';           
      $tabla.='</tr>';
      $tabla.='</thead>';
      $tabla.='<tbody>';
      // CUERPO DE LA TABLA
      foreach($proyectos->result() as $p)
      {
        $accion=' onclick="revisarFicha('.$p->id_proyecto.');" ';
        
        $tabla.='<tr style="cursor:pointer">';
        $tabla.='<td title="Código del Proyecto"'.$accion.'>';
        $tabla.=trim($p->cod_proy);
        $tabla.='</td>';         
        $tabla.='<td style="text-align:left" title="Objetivo Específico del Proyecto"'.$accion.'>';
        $tabla.=trim($p->obj_esp);
        $tabla.='</td>';
        $tabla.='<td title="Presupuesto Aprobado" style="text-align:right;"'.
                 $accion.'>';
        $tabla.=number_format($p->monto_aprobado, 2, ',','.');
        $tabla.='</td>';
        $color=($p->total>0)?' color:black; ':' color:red; ';
        $tabla.='<td title="Presupuesto Planificado" style="'.$color.
                'text-align:right;">';
        $tabla.=number_format($p->total, 2, ',','.');
        $tabla.='</td>';
        // INDICADOR DE CUADRE PRESUPUESTARIO
        $r=$this->comunes->analisis_proyecto($p->monto_aprobado, $p->total, $p->estatus);           
        $tabla.='<td style="text-align:left">';           
        $tabla.='<img src="'.base_url().'imagenes/'.$r['img'].'" ';
        $tabla.='title="'.$r['msj'].'" ';
        $tabla.='/>';           
        $tabla.='</td>'; 
           
        $tabla.='<td>';
        $accion=' onclick="revisarFicha('.$p->id_proyecto.');" ';
        $img='formulario.png';
        $tabla.='<img src="'.base_url().'imagenes/'.$img.'" '.$accion;
        $tabla.='title="Ficha Técnica"';
        $tabla.='/>';           
        $tabla.='</td>';
        $tabla.='<td>';
        $accion=' onclick="listarAcciones('.$p->id_proyecto.');" ';
        $img='acciones.png';
        $tabla.='<img src="'.base_url().'imagenes/'.$img.'" '.$accion;
        $tabla.='title="Acciones Específicas"';
        $tabla.='/>';   
        $tabla.='</td>';
        $tabla.='<td>';
        $accion=' onclick="planProyecto('.$p->id_proyecto.');" ';
        $img='gantt20.png';
        $tabla.='<img src="'.base_url().'imagenes/'.$img.'" '.$accion;
        $tabla.='title="Gantt de Actividades"';
        $tabla.='/>';   
        $tabla.='</td>';
        $tabla.='<td>';        
        $tabla.='</td>';        
        $tabla.='</tr>';                  
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
      $tabla.='APROBADO (Bs.)';
      $tabla.='</td>';
      $tabla.='<td style="text-align:right;">';
      $tabla.='PLANIFICADO (Bs.)';
      $tabla.='</td>';
      $tabla.='<td>';
      $tabla.='</td>';
      $tabla.='<td>';        
      $tabla.='</td>';      
      $tabla.='<td>';        
      $tabla.='</td>';
      $tabla.='<td>';        
      $tabla.='</td>';
      $tabla.='<td>';        
      $tabla.='</td>';      
      $tabla.='</tr>';
      $tabla.='</tfoot>';
      $tabla.='</table>';         
      
      $tabla.='</div>';
      return $tabla;
  }
  
  function _requerimiento_personal($id_estructura)
  {
      $tabla='<h4>REQUERIMIENTO DE PERSONAL </h4>';
      $estructura=" where id_estructura='".$id_estructura."' ";
      $yearpoa=$this->session->userdata('yearpoa');
      
      $personal=$this->Personal->listar_requerimiento_personal($estructura, $yearpoa);
      if ($personal->num_rows() === 0) // SI NO HAY REQUERIMIENTO DE PERSONAL EN LA UNIDAD
      { 
        $tabla='<h2><center>La Unidad No Posee Requerimiento de Personal para el año indicado</center></h2>';        
        return $tabla;
      } 
      
      $n = $personal->num_rows();
      $p = $personal->result();
      $i=0;
      $ac = $p[$i]->accion_centralizada;
      
      while($i<$n)
      {
        $tabla.='<h5>'.(($ac=='t')?'Personal por Acción Centralizada':'Personal por Proyectos').'</h5>';
        $tabla.='<table class="TablaNivel1 Zebrado">';
        $tabla.='<thead>';
        $tabla.='<tr>';
        $tabla.='<th width="50px">';
        $tabla.='</th>';
          
        $tabla.='<th width="250px" style="text-align:left!important">';
        $tabla.='TIPO DE PERSONAL';
        $tabla.='</th>';
        $tabla.='<th width="250px" style="text-align:left!important">';
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
        $tabla.='<th>';     
        $tabla.='TOTAL';
        $tabla.='</th>';          
        $tabla.='<th width="50px">';
        $tabla.='</th>';
        $tabla.='</tr>';
        $tabla.='</thead>';
        $tabla.='<tbody>';
        $machos=0;
        $hembras=0;

        while (($ac==$p[$i]->accion_centralizada) )
        {         
          $tabla.='<tr>';
          $tabla.='<td>';
          $tabla.='</td>';
          $tabla.='<td title="Tipo de Personal" style="text-align:left!important">';
          $tabla.=trim($p[$i]->tipo_personal);
          $tabla.='</td>'; 
          $tabla.='<td style="text-align:left!important" title="Personal">';
          $tabla.=trim($p[$i]->personal);
          $tabla.='</td>';          
          $tabla.='<td>';
          $tabla.=$p[$i]->femenino;             
          $tabla.='</td>';
          $tabla.='<td>';
          $tabla.=$p[$i]->masculino;
          $tabla.='</td>';        
          $tabla.='<td>';         
          $tabla.=$p[$i]->femenino + $p[$i]->masculino;
          $tabla.='</td>';
          $tabla.='<td>'; 
          $tabla.='</td>'; 
          $tabla.='</tr>'; 
          $hembras+=$p[$i]->femenino;
          $machos+=$p[$i]->masculino;
          $i++;
          if ($i==$n) break;
        }
        @$ac=$p[$i]->accion_centralizada;
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
  
  function _requerimiento_insumos($id_estructura)
  {
      $tabla='<h4>REQUERIMIENTO DE INSUMOS </h4>';
      $estructura=" where id_estructura='".$id_estructura."' ";
      $yearpoa=$this->session->userdata('yearpoa');
      
      $insumos=$this->Insumos->listar_requerimiento_insumos($estructura,$yearpoa); 
      if ($insumos->num_rows() === 0) // SI NO HAY REQUERIMIENTO DE INSUMOS EN LA UNIDAD
      { 
        $tabla='<h2><center>La Unidad No Posee Requerimiento de Insumos para el año indicado</center></h2>';        
        return $tabla;
      }
      $n = $insumos->num_rows();
      $insumos = $insumos->result();
      $i=0;
      $ti = $insumos[$i]->partida_generica;
      
      while($i<$n)
      {
          $tabla.='<h5>'.$insumos[$i]->tipo_insumo.'</h5>';
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
          $tabla.='<th width="50px">';          
          $tabla.='</th>';
          $tabla.='</tr>';
          $tabla.='</thead>';
          $tabla.='<tbody>';

        while (($ti==$insumos[$i]->partida_generica) )
        {         
          $tabla.='<tr>';
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
          $tabla.='<td>';           
          $tabla.='</td>'; 
          $tabla.='</tr>'; 
          $i++;
          if ($i==$n) break;
        }
        @$ti=$insumos[$i]->partida_generica;
        //PIE DE TABLA
        $tabla.='</tbody>';
        $tabla.='<tfoot>';
        $tabla.='<tr>';
        $tabla.='<td>CODIGO';
        $tabla.='</td>';
        $tabla.='<td>PARTIDA';        
        $tabla.='</td>';
        $tabla.='<td style="text-align:left">REQUERIMIENTO';
        $tabla.='</td>';
        $tabla.='<td>CANT';
        $tabla.='</td>';
        $tabla.='<td style="text-align:left">UNIDAD DE MEDIDA';
        $tabla.='</td>';
        $tabla.='<td>EXISTENCIA'; 
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
 
  // Actualizamos selector 
  function actualizaSelector()
  {
     if (!$this->input->is_ajax_request())die('Acceso denegado'); 
     
     $this->session->set_userdata('selector',($this->input->post('selector')));
  }
  
  // Actualizamos botón gantt
  function actualizaGantt()
  {
     if (!$this->input->is_ajax_request())die('Acceso denegado'); 
     $gantt=($this->input->post('gantt')=='checked')?true:false;
     $this->session->set_userdata('gantt',$gantt);
  }
  
  function _tabla_productos($id_estructura)
  {
     $yearpoa=$this->session->userdata('yearpoa');
     $estructura="where id_estructura='".$id_estructura."' ";
     $planificacion=$this->Productos->planificacion_productos($estructura,$yearpoa);
     
     if ($planificacion->num_rows()==0) // SI NO HAY SUB-PRODUCTOS DETERMINADOS EN LA UNIDAD
     {        
       $tabla='<h2><center>La Unidad No Posee Sub-Productos Determinados</center></h2>';      
       return $tabla;       
     } 
     
     $tabla='<table class="TablaNivel1 Zebrado">';
     $tabla.=$this->_cabecetabla(true);
     $tabla.='<tbody>';
     foreach ($planificacion->result() as $p)
     {
       $tabla.='<tr class="Resaltado">';
       $titulo= trim($p->pcodigo).'. '.trim($p->pnombre);
       $idsp=$p->id_subproducto;
       $tabla.='<td title="'.$titulo.'" onclick="javascript:VerInfo('.$idsp.')">';
       $tabla.=trim($p->pcodigo).'.'.trim($p->scodigo);
       $tabla.='</td>';          
       $tabla.='<td style="text-align:left" title="'.$titulo.'" onclick="javascript:VerInfo('.$idsp.')">';
       $tabla.=trim($p->snombre);
       $tabla.='</td>';
       $tabla.='<td title="Enero">';
       $tabla.=trim($p->ene)==''?0:trim($p->ene);
       $tabla.='</td>';
       $tabla.='<td title="Febrero">';
       $tabla.=trim($p->feb)==''?0:trim($p->feb);
       $tabla.='</td>';
       $tabla.='<td title="Marzo">';
       $tabla.=trim($p->mar)==''?0:trim($p->mar);
       $tabla.='</td>';
       $tabla.='<td title="Abril">';
       $tabla.=trim($p->abr)==''?0:trim($p->abr);
       $tabla.='</td>';
       $tabla.='<td title="Mayo">';
       $tabla.=trim($p->may)==''?0:trim($p->may);
       $tabla.='</td>';
       $tabla.='<td title="Junio">';
       $tabla.=trim($p->jun)==''?0:trim($p->jun);
       $tabla.='</td>';
       $tabla.='<td title="Julio">';
       $tabla.=trim($p->jul)==''?0:trim($p->jul);
       $tabla.='</td>';
       $tabla.='<td title="Agosto">';
       $tabla.=trim($p->ago)==''?0:trim($p->ago);
       $tabla.='</td>';
       $tabla.='<td title="Septiembre">';
       $tabla.=trim($p->sep)==''?0:trim($p->sep);
       $tabla.='</td>';
       $tabla.='<td title="Octubre">';
       $tabla.=trim($p->oct)==''?0:trim($p->oct);
       $tabla.='</td>';
       $tabla.='<td title="Noviembre">';
       $tabla.=trim($p->nov)==''?0:trim($p->nov);
       $tabla.='</td>';
       $tabla.='<td title="Diciembre">';
       $tabla.=trim($p->dic)==''?0:trim($p->dic);
       $tabla.='</td>';
       $tabla.='<td title="Total Anual">';
       $tabla.=trim($p->anual)==''?0:trim($p->anual);
       $tabla.='</td>';
       $tabla.='</tr>';        
    }
    //PIE DE TABLA
    $tabla.='</tbody></table><br/><br/>';               
    return $tabla;     
  }
  
  function _gantt_productos($id_estructura)
  {
     $yearpoa=$this->session->userdata('yearpoa');
     $estructura="where id_estructura='".$id_estructura."' ";
     $planificacion=$this->Productos->gantt_productos($estructura,$yearpoa);
     
     if ($planificacion->num_rows()==0) // SI NO HAY SUB-PRODUCTOS DETERMINADOS EN LA UNIDAD
     {        
       $tabla='<h2><center>La Unidad No Posee Sub-Productos Determinados</center></h2>';      
       return $tabla;       
     } 
 
     $tabla='<table class="TablaNivel1 Zebrado">';
     $tabla.=$this->_cabecetabla();
     $tabla.='<tbody>';
     
     $subpro='';
     foreach ($planificacion->result() as $p)
     {
        if ($subpro!=$p->id_subproducto) 
        {
          $titulo=trim($p->pcodigo).'. '.trim($p->pnombre);
          $idsp=$p->id_subproducto;   
          $tabla.='<tr class="Resaltado">';      
          $tabla.='<td title="'.$titulo.'" id="codSP_'.$idsp.'" onclick="javascript:VerInfo('.$idsp.')">';
          $tabla.=trim($p->pcodigo).'.'.trim($p->scodigo);
          $tabla.='</td>';                     
          $tabla.='<td style="text-align:left" title="'.$titulo.'" id="nomSP_'.$idsp.'" onclick="javascript:VerInfo('.$idsp.')">';
          $tabla.=trim($p->snombre);
          $tabla.='</td>';
          $tabla.='<td class="Cuadricula" colspan="12">';
          $tabla.='</td>';
          $tabla.='<td>';           
          $tabla.='</td>';
          $tabla.='</tr>';
        }
        // ACTIVIDADES
        $idplan=$p->id_plan_producto;
        $tabla.='<tr>';
        $tabla.='<td>';
        $tabla.='&nbsp';
        $tabla.='</td>';            
        $tabla.='<td style="text-align:left;">';             
        if ($p->actividad!='')
        {
          $fechai=date("d/m/Y",strtotime($p->fecha_ini));
          $fechaf=date("d/m/Y",strtotime($p->fecha_fin));
          $duracion=$this->comunes->diferenciaEntreFechas($p->fecha_ini, $p->fecha_fin, 'DIAS', true);
          $tabla.='<span id="act_'.$idplan.'">'.trim($p->actividad).'</span><br/>';
          $tabla.='<i>(Responsable: '.trim($p->responsable).' /';
          $tabla.=' Duración: ';
          $tabla.=($duracion<1)?1:$duracion;
          $tabla.=($duracion<1)?' día) </i>':' días) </i>';                
          $tabla.='<input type="hidden" id="fechaIni_'.$idplan.'" ';
          $tabla.='value="'.$fechai.'" />';
          $tabla.='<input type="hidden" id="fechaFin_'.$idplan.'" ';
          $tabla.='value="'.$fechaf.'" />';
          $tabla.='<input type="hidden" id="idResponsable_'.$idplan.'" ';
          $tabla.='value="'.$p->id_responsable.'" />';
        }
        $tabla.='</td>';
        $titulo='';
        $gantt='*** SUB-PRODUCTO SIN PLANIFICACION PROGRAMADA ***';
        if ($p->actividad!='')
        {
           $gantt=$this->comunes->mini_gantt($p->fecha_ini, $p->fecha_fin, 40);
           $titulo='Desde: ';
           $titulo.=date("d/m/Y",strtotime($p->fecha_ini));
           $titulo.=' Hasta: ';
           $titulo.=date("d/m/Y",strtotime($p->fecha_fin));
        }
        $tabla.='<td class="Cuadricula" colspan="12" title="'.$titulo.'">';
        $tabla.='<div class="gantt01">';
        $tabla.=$gantt;
        $tabla.='</div>';
        $tabla.='</td>';
        $tabla.='<td>';
          
        $tabla.='</td>';
        $tabla.='</tr>';             
        $subpro=$p->id_subproducto;     
    }
    $tabla.='</tbody></table><br/><br/>'; 
    return $tabla;
  }
        
  function ver_subproducto()
  {
      if (!$this->input->is_ajax_request()) die('Acceso Denegado');
       $id_subprod= intval($this->input->post('id_subproducto')); 
       $subproducto=$this->Productos->obtener_subproducto($id_subprod);
       if (count($subproducto)!=1)  
       {
        die('Error');
       }
       
      foreach ($subproducto as $fila)
      {
         $data='<div class="EntraDatos Info">';
         $data.='<table>';
         $data.='<thead>';
         $data.='<tr><th colspan="2">';            
         $data.='Información General del Sub-Producto Administrativo';
         $data.='</th></tr>';
         $data.='</thead>';
         $data.='<tbody>';
         $data.='<tr><td width="210px" style="text-align:right">';
         $data.='<label>Unidad Administrativa:</label>';
         $data.='</td>';
         $data.='<td>';
         $data.=$fila['ecodigo'].' - '.$fila['estructura'];
         $data.='</td>';
         $data.='</tr>';
         $data.='<tr><td style="text-align:right">';
         $data.='<label>Producto Administrativo:</label>';
         $data.='</td>';
         $data.='<td>';
         $data.=$fila['pcodigo'].'. '.$fila['pnombre'];
         $data.='</td>';
         $data.='</tr>';
         $data.='<tr>';
         $data.='<td style="text-align:right">';
         $data.='<label>Definición del Producto:</label>';
         $data.='</td>';
         $data.='<td>';
         $data.=$fila['pdefinicion'];         
         $data.='</td></tr>';
         $data.='<tr>';
         $data.='<td style="text-align:right">';
         $data.='<label>Sub-Producto Administrativo:</label>';
         $data.='</td>';
         $data.='<td>';
         $data.=$fila['pcodigo'].'.'.$fila['scodigo'].' '.$fila['nombre'];
         $data.='</td></tr>';
         $data.='<tr>';
         $data.='<td style="text-align:right">';
         $data.='<label>Definición del Sub-Producto:</label>';
         $data.='</td>';
         $data.='<td>';
         $data.=$fila['definicion'];         
         $data.='</td></tr>';
         $data.='<tr>';
         $data.='<td style="text-align:right">';
         $data.='<label>Unidad de Medida:</label>';
         $data.='</td>';
         $data.='<td>';
         $data.=$fila['unidad_medida'];
         $data.='</td></tr>';
         $data.='<tr>';
         $data.='<td style="text-align:right">';
         $data.='<label>Clasificación del Sub-Producto:</label>';
         $data.='</td>';
         $data.='<td>';
         // DETERMINADO/INDETERMINADO
              if ($fila['es_determinado']=='t')
              {
                  $datos=array(
                              'img'  =>base_url()."imagenes/determinado.png",
                              'span' => 'Sub-Producto Determinado');
              }
              else
              {
                  $datos=array(
                              'img'  =>base_url()."imagenes/indeterminado.png",
                             'span'  => 'Sub-Producto Indeterminado');
              }         
         $data.='<img src="'.$datos['img'].'"/>';
         $data.='<span>&nbsp;'.$datos['span'].'</span><br/>';
         // ORDINARIO/EXTRA-ORDINARIO
              if ($fila['es_extraordinario']=='t')
              {
                  $datos=array(
                              'img'  =>base_url()."imagenes/medalla.png",
                              'span' => 'Sub-Producto Extraordinario');
              }
              else
              {
                  $datos=array(
                              'img'  =>base_url()."imagenes/lego.png",
                             'span'  => 'Sub-Producto Ordinario');
              }         
         $data.='<img src="'.$datos['img'].'"/>';
         $data.='<span>&nbsp;'.$datos['span'].'</span><br/>';
         // BOTON TRAMITE/NO TRAMITE
              if ($fila['es_tramite']=='t')
              {
                  $datos=array(
                              'img'  =>base_url()."imagenes/tramite.png",
                              'span' => 'Trámite Administrativo a Terceros');                       
              }
              else
              {
                  $datos=array(
                              'img'  =>base_url()."imagenes/notramite.png",
                              'span' => 'No es Trámite Administrativo a Terceros');
              }                  
         $data.='<img src="'.$datos['img'].'"/>';
         $data.='<span>&nbsp;'.$datos['span'].'</span><br/>';
         $data.='</td></tr>';
         $data.='</tbody>';
         $data.='<tfoot>';
         $data.='<tr><td colspan="2">';        
         $data.='<div class="BotonIco" onclick="javascript:CancelarModal()" title="Cerrar Ventana">';
         $data.='<img src="imagenes/cancel.png"/>&nbsp;';
         $data.='Cerrar';
         $data.= '</div>';
         $data.='</td></tr>';
         $data.='</tfoot>';
         $data.='</table>';   
         $data.='</div>';
      }        
       die($data);
  }
  
  function estableceFechaTope()
  {
    if (!$this->input->is_ajax_request()) die('Acceso Denegado');          
    $id_estructura=intval($this->input->post('id_estructura'));
    $fecha_tope=$this->input->post('fecha_tope');
    
    $unidad=$this->Estructura->obtener_estructura($id_estructura);
    if (!$unidad)die('error consultando la estructura');
    
    $data='<div class="EntraDatos" >';
    $data.='<table>';
    $data.='<thead>';
    $data.='<tr><th>';            
    $data.='Fecha de Cierre del POA';  
    $data.='</th></tr>';           
    $data.='</thead>';            
    $data.='<tbody>';
    $data.='<tr>';
    $data.='<td style="text-align:center">';
    $data.='<label>Establecer Fecha Tope para:</label>';
    if ($unidad['id_tipo_estructura']==1)
    {
        $data.='<h4>TODAS LAS UNIDADES ADMINISTRATIVAS DEL SERVICIO INTERNO DEL MINISTERIO</h4>';
    }
    else
    {
        $data.='<h4>'.$unidad['codigo'].' - '.$unidad['descripcion'].'</h4>';    
    }
    $data.='<input type="text" class="Fechas Editable" id="Fecha" title="Fecha Tope" readonly="readonly" ';
    $data.='value="'.$fecha_tope.'"';
    $data.='/>';
    $data.='</td>';
    $data.='</tr>'; 
    $data.='</tbody>';
    
    $data.='<tfoot>';
    $data.='<tr><td colspan="2">';
    $data.='<div class="BotonIco" onclick="javascript:GuardarFecha('.$id_estructura.')" title="Establecer Fecha Tope">';
    $data.='<img src="imagenes/guardar32.png"/>&nbsp;';   
    $data.='Guardar';
    $data.= '</div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
    $data.='<div class="BotonIco" onclick="javascript:CancelarModal()" title="Cancelar">';
    $data.='<img src="imagenes/cancel.png"/>&nbsp;';
    $data.='Cancelar';
    $data.= '</div>';
    $data.='</td></tr>';
    $data.='</tfoot>';
    $data.='</table>'; 
    $data.='</div>';
    
    die($data);
  }
  
  function guardar_fecha()
  {   
    if (!$this->input->is_ajax_request()) die('Acceso Denegado');        
        $id_estructura=$this->input->post('id_estructura');
        
        $unidades=$this->Estructura->obtener_estructuras_inferiores($id_estructura);
        if (!$unidades){die('Error');}
        
        $datos=array('fecha_tope' => $this->input->post('fecha_tope')); 
        
        foreach ($unidades as $uni)
        {
          $donde=array('id_estructura'=> $uni['id_estructura']);
          $actualizado=$this->Crud->actualizar_registro('e_estructura', $datos, $donde);        
          if (!$actualizado){die('Error');}
        }
        
           $registro='Establecida Fecha Tope de Cierre del POA';
           $registro.=' ('.$datos['fecha_tope'].') para id_estructura: '.$id_estructura;
           $registro.=' y sus subordinadas';
           $registro.='. Registrado por: '.$this->session->userdata('usuario');
           $bitacora=array(
               'direccion_ip'   =>$this->session->userdata('ip_address'),
               'navegador'      =>$this->session->userdata('user_agent'),
               'id_usuario'     =>$this->session->userdata('id_usuario'),
               'controlador'    =>$this->uri->uri_string(),
               'tabla_afectada' =>'e_estructura',
               'tipo_accion'    =>'UPDATE',
               'registro'       =>$registro
           );
           $this->Crud->insertar_registro('z_bitacora', $bitacora);            
  }
  
  function revisar_ficha()
  {
      if (!$this->input->is_ajax_request()) die('Acceso Denegado');
      
      $id_proyecto= intval($this->input->post('id_proyecto'));
            
      $proy=$this->Proyectos->obtener_proyecto($id_proyecto);
      
      if (!$proy)die('error consultando proyecto');     
      
      $tabla='<table width="100%">';
      $tabla.='<tr>';
      $tabla.='<td width="30px">';    
      $tabla.='</td>';
      $tabla.='<td width="30px" class="BotonIco">';
      $accion=' onclick="javascript:actualiza(1);" ';
      $tabla.='<img src="'.base_url().'imagenes/back.png"'.$accion;
      $tabla.='title="clic para regresar"';
      $tabla.='/>'; 
      $tabla.='</td>';
      $tabla.='<td>';
      $tabla.='<strong>PROYECTO: '.trim($proy->cod_proy).'&nbsp;-&nbsp;</strong>';    
      $tabla.=trim($proy->obj_esp);
      $tabla.='</td>';
      $tabla.='</tr>';      
      $tabla.='</table>';      
      
      $tabla.='<div class="EntraDatos">';
      $tabla.='<table style="margin-top:0px!important">';
      $tabla.='<thead>';
      $tabla.='<tr><th colspan="2" style="text-align:left">';            
      $tabla.='Ficha Técnica del Proyecto: '.trim($proy->cod_proy); 
      $tabla.='</th></tr>';           
      $tabla.='</thead>';
      $tabla.='<tbody>'; 
      $tabla.='<tr>'; 
      $tabla.='<td colspan="2">';
      $tabla.='<label>Nombre del Proyecto (Objetivo Específico):</label>';      
      $tabla.=trim($proy->obj_esp);
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>';
      $tabla.='<td>';
      $tabla.='Código del Proyecto: ';      
      $tabla.='<input class="Editable" id="cod_proy" style="text-align:center!important;" maxlength="10"';   
      $tabla.=' onkeypress="return onlyDigits(event, this.value,false,false,true,\',\',\'.\',0);"';        
      $tabla.=' value="'.trim($proy->cod_proy).'" ';
      $tabla.='/>';
      $tabla.='</td>';
      $tabla.='<td>';
      $tabla.='Presupuesto Aprobado Bs.';
      $tabla.='<input class="Editable" id="monto_aprobado" style="text-align:right" ';      
      $tabla.=' onblur="this.value=formatMoneda(this.value,\',\',\'.\',2);"';   
      $tabla.=' onkeypress="return onlyDigits(event, this.value,true,false,true,\',\',\'.\',2);"';      
      $tabla.=' value="'.number_format($proy->monto_aprobado,2,',','.').'" ';
      $tabla.='/>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr style="border-bottom:#576F89 solid 1px">';
      $tabla.='<td colspan="2">';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>'; 
      $tabla.='<td colspan="3">';
      $tabla.='<label>Objetivo General:</label>';      
      $tabla.=trim($proy->obj_gen);
      $tabla.='</td>';
      $tabla.='</tr>';      
      $tabla.='<tr>';      
      $tabla.='<td colspan="2">';
      $tabla.='<label>Descripción Breve del Proyecto:</label>';
      $tabla.=trim($proy->descripcion_breve);
      $tabla.='</td>';
      $tabla.='</tr>'; 
      $tabla.='<tr style="border-bottom:#576F89 solid 1px">';
      $tabla.='<td colspan="2">';
      $tabla.='</td>';
      $tabla.='</tr>';      
      $tabla.='<tr>'; 
      $tabla.='<td colspan="2">';
      $tabla.='<label>Enunciado del Problema o Necesidad que Origina el Proyecto:</label>';   
      $tabla.=trim($proy->problema);
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>'; 
      $tabla.='<td colspan="2">';
      $tabla.='<label>Indicador de la situación actual del problema:</label>';      
      $tabla.=trim($proy->indicador_problema);      
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr style="border-bottom:#576F89 solid 1px">';
      $tabla.='<td colspan="2">';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>'; 
      $tabla.='<td colspan="2">';
      $tabla.='<label>Indicador de la situación Objetivo del Proyecto:</label>';      
      $tabla.=trim($proy->indicador_obj_proy);      
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>'; 
      $tabla.='<td colspan="2">';
      $tabla.='<label>Resultado del Proyecto:</label>';
      $tabla.=trim($proy->resultado);
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr style="border-bottom:#576F89 solid 1px">';
      $tabla.='<td colspan="2">';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>'; 
      $tabla.='<td width="50%">';
      $tabla.='<label>Responsable del Proyecto:</label>';      
      $tabla.=trim($proy->nombre).' '.trim($proy->apellido).' ('.trim($proy->correo).')';
      $tabla.='</td>';      
      $tabla.='<td style="padding-right:35px">';
      $tabla.='<label>Teléfonos de Contacto:</label>';      
      $tabla.=trim($proy->telefonos);
      $tabla.='</td>';
      $tabla.='</tr>';            
      $tabla.='</tbody>';
      
      $tabla.='<tfoot>';
      $tabla.='<tr><td colspan="3">';
      $tabla.='<div class="BotonIco" onclick="javascript:actualizarFicha('.$proy->id_proyecto.')" title="Guardar Cambios">';
      $tabla.='<img src="imagenes/guardar32.png"/>&nbsp;';   
      $tabla.='Guardar';
      $tabla.= '</div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
      $tabla.='<div class="BotonIco" onclick="javascript:actualiza(1);" title="Salir">';
      $tabla.='<img src="imagenes/cancel.png"/>&nbsp;';
      $tabla.='Cancelar';
      $tabla.= '</div>';
      $tabla.='</td></tr>';
      $tabla.='</tfoot>';
      $tabla.='</table>';
      $tabla.='</div>';
      die ($tabla);      
  }
   
  function actualizar_ficha()
  {
      if (!$this->input->is_ajax_request()) die('Acceso Denegado');
      
      $donde=array(
                'id_proyecto'  => intval($this->input->post('id_proyecto'))
                  );
      $datos=array(              
              'monto_aprobado'=>str_ireplace(",",".",str_ireplace(".","",$this->input->post('monto_aprobado'))),
              'cod_proy'=>$this->input->post('cod_proy'),
                  );        
      $actualizado=$this->Crud->actualizar_registro('p_proyectos', $datos, $donde);        
      if (!$actualizado){die('Error');}
      else 
      {           
         $registro='Asignación de Código y Monto Aprobado de Proyecto al id_proyecto: '.
                    $donde['id_proyecto'];         
         $registro.='. Actualizado por: '.$this->session->userdata('usuario');
         $bitacora=array(
             'direccion_ip'   =>$this->session->userdata('ip_address'),
             'navegador'      =>$this->session->userdata('user_agent'),
             'id_usuario'     =>$this->session->userdata('id_usuario'),
             'controlador'    =>$this->uri->uri_string(),
             'tabla_afectada' =>'p_proyectos',
             'tipo_accion'    =>'UPDATE',
             'registro'       =>$registro
         );
         $this->Crud->insertar_registro('z_bitacora', $bitacora);            
      };   
  }
  
  function listar_acciones()
  {
    if (!$this->input->is_ajax_request()) die('Acceso Denegado'); // Si la peticion No vino por AJAX
    
    $id_proyecto=$this->input->post('id_proyecto'); 
    
    $proyecto=$this->Proyectos->obtener_proyecto($id_proyecto);
    if (!$proyecto)die('error consultando proyecto');
        
    $tabla='<table width="100%">';
    $tabla.='<tr>';
    $tabla.='<td width="30px">';    
    $tabla.='</td>';
    $tabla.='<td width="30px" class="BotonIco">';
    $accion=' onclick="javascript:actualiza(1);" ';
    $tabla.='<img src="'.base_url().'imagenes/back.png"'.$accion;
    $tabla.='title="clic para regresar"';
    $tabla.='/>'; 
    $tabla.='</td>';
    $tabla.='<td>';
    $tabla.='<strong>PROYECTO: '.trim($proyecto->cod_proy).'&nbsp;-&nbsp;</strong>';    
    $tabla.=trim($proyecto->obj_esp);
    $tabla.='</td>';
    $tabla.='</tr>';
    $tabla.='<tr>';
    $tabla.='<td colspan="2">';
    $tabla.='</td>';
    $tabla.='<td>';
    $tabla.='<h4>Acciones Específicas</h4>';
    $tabla.='</td>';
    $tabla.='</tr>';
    $tabla.='</table>';

    $acciones=$this->Proyectos->listar_acciones($id_proyecto);  
    
    if (!$acciones) // SI NO HAY ACCIONES ESPECIFICAS EN EL PROYECTO
    { 
      $tabla.='<h2><center>- Proyecto sin Acciones Específicas -</center></h2>';      
      
      die($tabla);      
    }     
    // CONSTRUIMOS LA TABLA 

    // ENCABEZADOS DE TABLA
    $tabla.='<table class="TablaNivel1 Zebrado">';
    $tabla.='<thead>';
    $tabla.='<tr>';
    $tabla.='<th width="30px">';
    $tabla.='Nº';
    $tabla.='</th>';
    $tabla.='<th>';
    $tabla.='Descripción';
    $tabla.='</th>';
    $tabla.='<th style="width:150px; text-align:left">';
    $tabla.='Indicadores';
    $tabla.='</th>';
    $tabla.='<th style="width:150px; text-align:left">';        
    $tabla.='Medios de Verificación';
    $tabla.='</th>';
    $tabla.='<th style="width:150px; text-align:left">';
    $tabla.='Supuestos';
    $tabla.='</th>';
    $tabla.='<th style="width:100px; text-align:right; padding-right:10px">';
    $tabla.='Presupuesto(Bs.)';
    $tabla.='</th>';    
    $tabla.='<th width="30px">';    
    $tabla.='</th>';
    $tabla.='</tr>';
    $tabla.='</thead>';
    $tabla.='<tbody>';
    
    $total=0;
    foreach($acciones as $ae)
    {
       $tabla.='<tr>';
       $tabla.='<td title="Nº de la Acción">';
       $tabla.=trim($ae['codigo_ae']);
       $tabla.='</td>';        
       $tabla.='<td style="text-align:left!important" title="Acción Específica" >';
       $tabla.=trim($ae['descripcion_ae']);
       $tabla.='</td>';
              
       $tabla.='<td style="text-align:left!important" title="Indicadores Objetivamente Verificables">';
       $tabla.=trim($ae['iov_ae']);
       $tabla.='</td>';
       
       $tabla.='<td style="text-align:left!important" title="Medios de Verificación">';
       $tabla.=trim($ae['mv_ae']);
       $tabla.='</td>';
       
       $tabla.='<td style="text-align:left!important" title="Supuestos">';
       $tabla.=trim($ae['supuestos_ae']);
       $tabla.='</td>';       
       $tabla.='<td style="text-align:right; padding-right:20px">';
       $tabla.=number_format($ae['total'],2,',','.');       
       $tabla.='</td>';
       
       $total+=$ae['total'];
       
       $tabla.='<td>';         
       $tabla.='</td>';
       $tabla.='</tr>';
    }
    //PIE DE TABLA   
    $tabla.='</tbody>';
    $tabla.='<tfoot>';
    $tabla.='<tr>';
    $tabla.='<td colspan="5" style="text-align:right; ">';
    $tabla.='Presupuesto Total del Proyecto Bs.';
    $tabla.='</td>';
    $tabla.='<td style="text-align:right; padding-right:20px; font-weight:bold">';
    $tabla.=number_format($total,2,',','.');    
    $tabla.='</td>';    
    $tabla.='<td>';    
    $tabla.='</td>';
    $tabla.='</tr>';
    $tabla.='</tfoot>';
    $tabla.='</table><br/><br/>';   

    die($tabla);     
  }  
  
  function revisar_actividad()
  {
      if (!$this->input->is_ajax_request()) die('Acceso Denegado');
      
      $id_actividad= intval($this->input->post('id_actividad'));
      
      $act=$this->Proyectos->obtener_actividad($id_actividad);
      if (!$act)die('error consultando proyecto');
      
      date_default_timezone_set('America/Caracas'); // Establece la Hora de Venezuela para funciones de fecha y hora
      
      $tabla='<table width="100%">';
      $tabla.='<tr>';
      $tabla.='<td width="30px">';    
      $tabla.='</td>';
      $tabla.='<td width="30px" class="BotonIco">';
      $accion=' onclick="javascript:planProyecto('.$act->id_proyecto.');" ';
      $tabla.='<img src="'.base_url().'imagenes/back.png"'.$accion;
      $tabla.='title="clic para regresar"';
      $tabla.='/>'; 
      $tabla.='</td>';
      $tabla.='<td>';
      $tabla.='<strong>PROYECTO: '.trim($act->cod_proy).'&nbsp;-&nbsp;</strong>';    
      $tabla.=trim($act->obj_esp);
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>';
      $tabla.='<td colspan="2">';
      $tabla.='</td>';
      $tabla.='<td><p>';
      $tabla.='<strong>Acción Específica:</strong> '.trim($act->codigo_ae).' - '.trim($act->descripcion_ae);
      $tabla.='</p></td>';
      $tabla.='</tr>';
      $tabla.='</table>';   
      
      $tabla.='<div class="EntraDatos">';
      $tabla.='<table style="margin-top:0px!important">';
      $tabla.='<thead>';
      $tabla.='<tr><th colspan="2">';        
      $tabla.='Actividad Nº: '.$act->codigo_ae.'.'.$act->codigo_act;
      $tabla.='</th></tr>';           
      $tabla.='</thead>';
      $tabla.='<tbody>'; 
      $tabla.='<tr>'; 
      $tabla.='<td>';
      $tabla.='<label>Actividad:</label>';      
      $tabla.=trim($act->descripcion_act);      
      $tabla.='</td>';      
      $tabla.='<td>';            
      $tabla.='<label>Fuente Presupuestaria:</label>'.$act->cod_fuente.' - '.$act->fuente;      
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>';      
      $tabla.='<td width="50%">';
      $tabla.='<label>Unidad de Medida:</label>';      
      $tabla.=trim($act->um_act);      
      $tabla.='</td>';            
      $tabla.='<td>';
      $tabla.='<label>Cantidad:</label>';
      $tabla.=trim($act->cantidad_act);      
      $tabla.='</td>';
      $tabla.='</tr>'; 
      $tabla.='<tr style="border-bottom:#576F89 solid 1px">';      
      $tabla.='<td>';
      $tabla.='<label>Medios de Verificación de la Actividad</label>';      
      $tabla.=trim($act->mv_act);      
      $tabla.='</td>';
      $tabla.='<td>';
      $tabla.='<label>Supuestos</label>';      
      $tabla.=trim($act->supuestos_act);      
      $tabla.='</td>';
      $tabla.='</tr>';      
      $tabla.='<tr>'; 
      $tabla.='<td>';
      $tabla.='<label>Responsable de la Actividad:</label>';      
      $tabla.=trim($act->nombre).' '.trim($act->apellido);
      $tabla.='</td>';
      $tabla.='<td>';
      $tabla.='Desde: '.date("d/m/Y",strtotime($act->fecha_ini)).'<br/>';      
      $tabla.='Hasta: '.date("d/m/Y",strtotime($act->fecha_fin));      
      $tabla.='</td>';
      $tabla.='</tr>';          
      $tabla.='</tbody>';      
      $tabla.='<tfoot>';
      $tabla.='<tr><td colspan="2">';    
      $tabla.='<div class="BotonIco" onclick="javascript:planProyecto('.$act->id_proyecto.');" title="Salir">';
      $tabla.='<img src="imagenes/cancel.png"/>&nbsp;';
      $tabla.='Salir';
      $tabla.= '</div>';
      $tabla.='</td></tr>';
      $tabla.='</tfoot>';
      $tabla.='</table>';
      $tabla.='</div>';
      die ($tabla);
  } 

  function listar_presupuesto()
  {
     if (!$this->input->is_ajax_request()) die('Acceso Denegado');

      $id_actividad= intval($this->input->post('id_actividad'));
      
      $act=$this->Proyectos->obtener_actividad($id_actividad);
      if (!$act)die('error consultando proyecto');
      
      date_default_timezone_set('America/Caracas'); // Establece la Hora de Venezuela para funciones de fecha y hora
      
      $tabla='<table width="100%">';
      $tabla.='<tr>';
      $tabla.='<td width="30px">';    
      $tabla.='</td>';
      $tabla.='<td width="30px" class="BotonIco">';
      $accion=' onclick="javascript:planProyecto('.$act->id_proyecto.');" ';
      $tabla.='<img src="'.base_url().'imagenes/back.png"'.$accion;
      $tabla.='title="clic para regresar"';
      $tabla.='/>'; 
      $tabla.='</td>';
      $tabla.='<td>';
      $tabla.='<strong>PROYECTO: '.trim($act->cod_proy).'&nbsp;-&nbsp;</strong>';    
      $tabla.=trim($act->obj_esp);
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>';
      $tabla.='<td colspan="2">';
      $tabla.='</td>';
      $tabla.='<td>';
      $tabla.='<h4>PLANIFICACIÓN PRESUPUESTARIA</h4>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='</table>'; 
      
      $presupuesto=$this->Proyectos->listar_presupuesto($id_actividad);
      
      if (!$presupuesto) // SI NO HAY PRESUPUESTO PARA ESTA ACTIVIDAD
      { 
        $tabla.='<div id="presupuesto">';  
        $tabla.='<h2><center>- Actividad sin Prespuesto -</center></h2>';        
        $tabla.='</div>';        
        die($tabla);
      }        
      // CONSTRUIMOS LA TABLA 
  
      // ENCABEZADOS DE TABLA
      $tabla.='<div id="presupuesto">';
      $tabla.='<table class="TablaNivel1 Zebrado">';
      $tabla.='<thead>';
      $tabla.='<tr>';
      $tabla.='<th width="100px">';
      $tabla.='Partida';
      $tabla.='</th>';
      $tabla.='<th style="text-align:left">';
      $tabla.='Descripción del Gasto';
      $tabla.='</th>';
      $tabla.='<th width="150px">';
      $tabla.='Unida Medida';
      $tabla.='</th>';
      $tabla.='<th width="60px">';
      $tabla.='Cantidad';
      $tabla.='</th>';
      $tabla.='<th style="text-align:right; width:150px">';        
      $tabla.='Costo Unitario (Bs.)';
      $tabla.='</th>';
      $tabla.='<th style="text-align:right; width:150px">';
      $tabla.='Costo Total (Bs.)';
      $tabla.='</th>';
      $tabla.='<th width="20px">';      
      $tabla.='</th>';
      $tabla.='</tr>';
      $tabla.='</thead>';
      $tabla.='<tbody>';      
      $total=0;
      foreach($presupuesto as $p)
      {
         $tabla.='<tr>';
         $tabla.='<td title="'.$p->denominacion.'">';
         $tabla.=trim($p->id_partida);
         $tabla.='</td>';  
         $tabla.='<td style="text-align:left">';
         $tabla.=trim($p->descripcion_gasto);
         $tabla.='</td>';
         $tabla.='<td>';
         $tabla.=trim($p->um);
         $tabla.='</td>';
         $tabla.='<td>';
         $tabla.=number_format($p->cantidad,2,',','.');
         $tabla.='</td>';
         $tabla.='<td style="text-align:right">';
         $tabla.=number_format($p->costo_unitario,2,',','.');
         $tabla.='</td>';
         $tabla.='<td style="text-align:right;">';
         $tabla.=number_format((($p->cantidad)*($p->costo_unitario)),2,',','.');
         $tabla.='</td>';
         $tabla.='<td>';
         $tabla.='</td>';
         $tabla.='</tr>';
         $total+=($p->cantidad)*($p->costo_unitario);
      }
      $tabla.='</tbody>';
      $tabla.='<tfoot>';
      $tabla.='<tr>';
      $tabla.='<td colspan="5" style="text-align:right">';
      $tabla.='COSTO TOTAL DE LA ACTIVIDAD';
      $tabla.='</td>';
      $tabla.='<td style="text-align:right; font-size:1.2em;"> Bs. ';
      $tabla.=number_format($total,2,',','.');
      $tabla.='</td>';
      $tabla.='<td>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='</tfoot>';
      $tabla.='</table><br/><br/>';      
      $tabla.='</div>';
      die($tabla);      
  }
  
  function _cabecetabla($total=false)
  {    
    $tabla='<thead><tr><th width="40px">Nº</th>';
    $tabla.='<th>Nombre del Sub-Producto</th>';
    $tabla.='<th width="40px" title="Enero">E</th>';
    $tabla.='<th width="40px" title="Febrero">F</th>';
    $tabla.='<th width="40px" title="Marzo">M</th>';
    $tabla.='<th width="40px" title="Abril">A</th>';
    $tabla.='<th width="40px" title="Mayo">M</th>';
    $tabla.='<th width="40px" title="Junio">J</th>';
    $tabla.='<th width="40px" title="Julio">J</th>';
    $tabla.='<th width="40px" title="Agosto">A</th>';
    $tabla.='<th width="40px" title="Septiembre">S</th>';
    $tabla.='<th width="40px" title="Octubre">O</th>';
    $tabla.='<th width="40px" title="Noviembre">N</th>';
    $tabla.='<th width="41px" title="Diciembre">D</th>';
    $tabla.=($total==true)?'<th width="40px" title="Total Anual">Total</th>':'<th width="40px"></th>';     
    $tabla.='</tr></thead>';
    $tabla.='<tfoot><tr>';
    $tabla.='<td >Nº</td>';
    $tabla.='<td >Nombre del Sub-Producto</td>';
    $tabla.='<td title="Enero">E</td>';
    $tabla.='<td title="Febrero">F</td>';
    $tabla.='<td title="Marzo">M</td>';
    $tabla.='<td title="Abril">A</td>';
    $tabla.='<td title="Mayo">M</td>';
    $tabla.='<td title="Junio">J</td>';
    $tabla.='<td title="Julio">J</td>';
    $tabla.='<td title="Agosto">A</td>';
    $tabla.='<td title="Septiembre">S</td>';
    $tabla.='<td title="Octubre">O</td>';
    $tabla.='<td title="Noviembre">N</td>';
    $tabla.='<td title="Diciembre">D</td>';    
    $tabla.=($total==true)?'<td title="Total Anual">Total</td>':'<td></td>';    
    $tabla.='</tr></tfoot>';
    return $tabla;
  }
  
  function _cabecetabla_proy()
  {    
    $tabla='<thead><tr><th width="30px">Nº</th>';
    $tabla.='<th colspan="2">Acción Específica / Actividad</th>';
    $tabla.='<th width="40px" title="Enero">E</th>';
    $tabla.='<th width="40px" title="Febrero">F</th>';
    $tabla.='<th width="40px" title="Marzo">M</th>';
    $tabla.='<th width="40px" title="Abril">A</th>';
    $tabla.='<th width="40px" title="Mayo">M</th>';
    $tabla.='<th width="40px" title="Junio">J</th>';
    $tabla.='<th width="40px" title="Julio">J</th>';
    $tabla.='<th width="40px" title="Agosto">A</th>';
    $tabla.='<th width="40px" title="Septiembre">S</th>';
    $tabla.='<th width="40px" title="Octubre">O</th>';
    $tabla.='<th width="40px" title="Noviembre">N</th>';
    $tabla.='<th width="41px" title="Diciembre">D</th>';
    $tabla.='<th width="40px"></th>';     
    $tabla.='</tr></thead>';
    $tabla.='<tfoot><tr>';
    $tabla.='<td >Nº</td>';
    $tabla.='<td colspan="2">Acción Específica / Actividad</td>';
    $tabla.='<td title="Enero">E</td>';
    $tabla.='<td title="Febrero">F</td>';
    $tabla.='<td title="Marzo">M</td>';
    $tabla.='<td title="Abril">A</td>';
    $tabla.='<td title="Mayo">M</td>';
    $tabla.='<td title="Junio">J</td>';
    $tabla.='<td title="Julio">J</td>';
    $tabla.='<td title="Agosto">A</td>';
    $tabla.='<td title="Septiembre">S</td>';
    $tabla.='<td title="Octubre">O</td>';
    $tabla.='<td title="Noviembre">N</td>';
    $tabla.='<td title="Diciembre">D</td>';    
    $tabla.='<td></td>';    
    $tabla.='</tr></tfoot>';
    return $tabla;
  }
    
}