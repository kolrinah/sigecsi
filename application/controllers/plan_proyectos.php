<?php if (!defined('BASEPATH')) exit('Sin Acceso Directo al Script');      
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *  SISTEMA DE GESTION Y CONTROL DEL SERVICIO INTERNO                *
 *  DESARROLLADO POR: ING.REIZA GARCÍA                               *
 *                    ING.HÉCTOR MARTÍNEZ                            *
 *  PARA:  MINISTERIO DEL PODER POPULAR PARA RELACIONES EXTERIORES   *
 *  FECHA: ENERO DE 2013                                             *
 *  FRAMEWORK PHP UTILIZADO: CodeIgniter Version 2.1.3               *
 *                           http://ellislab.com/codeigniter         *
 *  TELEFONOS PARA SOPORTE: 0416-9052533 / 0212-5153033              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
class Plan_proyectos extends CI_Controller {
  function __construct() 
  {
     parent::__construct();
     $this->load->helper('form');
     $this->load->library('Comunes');
     $this->load->model('Usuarios');
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
    $year_poa=$ahora['year']+1; // El año inicial del POA es el año siguiente al año en curso
    
    $data=array();
    $data['titulo']='Planificación de Proyectos';
    $data['subtitulo']='Planificación Operativa del Año:';
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
                       Actualiza();"
            );
    $data['flecha_sus']= array(
          'src' => base_url().'imagenes/back_enabled.png',
          'alt' => 'Atrás',
          'class' => 'BotonIco',
          'width' => '',
          'height' => '',
          'title' => 'Clic para retroceder',
          'onclick'=> "javascript:document.getElementById('year_poa').value --; Actualiza();"
            ); 
    
    $data['contenido']='plan_proyectos/plan_proyectos';    
    $data['script']='<!-- Cargamos CSS de DataTables -->'."\n";    
    $data['script'].="\t".'<link rel="stylesheet" type="text/css" media="all" href="'.base_url().'css/dataTables.css"/>'."\n";
    $data['script'].='<!-- Cargamos JS para DataTables -->'."\n";
    $data['script'].="\t".'<script type="text/javascript" src="'.base_url().'js/jquery.dataTables.js"></script>'."\n";
    $data['script'].='<!-- Cargamos Nuestro JS -->'."\n";
    $data['script'].="\t".'<script type="text/javascript" src="'.base_url().'js/plan_proyectos.js"></script>'."\n";
       
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
    
    $botoncrear='';
    if (($ahora<$tope) && ($yearpoa>=$fecha['year']))
    {
      $botoncrear='<center><div class="BotonIco" onclick="javascript:CrearProyecto('.$id_estructura.')" title="Nuevo Proyecto">';
      $botoncrear.='<img src="imagenes/faviconPOA.png"/>&nbsp;';   
      $botoncrear.='Crear Proyecto';
      $botoncrear.= '</div></center>';
    }
    
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
      $tabla.=$botoncrear;
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
        $tabla.='<td width="10%" style="vertical-align:middle; text-align:right; padding:0 10px 5px 0">';
        $tope=strtotime($proyectos[$i]['fecha_tope']);
        $ahora=time();        
        $img=($ahora>$tope)?'cerrado.png':'abierto.png';        
        
        $tabla.='<img src="'.base_url().'imagenes/'.$img.'" ';
        $tabla.='title="Fecha Tope: '.date("d/m/Y",strtotime($proyectos[$i]['fecha_tope'])).'"';
        $tabla.='/>';
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
        while(($proyectos[$i]['id_estructura']==$id_estructura))
        {
           $tope=strtotime($proyectos[$i]['fecha_tope']);
           $ahora=time();
           $editable=0;
           if (($ahora<$tope) && ($yearpoa>=$fecha['year']))
           {
              $editable=1;
           }
           $tabla.='<tr class="Resaltado">';
           $tabla.='<td title="Código del Proyecto">';
           $tabla.=trim($proyectos[$i]['cod_proy']);
           $tabla.='</td>'; 
           $accion=' onclick="RevisarFicha('.$proyectos[$i]['id_proyecto'].','.$editable.');" ';
           $tabla.='<td style="text-align:left!important" title="Objetivo Específico del Proyecto"'.$accion.'>';
           $tabla.=trim($proyectos[$i]['obj_esp']);
           $tabla.='</td>'; 
           $tabla.='<td title="Presupuesto Aprobado" style="text-align:right;"'.
                     $accion.'>';
           $tabla.=number_format($proyectos[$i]['monto_aprobado'], 2, ',','.');
           $tabla.='</td>';           
           $color=($proyectos[$i]['total']>0)?' color:black; ':' color:red; ';
           $tabla.='<td title="Presupuesto Planificado" style="'.$color.
                    'text-align:right;">';
           $tabla.=number_format($proyectos[$i]['total'], 2, ',','.');
           $tabla.='</td>';            
           // INDICADOR DE CUADRE PRESUPUESTARIO           
           $tabla.='<td style="text-align:left">';           
           $r=$this->comunes->analisis_proyecto($proyectos[$i]['monto_aprobado'], $proyectos[$i]['total'], $proyectos[$i]['estatus']);           
           $tabla.='<img src="'.base_url().'imagenes/'.$r['img'].'" ';
           $tabla.='title="'.$r['msj'].'" ';
           $tabla.='/>';          
           $tabla.='</td>';
           $tabla.='<td>';
           $accion=' onclick="RevisarFicha('.$proyectos[$i]['id_proyecto'].','.$editable.');" ';
           $img='formulario.png';
           $tabla.='<img src="'.base_url().'imagenes/'.$img.'" ';
           $tabla.='title="Ficha Técnica"'.$accion;
           $tabla.='/>';           
           $tabla.='</td>';
           $tabla.='<td>';
           $accion=' onclick="ListarAcciones('.$proyectos[$i]['id_proyecto'].','.$editable.');" ';
           $img='acciones.png';
           $tabla.='<img src="'.base_url().'imagenes/'.$img.'" ';
           $tabla.='title="Acciones Específicas"'.$accion;
           $tabla.='/>';   
           $tabla.='</td>';
           $tabla.='<td>';
           $accion=' onclick="planProyecto('.$proyectos[$i]['id_proyecto'].','.$editable.');" ';
           $img='gantt20.png';
           $tabla.='<img src="'.base_url().'imagenes/'.$img.'" '.$accion;
           $tabla.='title="Gantt de Actividades"';
           $tabla.='/>';   
           $tabla.='</td>';           
           $tabla.='<td>';
           if ($editable==1)
           {
             $accion=' onclick="EliminarProyecto('.$proyectos[$i]['id_proyecto'].');" ';
             $img='borrar.png';
             $tabla.='<img src="'.base_url().'imagenes/'.$img.'" ';
             $tabla.='title="Eliminar Proyecto"'.$accion;
             $tabla.='/>';   
           }
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
        $tabla.='</table><br/><br/>';   
        if ($i==$n) break;
        $id_estructura=$proyectos[$i]['id_estructura'];                         
    }    
    $tabla.=$botoncrear;
    if ($this->input->is_ajax_request()) die($tabla); // Si la peticion vino por AJAX
    else return $tabla;
  }
  
  function revisar_ficha()
  {
      if (!$this->input->is_ajax_request()) die('Acceso Denegado');
      date_default_timezone_set('America/Caracas'); // Establece la Hora de Venezuela para funciones de fecha y hora
      
      $id_proyecto= intval($this->input->post('id_proyecto'));
      $editable=intval($this->input->post('editable'));
      $readonly=($editable==0)?" readonly='readonly'":'';
      
      $proy=$this->Proyectos->obtener_proyecto($id_proyecto);
      if (!$proy)die('error consultando proyecto');
        
      $tabla='<table width="100%"><tr>';
      $tabla.='<td width="30px" class="BotonIco">';
      $accion=' onclick="javascript:Actualiza();" ';        
      $tabla.='<img src="'.base_url().'imagenes/back.png"'.$accion;
      $tabla.='title="clic para regresar"';
      $tabla.='/>'; 
      $tabla.='</td>';
      $tabla.='<td style="vertical-align:middle; text-align:left">';
      $tabla.='<h4>'.$proy->codigo.' - '.$proy->descripcion.'</h4>';
      $tabla.='</td>';
      $tabla.='<td width="10%" style="vertical-align:middle; text-align:right;">';
      
      $img=($editable==1)?'abierto.png':'cerrado.png';        
        
      $tabla.='<img src="'.base_url().'imagenes/'.$img.'" ';
      $tabla.='title="Fecha Tope: '.date("d/m/Y",strtotime($proy->fecha_tope)).'"';
      $tabla.='/>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='</table>';     
      
      $tabla.='<div class="EntraDatos">';
      $tabla.='<table style="margin-top:0px!important">';
      $tabla.='<thead>';
      $tabla.='<tr><th colspan="2" style="text-align:left">';            
      $tabla.='Ficha Técnica del Proyecto: '.$proy->cod_proy; 
      $tabla.='</th></tr>';           
      $tabla.='</thead>';
      $tabla.='<tbody>'; 
      $tabla.='<tr>'; 
      $tabla.='<td colspan="2">';
      $tabla.='<label>Nombre del Proyecto (Objetivo Específico)</label>';
      $tabla.='<textarea rows="1" class="CampoFicha Editable" id="obj_esp" title="Nombre del Proyecto"'.$readonly.'>';
      $tabla.=$proy->obj_esp;
      $tabla.='</textarea>';
      $tabla.='</td>';
      $tabla.='</tr>';     
      $tabla.='<tr style="border-bottom:#576F89 solid 1px">';
      $tabla.='<td colspan="2">';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>'; 
      $tabla.='<td colspan="3">';
      $tabla.='<label>Objetivo General</label>';
      $tabla.='<textarea rows="1" class="CampoFicha Editable" id="obj_gen" title="Objetivo General"'.$readonly.'>';
      $tabla.=$proy->obj_gen;
      $tabla.='</textarea>';
      $tabla.='</td>';
      $tabla.='</tr>';      
      $tabla.='<tr>';      
      $tabla.='<td colspan="2">';
      $tabla.='<label>Descripción Breve del Proyecto</label>';
      $tabla.='<textarea rows="1" class="CampoFicha Editable" id="descripcion_breve" title="Descripción del Proyecto"'.$readonly.'>';
      $tabla.=$proy->descripcion_breve;
      $tabla.='</textarea>';
      $tabla.='</td>';
      $tabla.='</tr>'; 
      $tabla.='<tr style="border-bottom:#576F89 solid 1px">';
      $tabla.='<td colspan="2">';
      $tabla.='</td>';
      $tabla.='</tr>';      
      $tabla.='<tr>'; 
      $tabla.='<td colspan="2">';
      $tabla.='<label>Enunciado del Problema o Necesidad que Origina el Proyecto</label>';
      $tabla.='<textarea rows="1" class="CampoFicha Editable" id="problema" title="Enunciado del Problema"'.$readonly.'>';
      $tabla.=$proy->problema;
      $tabla.='</textarea>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>'; 
      $tabla.='<td colspan="2">';
      $tabla.='<label>Indicador de la situación actual del problema</label>';
      $tabla.='<textarea rows="1" class="CampoFicha Editable" id="indicador_problema" title="Indicador del Problema"'.$readonly.'>';
      $tabla.=$proy->indicador_problema;
      $tabla.='</textarea>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr style="border-bottom:#576F89 solid 1px">';
      $tabla.='<td colspan="2">';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>'; 
      $tabla.='<td colspan="2">';
      $tabla.='<label>Indicador de la situación Objetivo del Proyecto</label>';
      $tabla.='<textarea rows="1" class="CampoFicha Editable" id="indicador_obj_proy" title="Indicador Objetivo del Proyecto"'.$readonly.'>';
      $tabla.=$proy->indicador_obj_proy;
      $tabla.='</textarea>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>'; 
      $tabla.='<td colspan="2">';
      $tabla.='<label>Resultado del Proyecto</label>';
      $tabla.='<textarea rows="1" class="CampoFicha Editable" id="resultado" title="Resultado del Proyecto"'.$readonly.'>';
      $tabla.=$proy->resultado;
      $tabla.='</textarea>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr style="border-bottom:#576F89 solid 1px">';
      $tabla.='<td colspan="2">';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>'; 
      $tabla.='<td width="50%">';
      $tabla.='<label>Responsable del Proyecto</label>';
      $tabla.='<select id="id_responsable" class="Editable" style="width:90%">';
      $tabla.=$this->_listar_usuarios($proy->id_responsable);
      $tabla.='</select>';
      $tabla.='<div>Unidad: '.$proy->descripcion.'</div>';         
      $tabla.='</td>';      
      $tabla.='<td style="padding-right:35px">';
      $tabla.='<label>Teléfonos de Contacto</label>';
      $tabla.='<textarea rows="1" class="CampoFicha Editable" id="telefonos" title="Teléfonos de Contacto"'.$readonly.'>';
      $tabla.=$proy->telefonos;
      $tabla.='</textarea>';
      $tabla.='<div>Correo: '.$proy->correo.'</div>';  
      $tabla.='</td>';
      $tabla.='</tr>';            
      $tabla.='</tbody>';
      
      $tabla.='<tfoot>';
      $tabla.='<tr><td colspan="3">';
      if ($editable==1)
      {
        $tabla.='<div class="BotonIco" onclick="javascript:ActualizarFicha('.$proy->id_proyecto.')" title="Guardar Cambios">';
        $tabla.='<img src="imagenes/guardar32.png"/>&nbsp;';   
        $tabla.='Guardar';
        $tabla.= '</div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
      }
      $tabla.='<div class="BotonIco" onclick="javascript:Actualiza()" title="Salir">';
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
              'id_responsable'=>$this->input->post('id_responsable'),          
              'obj_esp'=>$this->input->post('obj_esp'),
              'obj_gen'=>$this->input->post('obj_gen'),
              'descripcion_breve'=>$this->input->post('descripcion_breve'),
              'problema'=>$this->input->post('problema'),
              'indicador_problema'=>$this->input->post('indicador_problema'),
              'indicador_obj_proy'=>$this->input->post('indicador_obj_proy'),
              'resultado'=>$this->input->post('resultado'),
              'telefonos'=>$this->input->post('telefonos')
              );        
      $actualizado=$this->Crud->actualizar_registro('p_proyectos', $datos, $donde);        
      if (!$actualizado){die('Error');}
      else 
      {           
         $registro='id_proyecto: '.$donde['id_proyecto'];         
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
  
  function crear_proyecto()
  {
      if (!$this->input->is_ajax_request()) die('Acceso Denegado');
      
      $id_estructura= intval($this->input->post('id_estructura'));      
                  
      $tabla='<div class="EntraDatos">';
      $tabla.='<table style="margin-top:0px!important">';
      $tabla.='<thead>';
      $tabla.='<tr><th colspan="2">';            
      $tabla.='Ficha Técnica del Proyecto';  
      $tabla.='</th></tr>';           
      $tabla.='</thead>';
      $tabla.='<tbody>'; 
      $tabla.='<tr>'; 
      $tabla.='<td colspan="2">';
      $tabla.='<label>Nombre del Proyecto (Objetivo Específico)</label>';
      $tabla.='<textarea rows="1" class="CampoFicha Editable" id="obj_esp" title="Nombre del Proyecto">';
      $tabla.='</textarea>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr style="border-bottom:#576F89 solid 1px">';
      $tabla.='<td colspan="2">';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>'; 
      $tabla.='<td colspan="2">';
      $tabla.='<label>Objetivo General</label>';
      $tabla.='<textarea rows="1" class="CampoFicha Editable" id="obj_gen" title="Objetivo General">';
      $tabla.='</textarea>';
      $tabla.='</td>';
      $tabla.='</tr>';      
      $tabla.='<tr>';
      $tabla.='<td colspan="2">';
      $tabla.='<label>Descripción Breve del Proyecto</label>';
      $tabla.='<textarea rows="1" class="CampoFicha Editable" id="descripcion_breve" title="Descripción Breve del Proyecto">';
      $tabla.='</textarea>';
      $tabla.='</td>';
      $tabla.='</tr>'; 
      $tabla.='<tr style="border-bottom:#576F89 solid 1px">';
      $tabla.='<td colspan="2">';
      $tabla.='</td>';
      $tabla.='</tr>';      
      $tabla.='<tr>'; 
      $tabla.='<td colspan="2">';
      $tabla.='<label>Enunciado del Problema o Necesidad que Origina el Proyecto</label>';
      $tabla.='<textarea rows="1" class="CampoFicha Editable" id="problema" title="Enunciado del Problema">';
      $tabla.='</textarea>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>'; 
      $tabla.='<td colspan="2">';
      $tabla.='<label>Indicador de la situación actual del problema</label>';
      $tabla.='<textarea rows="1" class="CampoFicha Editable" id="indicador_problema" title="Indicador del Problema">';
      $tabla.='</textarea>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr style="border-bottom:#576F89 solid 1px">';
      $tabla.='<td colspan="2">';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>'; 
      $tabla.='<td colspan="2">';
      $tabla.='<label>Indicador de la situación Objetivo del Proyecto</label>';
      $tabla.='<textarea rows="1" class="CampoFicha Editable" id="indicador_obj_proy" title="Indicador Objetivo del Proyecto">';
      $tabla.='</textarea>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>'; 
      $tabla.='<td colspan="2">';
      $tabla.='<label>Resultado del Proyecto</label>';
      $tabla.='<textarea rows="1" class="CampoFicha Editable" id="resultado" title="Resultado del Proyecto">';
      $tabla.='</textarea>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr style="border-bottom:#576F89 solid 1px">';
      $tabla.='<td colspan="2">';
      $tabla.='</td>';
      $tabla.='</tr>';      
      $tabla.='<tr>';
      $tabla.='<td width="50%">';
      $tabla.='<label>Responsable del Proyecto</label>';
      $tabla.='<select id="id_responsable" class="Editable" style="width:90%">';
      $tabla.=$this->_listar_usuarios($this->session->userdata('id_usuario'));
      $tabla.='</select>';
      $tabla.='<div>Unidad: '.$this->session->userdata('nombre_estruct').'</div>';         
      $tabla.='</td>';      
      $tabla.='<td style="padding-right:35px">';
      $tabla.='<label>Teléfonos de Contacto</label>';
      $tabla.='<textarea rows="1" class="CampoFicha Editable" id="telefonos" title="Teléfonos de Contacto">';
      $tabla.='</textarea>';
      $tabla.='<div>Correo: '.$this->session->userdata('correo').'</div>';  
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='</tbody>';
      
      $tabla.='<tfoot>';
      $tabla.='<tr><td colspan="2">';
      
      $tabla.='<div class="BotonIco" onclick="javascript:GuardarProyecto('.$id_estructura.')" title="Guardar Ficha del Proyecto">';
      $tabla.='<img src="imagenes/guardar32.png"/>&nbsp;';   
      $tabla.='Guardar';
      $tabla.= '</div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
      
      $tabla.='<div class="BotonIco" onclick="javascript:Actualiza()" title="Salir">';
      $tabla.='<img src="imagenes/cancel.png"/>&nbsp;';
      $tabla.='Cancelar';
      $tabla.= '</div>';
      $tabla.='</td></tr>';
      $tabla.='</tfoot>';
      $tabla.='</table>';
      $tabla.='</div>';
      die ($tabla);
  }
  
  function guardar_proyecto()
  {   
    if (!$this->input->is_ajax_request()) die('Acceso Denegado');
    
        $datos=array(            
                     'id_estructura'=>$this->input->post('id_estructura'),
                     'yearpoa'=>$this->input->post('yearpoa'),
                     'obj_esp'=>$this->input->post('obj_esp'),
                     'obj_gen'=>$this->input->post('obj_gen'),
                     'descripcion_breve'=>$this->input->post('descripcion_breve'),
                     'problema'=>$this->input->post('problema'),
                     'indicador_problema'=>$this->input->post('indicador_problema'),
                     'indicador_obj_proy'=>$this->input->post('indicador_obj_proy'),
                     'resultado'=>$this->input->post('resultado'),
                     'id_responsable'=>$this->input->post('id_responsable'),
                     'telefonos'=>$this->input->post('telefonos')
                     );              
        
        $insertado=$this->Crud->insertar_registro('p_proyectos', $datos);
        if (!$insertado){die('Error');}
        else
        {
           $registro='id_proyecto: '.$this->db->insert_id();
           $registro.='. '.$datos['obj_esp'];           
           $registro.='. Registrado por: '.$this->session->userdata('usuario');
           $bitacora=array(
               'direccion_ip'   =>$this->session->userdata('ip_address'),
               'navegador'      =>$this->session->userdata('user_agent'),
               'id_usuario'     =>$this->session->userdata('id_usuario'),
               'controlador'    =>$this->uri->uri_string(),
               'tabla_afectada' =>'p_proyectos',
               'tipo_accion'    =>'INSERT',
               'registro'       =>$registro
           );
           $this->Crud->insertar_registro('z_bitacora', $bitacora);             
        }    
  }
  
  function eliminar_proyecto()
  {
    if (!$this->input->is_ajax_request()) die('Acceso Denegado');
    $donde=array(
        'id_proyecto' => intval($this->input->post('id_proyecto'))
                   );        
    $borrado=$this->Crud->eliminar_registro('p_proyectos', $donde);
    if (!$borrado){die('Error');}
    else
    {
       $registro='id_proyecto: '.$donde['id_proyecto'];           
       $registro.='. Borrado por: '.$this->session->userdata('usuario');
       $bitacora=array(
           'direccion_ip'   =>$this->session->userdata('ip_address'),
           'navegador'      =>$this->session->userdata('user_agent'),
           'id_usuario'     =>$this->session->userdata('id_usuario'),
           'controlador'    =>$this->uri->uri_string(),
           'tabla_afectada' =>'p_proyectos',
           'tipo_accion'    =>'DELETE',
           'registro'       =>$registro
       );
       $this->Crud->insertar_registro('z_bitacora', $bitacora); 
    }
  }

  function listar_acciones($id_proyecto=0,$editable=0)
  {
    if ($this->input->is_ajax_request()) 
    {
        $id_proyecto=$this->input->post('id_proyecto'); // Si la peticion vino por AJAX
        $editable=$this->input->post('editable');
    }
    date_default_timezone_set('America/Caracas'); // Establece la Hora de Venezuela para funciones de fecha y hora
    $proyecto=$this->Proyectos->obtener_proyecto($id_proyecto);
    if (!$proyecto)die('error consultando proyecto');
    
    $tabla='<table width="100%"><tr><td style="vertical-align:middle; text-align:left">';
    $tabla.='<h4>'.$proyecto->codigo.' - '.$proyecto->descripcion.'</h4>';
    $tabla.='</td>';
    $tabla.='<td width="10%" style="vertical-align:middle; text-align:right;">';
    
    $img=($editable==1)?'abierto.png':'cerrado.png';        
      
    $tabla.='<img src="'.base_url().'imagenes/'.$img.'" ';
    $tabla.='title="Fecha Tope: '.date("d/m/Y",strtotime($proyecto->fecha_tope)).'"';
    $tabla.='/>';
    $tabla.='</td>';
    $tabla.='</tr>';
    $tabla.='</table>';
    $tabla.='<table width="100%">';
    $tabla.='<tr>';
    $tabla.='<td width="30px" class="BotonIco">';
    $accion=' onclick="javascript:Actualiza();" ';        
    $tabla.='<img src="'.base_url().'imagenes/back.png"'.$accion;
    $tabla.='title="clic para regresar"';
    $tabla.='/>'; 
    $tabla.='</td>';
    $tabla.='<td width="150px" style="vertical-align:top; text-align:right">';
    $tabla.='<strong>PROYECTO: '.$proyecto->cod_proy.'&nbsp;-&nbsp;</strong>';
    $tabla.='</td>';
    $tabla.='<td style="vertical-align:top; text-align:left; padding:0 10px 10px 0">';
    $tabla.=$proyecto->obj_esp;
    $tabla.='</td>';
    $tabla.='</tr>';
    $tabla.='<tr>';
    $tabla.='<td colspan="3">';
    $tabla.='<h4>ACCIONES ESPECÍFICAS</h4>';
    $tabla.='</td>';
    $tabla.='</tr>';
    $tabla.='</table>';
    
    $botoncrear='';
    if ($editable==1)
    {
      $botoncrear='<div class="BotonIco" onclick="javascript:CrearAccion('.$id_proyecto.')" title="Nueva Acción Específica">';
      $botoncrear.='<img src="imagenes/addacciones.png"/>&nbsp;';   
      $botoncrear.='Crear Acción';
      $botoncrear.='</div>';
    }

    $acciones=$this->Proyectos->listar_acciones($id_proyecto);  
    
    if (!$acciones) // SI NO HAY ACCIONES ESPECIFICAS EN EL PROYECTO
    { 
      $tabla.='<h2><center>- Proyecto sin Acciones Específicas -</center></h2>';      
      $tabla.='<center>'.$botoncrear.'</center>';
      if ($this->input->is_ajax_request()) die($tabla); // Si la peticion vino por AJAX
      else return $tabla;
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
    $tabla.='<th style="width:130px; text-align:left">';
    $tabla.='Supuestos';
    $tabla.='</th>';
    $tabla.='<th style="width:120px; text-align:right; padding-right:10px">';
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
       $accion=' onclick="RevisarAccion('.$ae['id_accion'].','.$editable.');" ';
       
       $tabla.='<td style="text-align:left!important" title="Acción Específica"'.$accion.'>';
       $tabla.=trim($ae['descripcion_ae']);
       $tabla.='</td>';
              
       $tabla.='<td style="text-align:left!important" title="Indicadores Objetivamente Verificables"'.$accion.'>';
       $tabla.=trim($ae['iov_ae']);
       $tabla.='</td>';
       
       $tabla.='<td style="text-align:left!important" title="Medios de Verificación"'.$accion.'>';
       $tabla.=trim($ae['mv_ae']);
       $tabla.='</td>';
       
       $tabla.='<td style="text-align:left!important" title="Supuestos"'.$accion.'>';
       $tabla.=trim($ae['supuestos_ae']);
       $tabla.='</td>';       
       $tabla.='<td style="text-align:right; padding-right:20px">';
       $tabla.=number_format($ae['total'],2,',','.');       
       $tabla.='</td>';
       
       $total+=$ae['total'];
       
       $tabla.='<td class="CeldaIconos">';
       if ($editable==1)
       {
         $accion=' onclick="EditarAccion('.$ae['id_accion'].');" ';
         $img='accionedit.png';
         $tabla.='<img src="'.base_url().'imagenes/'.$img.'" class="BotonIco" ';
         $tabla.='title="Editar Acción Específica"'.$accion;
         $tabla.='/>';
         $tabla.='<br/>';
       }
       
       $accion=' onclick="ListarActividades('.$ae['id_accion'].','.$editable.');" ';
       $img='listaractividades.png';
       $tabla.='<img src="'.base_url().'imagenes/'.$img.'" class="BotonIco" ';
       $tabla.='title="Listar Actividades"'.$accion;
       $tabla.='/>';   
         
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
  
    $botoncrear.='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
    $botoncrear.='<div class="BotonIco" onclick="javascript:planProyecto('.$id_proyecto.','.$editable.')" title="Programar Actividades">';
    $botoncrear.='<img src="imagenes/programacion.png"/>&nbsp;';   
    $botoncrear.='Programar';
    $botoncrear.='</div>';
    
    $tabla.='<center>'.$botoncrear.'</center>';

    if ($this->input->is_ajax_request()) die($tabla); // Si la peticion vino por AJAX
    else return $tabla;
  }  

  function editar_accion()
  {
      if (!$this->input->is_ajax_request()) die('Acceso Denegado');
      
      $id_accion= intval($this->input->post('id_accion'));
            
      $ae=$this->Proyectos->obtener_accion($id_accion);
      if (!$ae)die('error consultando acción específica');
      
      date_default_timezone_set('America/Caracas'); // Establece la Hora de Venezuela para funciones de fecha y hora
      
      $tabla='<table width="100%"><tr><td style="vertical-align:middle; text-align:left">';
      $tabla.='<h4>'.$ae->codigo.' - '.$ae->descripcion.'</h4>';
      $tabla.='</td>';
      $tabla.='<td width="10%" style="vertical-align:middle; text-align:right;">';
      
      $img='abierto.png';        
        
      $tabla.='<img src="'.base_url().'imagenes/'.$img.'" ';
      $tabla.='title="Fecha Tope: '.date("d/m/Y",strtotime($ae->fecha_tope)).'"';
      $tabla.='/>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='</table>';
      $tabla.='<table width="100%">';
      $tabla.='<tr>';
      $tabla.='<td width="30px" class="BotonIco">';
      $accion=' onclick="javascript:ListarAcciones('.$ae->id_proyecto.',1);" ';        
      $tabla.='<img src="'.base_url().'imagenes/back.png"'.$accion;
      $tabla.='title="clic para regresar"';
      $tabla.='/>'; 
      $tabla.='</td>';
      $tabla.='<td width="150px" style="vertical-align:top; text-align:right">';
      $tabla.='<strong>PROYECTO: '.$ae->cod_proy.'&nbsp;-&nbsp;</strong>';
      $tabla.='</td>';
      $tabla.='<td style="vertical-align:top; text-align:left; padding:0 10px 10px 0">';
      $tabla.=$ae->obj_esp;
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='</table>';     
             
      $tabla.='<div class="EntraDatos">';
      $tabla.='<table style="margin-top:0px!important">';
      $tabla.='<thead>';
      $tabla.='<tr><th colspan="2">';            
      $tabla.='Acción Específica Nº: '.$ae->codigo_ae;  
      $tabla.='</th></tr>';           
      $tabla.='</thead>';
      $tabla.='<tbody>'; 
      $tabla.='<tr>'; 
      $tabla.='<td colspan="2">';
      $tabla.='<label>Nº:</label>';
      $tabla.='<input type="text" class="Editable" id="codigo_ae" title="Nº de la Acción Específica" value="';
      $tabla.=$ae->codigo_ae.'" maxlength="3" size="3" style="text-align:center"';   
      //onlyDigits(e, value, allowDecimal, allowNegative, allowThousand, decSep, thousandSep, decLength)
      $tabla.=' onblur="this.value=formatNumber(this.value,0);"';
      $tabla.=' onkeyup="formatNumber(this.value,0);" ';
      $tabla.=' onkeypress="return onlyDigits(event, this.value, false,false,false,\',\',\'.\',0);" ';
      
      $tabla.=' /><br/>';
      
      $tabla.='<label>Nombre de la Acción Específica (Bien o Servicio):</label>';
      $tabla.='<textarea rows="2" class="CampoFicha Editable" id="descripcion_ae" title="Acción Específica">';
      $tabla.=$ae->descripcion_ae;
      $tabla.='</textarea>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>';
      $tabla.='<td width="1px">';      
      $tabla.='</td>';
      $tabla.='<td>';
      $tabla.='<label>Indicadores Objetivamente Verificables</label>';
      $tabla.='<textarea rows="2" class="CampoFicha Editable" id="iov_ae" title="Indicadores de la Acción Específica">';
      $tabla.=$ae->iov_ae;
      $tabla.='</textarea>';
      $tabla.='</td>';
      $tabla.='</tr>'; 
      $tabla.='<tr>';
      $tabla.='<td width="1px">';      
      $tabla.='</td>';
      $tabla.='<td>';
      $tabla.='<label>Medios de Verificación de los Indicadores</label>';
      $tabla.='<textarea rows="2" class="CampoFicha Editable" id="mv_ae" title="Medios de Verificación de la Acción Específica">';
      $tabla.=$ae->mv_ae;
      $tabla.='</textarea>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>';
      $tabla.='<td width="1px">';
      $tabla.='</td>';
      $tabla.='<td>';
      $tabla.='<label>Supuestos</label>';
      $tabla.='<textarea rows="2" class="CampoFicha Editable" id="supuestos_ae" title="Supuestos de la Acción Específica">';
      $tabla.=$ae->supuestos_ae;
      $tabla.='</textarea>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr style="border-bottom:#576F89 solid 1px">';
      $tabla.='<td colspan="2">';
      $tabla.='</td>';
      $tabla.='</tr>';
      
      $tabla.='</tbody>';
      
      $tabla.='<tfoot>';
      $tabla.='<tr><td colspan="2">';
      $tabla.='<div class="BotonIco" onclick="javascript:EliminarAccion('.$ae->id_accion.','.$ae->id_proyecto.')" title="Eliminar Acción Específica">';
      $tabla.='<img src="imagenes/delacciones.png"/>&nbsp;';   
      $tabla.='Eliminar';
      $tabla.= '</div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';  
      
      $tabla.='<div class="BotonIco" onclick="javascript:ActualizarAccion('.$ae->id_accion.','.$ae->id_proyecto.')" title="Guardar Cambios">';
      $tabla.='<img src="imagenes/guardar32.png"/>&nbsp;';   
      $tabla.='Guardar';
      $tabla.= '</div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

      $tabla.='<div class="BotonIco" onclick="javascript:ListarAcciones('.$ae->id_proyecto.',1);" title="Salir">';
      $tabla.='<img src="imagenes/cancel.png"/>&nbsp;';
      $tabla.='Cancelar';
      $tabla.= '</div>';
      $tabla.='</td></tr>';
      $tabla.='</tfoot>';
      $tabla.='</table>';
      $tabla.='</div>';
      die ($tabla);
  }
  
  function actualizar_accion()
  {
      if (!$this->input->is_ajax_request()) die('Acceso Denegado');
      
      $donde=array(
                'id_accion'  => intval($this->input->post('id_accion'))
                  );
      $datos=array(              
              'descripcion_ae'=>$this->input->post('descripcion_ae'),
              'mv_ae'=>$this->input->post('mv_ae'),
              'iov_ae'=>$this->input->post('iov_ae'),
              'supuestos_ae'=>$this->input->post('supuestos_ae'),
              'codigo_ae'=>str_ireplace(",",".",str_ireplace(".","",$this->input->post('codigo_ae'))) 
              );        
      $actualizado=$this->Crud->actualizar_registro('p_acciones', $datos, $donde);        
      if (!$actualizado){die('Error');}
      else 
      {           
         $registro='id_accion: '.$donde['id_accion'];         
         $registro.='. Actualizado por: '.$this->session->userdata('usuario');
         $bitacora=array(
             'direccion_ip'   =>$this->session->userdata('ip_address'),
             'navegador'      =>$this->session->userdata('user_agent'),
             'id_usuario'     =>$this->session->userdata('id_usuario'),
             'controlador'    =>$this->uri->uri_string(),
             'tabla_afectada' =>'p_acciones',
             'tipo_accion'    =>'UPDATE',
             'registro'       =>$registro
         );
         $this->Crud->insertar_registro('z_bitacora', $bitacora);            
      };   
  }
  
  function eliminar_accion()
  {
    if (!$this->input->is_ajax_request()) die('Acceso Denegado');
    $donde=array(
        'id_accion' => intval($this->input->post('id_accion'))
                   );        
    $borrado=$this->Crud->eliminar_registro('p_acciones', $donde);
    if (!$borrado){die('Error');}
    else
    {
       $registro='id_accion: '.$donde['id_accion'];           
       $registro.='. Borrado por: '.$this->session->userdata('usuario');
       $bitacora=array(
           'direccion_ip'   =>$this->session->userdata('ip_address'),
           'navegador'      =>$this->session->userdata('user_agent'),
           'id_usuario'     =>$this->session->userdata('id_usuario'),
           'controlador'    =>$this->uri->uri_string(),
           'tabla_afectada' =>'p_acciones',
           'tipo_accion'    =>'DELETE',
           'registro'       =>$registro
       );
       $this->Crud->insertar_registro('z_bitacora', $bitacora); 
    }
  }
  
  function crear_accion()
  {
      if (!$this->input->is_ajax_request()) die('Acceso Denegado');
      
      $id_proyecto= intval($this->input->post('id_proyecto'));
      
      $nro_acciones=$this->Crud->contar_items('p_acciones', array('id_proyecto' => $id_proyecto))+1;
      
      $proy=$this->Proyectos->obtener_proyecto($id_proyecto);
      if (!$proy)die('error consultando proyecto');
      
      date_default_timezone_set('America/Caracas'); // Establece la Hora de Venezuela para funciones de fecha y hora
      
      $tabla='<table width="100%"><tr><td style="vertical-align:middle; text-align:left">';
      $tabla.='<h4>'.$proy->codigo.' - '.$proy->descripcion.'</h4>';
      $tabla.='</td>';
      $tabla.='<td width="10%" style="vertical-align:middle; text-align:right;">';
      
      $img='abierto.png';        
        
      $tabla.='<img src="'.base_url().'imagenes/'.$img.'" ';
      $tabla.='title="Fecha Tope: '.date("d/m/Y",strtotime($proy->fecha_tope)).'"';
      $tabla.='/>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='</table>';
      $tabla.='<table width="100%">';
      $tabla.='<tr>';
      $tabla.='<td width="30px" class="BotonIco">';
      $accion=' onclick="javascript:ListarAcciones('.$proy->id_proyecto.',1);" ';        
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
      $tabla.='</table>';     
             
      $tabla.='<div class="EntraDatos">';
      $tabla.='<table style="margin-top:0px!important">';
      $tabla.='<thead>';
      $tabla.='<tr><th colspan="2">'; 
       
      $tabla.='Creación de Acción Específica Nº: ';
      $tabla.=$nro_acciones;
      $tabla.='</th></tr>';           
      $tabla.='</thead>';
      $tabla.='<tbody>'; 
      $tabla.='<tr>'; 
      $tabla.='<td colspan="2">';      
      $tabla.='<input type="hidden" id="codigo_ae" value="';
      $tabla.=$nro_acciones;
      $tabla.='"/>';      
      $tabla.='<label>Nombre de la Acción Específica (Bien o Servicio):</label>';
      $tabla.='<textarea rows="2" class="CampoFicha Editable" id="descripcion_ae" title="Acción Específica">';      
      $tabla.='</textarea>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>';
      $tabla.='<td width="1px">';      
      $tabla.='</td>';
      $tabla.='<td>';
      $tabla.='<label>Indicadores Objetivamente Verificables</label>';
      $tabla.='<textarea rows="2" class="CampoFicha Editable" id="iov_ae" title="Indicadores de la Acción Específica">';
      $tabla.='</textarea>';
      $tabla.='</td>';
      $tabla.='</tr>'; 
      $tabla.='<tr>';
      $tabla.='<td width="1px">';      
      $tabla.='</td>';
      $tabla.='<td>';
      $tabla.='<label>Medios de Verificación de los Indicadores</label>';
      $tabla.='<textarea rows="2" class="CampoFicha Editable" id="mv_ae" title="Medios de Verificación de la Acción Específica">';
      $tabla.='</textarea>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>';
      $tabla.='<td width="1px">';
      $tabla.='</td>';
      $tabla.='<td>';
      $tabla.='<label>Supuestos</label>';
      $tabla.='<textarea rows="2" class="CampoFicha Editable" id="supuestos_ae" title="Supuestos de la Acción Específica">';
      $tabla.='</textarea>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr style="border-bottom:#576F89 solid 1px">';
      $tabla.='<td colspan="2">';
      $tabla.='</td>';
      $tabla.='</tr>';
      
      $tabla.='</tbody>';
      
      $tabla.='<tfoot>';
      $tabla.='<tr><td colspan="2">';
     
      $tabla.='<div class="BotonIco" onclick="javascript:GuardarAccion('.$proy->id_proyecto.')" title="Guardar Acción Específica">';
      $tabla.='<img src="imagenes/guardar32.png"/>&nbsp;';   
      $tabla.='Guardar';
      $tabla.= '</div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

      $tabla.='<div class="BotonIco" onclick="javascript:ListarAcciones('.$proy->id_proyecto.',1);" title="Salir">';
      $tabla.='<img src="imagenes/cancel.png"/>&nbsp;';
      $tabla.='Cancelar';
      $tabla.= '</div>';
      $tabla.='</td></tr>';
      $tabla.='</tfoot>';
      $tabla.='</table>';
      $tabla.='</div>';
      die ($tabla);
  } 
 
  function guardar_accion()
  {   
     if (!$this->input->is_ajax_request()) die('Acceso Denegado');
    
     $datos=array(
              'id_proyecto' =>$this->input->post('id_proyecto'),
              'descripcion_ae'=>$this->input->post('descripcion_ae'),
              'mv_ae'=>$this->input->post('mv_ae'),
              'iov_ae'=>$this->input->post('iov_ae'),
              'supuestos_ae'=>$this->input->post('supuestos_ae'),
              'codigo_ae'=>$this->input->post('codigo_ae')
              );              
        
     $insertado=$this->Crud->insertar_registro('p_acciones', $datos);
     if (!$insertado){die('Error');}
     else
     {
        $registro='id_accion: '.$this->db->insert_id();
        $registro.='. '.$datos['obj_esp'];           
        $registro.='. Registrado por: '.$this->session->userdata('usuario');
        $bitacora=array(
            'direccion_ip'   =>$this->session->userdata('ip_address'),
            'navegador'      =>$this->session->userdata('user_agent'),
            'id_usuario'     =>$this->session->userdata('id_usuario'),
            'controlador'    =>$this->uri->uri_string(),
            'tabla_afectada' =>'p_acciones',
            'tipo_accion'    =>'INSERT',
            'registro'       =>$registro
        );
        $this->Crud->insertar_registro('z_bitacora', $bitacora);             
     }    
  }  
  
  function listar_actividades($id_accion=0,$editable=0)
  {
    if ($this->input->is_ajax_request()) 
    {
        $id_accion=$this->input->post('id_accion'); // Si la peticion vino por AJAX
        $editable=$this->input->post('editable');
    }
    date_default_timezone_set('America/Caracas'); // Establece la Hora de Venezuela para funciones de fecha y hora
    $ae=$this->Proyectos->obtener_accion($id_accion);
    if (!$ae)die('error consultando proyecto');
    
    $tabla='<table width="100%"><tr><td style="vertical-align:middle; text-align:left">';
    $tabla.='<h4>'.$ae->codigo.' - '.$ae->descripcion.'</h4>';
    $tabla.='</td>';
    $tabla.='<td width="10%" style="vertical-align:middle; text-align:right;">';
    
    $img=($editable==1)?'abierto.png':'cerrado.png';        
      
    $tabla.='<img src="'.base_url().'imagenes/'.$img.'" ';
    $tabla.='title="Fecha Tope: '.date("d/m/Y",strtotime($ae->fecha_tope)).'"';
    $tabla.='/>';
    $tabla.='</td>';
    $tabla.='</tr>';
    $tabla.='</table>';
    $tabla.='<table width="100%">';
    $tabla.='<tr>';
    $tabla.='<td width="30px" class="BotonIco">';
    $accion=' onclick="javascript:ListarAcciones('.$ae->id_proyecto.','.$editable.');" ';
    $tabla.='<img src="'.base_url().'imagenes/back.png"'.$accion;
    $tabla.='title="clic para regresar"';
    $tabla.='/>'; 
    $tabla.='</td>';
    $tabla.='<td width="150px" style="vertical-align:top; text-align:right">';
    $tabla.='<strong>PROYECTO: '.$ae->cod_proy.'&nbsp;-&nbsp;</strong>';
    $tabla.='</td>';
    $tabla.='<td style="vertical-align:top; text-align:left; padding:0 10px 10px 0">';
    $tabla.=$ae->obj_esp;
    $tabla.='</td>';
    $tabla.='</tr>';
    $tabla.='<tr>';
    $tabla.='<td>';
    $tabla.='</td>';
    $tabla.='<td style="vertical-align:top; text-align:right">';
    $tabla.='<strong>Acción Específica Nº&nbsp;';
    $tabla.=$ae->codigo_ae.'&nbsp;-</strong>&nbsp;';
    $tabla.='</td>';
    $tabla.='<td>';
    $tabla.=$ae->descripcion_ae;
    $tabla.='</td>';
    $tabla.='</tr>';
    $tabla.='<tr>';
    $tabla.='<td colspan="3">';
    $tabla.='<h4>ACTIVIDADES</h4>';
    $tabla.='</td>';
    $tabla.='</tr>';
    $tabla.='</table>';
    
    $botoncrear='';
    if ($editable==1)
    {
      $botoncrear='<center><div class="BotonIco" onclick="javascript:ProgramarActividad('.$id_accion.')" title="Nueva Actividad">';
      $botoncrear.='<img src="imagenes/addactividad.png"/>&nbsp;';   
      $botoncrear.='Crear Actividad';
      $botoncrear.= '</div></center>';
    }

    $actividades=$this->Proyectos->listar_actividades($id_accion);
    
    if (!$actividades) // SI NO HAY ACTIVIDADES PARA LA ACCION ESPECIFICA DEL PROYECTO
    { 
      $tabla.='<h2><center>- Acciones Específicas sin Actividades -</center></h2>';
      $tabla.=$botoncrear;
      if ($this->input->is_ajax_request()) die($tabla); // Si la peticion vino por AJAX
      else return $tabla;
    }  
    
    // CONSTRUIMOS LA TABLA 

    // ENCABEZADOS DE TABLA
    $tabla.='<table class="TablaNivel1 Zebrado">';
    $tabla.='<thead>';
    $tabla.='<tr>';
    $tabla.='<th width="40px">';
    $tabla.='Nº';
    $tabla.='</th>';
    $tabla.='<th>';
    $tabla.='Actividad';
    $tabla.='</th>';
    $tabla.='<th style="width:60px; text-align:center">';
    $tabla.='Cantidad';
    $tabla.='</th>';
    $tabla.='<th style="width:120px; text-align:center">';        
    $tabla.='Unidad Medida';
    $tabla.='</th>';
    $tabla.='<th style="width:150px; text-align:left">';
    $tabla.='Medios de Verificación';
    $tabla.='</th>';
    $tabla.='<th style="width:100px; text-align:left">';
    $tabla.='Supuestos';
    $tabla.='</th>';
    $tabla.='<th style="width:130px; text-align:right; padding-right:10px">';
    $tabla.='Presupuesto(Bs.)';
    $tabla.='</th>';    
    $tabla.='<th width="30px">';    
    $tabla.='</th>';
    $tabla.='</tr>';
    $tabla.='</thead>';
    $tabla.='<tbody>';
    
    $total=0;
    foreach($actividades as $ae)
    {
       $tabla.='<tr>';
       $tabla.='<td title="Nº de Actividad">';
       $tabla.=trim($ae['codigo_ae']).'.'.trim($ae['codigo_act']);
       $tabla.='</td>';
       
       $accion=' onclick="RevisarActividad('.$ae['id_actividad'].','.$editable.');" ';      
       
       $tabla.='<td style="text-align:left!important" title="Acción Específica"'.$accion.'>';
       $tabla.=trim($ae['descripcion_act']);
       $tabla.='</td>';
              
       $tabla.='<td style="text-align:center" title="Cantidad"'.$accion.'>';
       $tabla.=trim($ae['cantidad_act']);
       $tabla.='</td>';

       $tabla.='<td style="text-align:center" title="Unidad de Medida"'.$accion.'>';
       $tabla.=trim($ae['um_act']);
       $tabla.='</td>';
       
       $tabla.='<td style="text-align:left!important" title="Medios de Verificación"'.$accion.'>';
       $tabla.=trim($ae['mv_act']);
       $tabla.='</td>';
       
       $tabla.='<td style="text-align:left!important" title="Supuestos"'.$accion.'>';
       $tabla.=trim($ae['supuestos_act']);
       $tabla.='</td>';
       
       $tabla.='<td style="text-align:right; padding-right:20px">';
       $tabla.=number_format($ae['total'],2,',','.');       
       $tabla.='</td>';
       
       $total+=$ae['total'];
       
       $tabla.='<td class="CeldaIconos">';
       if ($editable==1)
       {
         $accion=' onclick="EditarActividad('.$ae['id_actividad'].');" ';
         $img='editactividad.png';
         $tabla.='<img src="'.base_url().'imagenes/'.$img.'" class="BotonIco" ';
         $tabla.='title="Editar Actividad"'.$accion;
         $tabla.='/>';
       }     
         
       $tabla.='</td>';
       $tabla.='</tr>';
    }
    $tabla.='</tbody>';
    //PIE DE TABLA        
    $tabla.='<tfoot>';
    $tabla.='<tr>';
    $tabla.='<td colspan="6" style="text-align:right;">';
    $tabla.='Presupuesto Total de la Acción Específica Bs.';
    $tabla.='</td>';
    $tabla.='<td style="text-align:right; padding-right:20px; font-weight:bold">';
    $tabla.=number_format($total,2,',','.');  
    $tabla.='<td>';    
    $tabla.='</td>';
    $tabla.='</tr>';
    $tabla.='</tfoot>';
    $tabla.='</table><br/><br/>';   
  
    $tabla.=$botoncrear;

    if ($this->input->is_ajax_request()) die($tabla); // Si la peticion vino por AJAX
    else return $tabla;
  }  
  
  function plan_proyecto()
  {
    if (!$this->input->is_ajax_request())die('Sin Acceso al Script');// Si la peticion No es AJAX
    
    $id_proyecto=$this->input->post('id_proyecto');
    $editable=$this->input->post('editable');
     
    date_default_timezone_set('America/Caracas');      
    $plan=$this->Proyectos->obtener_proyecto($id_proyecto);
    if (!$plan) die('ERROR');

    // DATOS DE LA ESTRUCTURA
    $tabla='<table width="100%"><tr><td style="vertical-align:middle; text-align:left">';
    $tabla.='<h4>'.$plan->codigo.' - '.$plan->descripcion.'</h4>';
    $tabla.='</td>';
    $tabla.='<td width="10%" style="vertical-align:middle; text-align:right;">';
    
    $img=($editable==1)?'abierto.png':'cerrado.png';        
      
    $tabla.='<img src="'.base_url().'imagenes/'.$img.'" ';
    $tabla.='title="Fecha Tope: '.date("d/m/Y",strtotime($plan->fecha_tope)).'"';
    $tabla.='/>';
    $tabla.='</td>';
    $tabla.='</tr>';
    $tabla.='</table>';
    
    // DATOS DEL PROYECTO
    $tabla.='<table width="100%">';
    $tabla.='<tr>';
    $tabla.='<td width="30px" class="BotonIco">';
    $accion=' onclick="javascript:ListarAcciones('.$plan->id_proyecto.','.$editable.');" ';        
    $tabla.='<img src="'.base_url().'imagenes/back.png"'.$accion;
    $tabla.='title="clic para regresar"';
    $tabla.='/>'; 
    $tabla.='</td>';
    $tabla.='<td width="150px" style="vertical-align:top; text-align:right">';
    $tabla.='<strong>PROYECTO: '.$plan->cod_proy.'&nbsp;-&nbsp;</strong>';
    $tabla.='</td>';
    $tabla.='<td style="vertical-align:top; text-align:left; padding:0 10px 10px 0">';
    $tabla.=$plan->obj_esp;
    $tabla.='</td>';
    $tabla.='</tr>';
    $tabla.='</table>';
    $tabla.='<input type="hidden" id="idProyecto" value="'.$plan->id_proyecto.'" />';
    $tabla.='<input type="hidden" id="editable" value="'.$editable.'" />';    
    
    $tabla.='<table>';
    $tabla.='<tr>';    
    $tabla.='<td>';
    $tabla.='<h4>PLANIFICACIÓN DE ACTIVIDADES</h4>';
    $tabla.='</td>';
    
    $g=$this->session->userdata('gantt')?' checked="checked" ':'';
    $o=' onchange="actualizaGantt($(this).attr(\'checked\'));" ';
    $tabla.='<td style="text-align:center;">';
    $tabla.='<input type="checkbox" id="botonGantt" '.$g.$o.' />';
    $tabla.='<label for="botonGantt" title="clic para cambiar">Gantt</label>';    
    $tabla.='</td>';    
    $tabla.='</tr>';
    $tabla.='</table>';
    $tabla.= ($this->session->userdata('gantt'))?$this->_gantt_proyecto($id_proyecto,$editable):
                                                 $this->_tabla_proyecto($id_proyecto);
    
    die($tabla);
  }
  
  function _tabla_proyecto($id_proyecto)
  {
     date_default_timezone_set('America/Caracas');      
     $plan=$this->Proyectos->tabla_proyecto($id_proyecto);
     if (!$plan)
     {
       $tabla='<center><h2>- Proyecto sin Actividades Programadas -</h2></center>';
       return $tabla;
     }
     $tabla='<table class="TablaNivel1 Zebrado">';
     $tabla.=$this->comunes->cabecetablatotal();
     $tabla.='<tbody>';
     foreach ($plan as $p)
     {
        $tabla.='<tr class="Resaltado">';
        $tabla.='<td>';
        $tabla.=$p->codigo_ae.'.'.$p->codigo_act;
        $tabla.='</td>';
        $tabla.='<td colspan="2" style="text-align:left" title="Acción Específica Nº '.
                $p->codigo_ae.' '.$p->descripcion_ae.'">';
        $tabla.='<strong>'.$p->descripcion_act.'</strong><br/>Unidad de Medida: '.$p->um_act;
        $tabla.='</td>';
        
        $tabla.='<td style="vertical-align:middle" title="Enero">'.$p->ene.'</td>';
        $tabla.='<td style="vertical-align:middle"  title="Febrero">'.$p->feb.'</td>';
        $tabla.='<td style="vertical-align:middle"  title="Marzo">'.$p->mar.'</td>';
        $tabla.='<td style="vertical-align:middle"  title="Abril">'.$p->abr.'</td>';
        $tabla.='<td style="vertical-align:middle"  title="Mayo">'.$p->may.'</td>';
        $tabla.='<td style="vertical-align:middle"  title="Junio">'.$p->jun.'</td>';
        $tabla.='<td style="vertical-align:middle"  title="Julio">'.$p->jul.'</td>';
        $tabla.='<td style="vertical-align:middle"  title="Agosto">'.$p->ago.'</td>';
        $tabla.='<td style="vertical-align:middle"  title="Septiembte">'.$p->sep.'</td>';
        $tabla.='<td style="vertical-align:middle"  title="Octubre">'.$p->oct.'</td>';
        $tabla.='<td style="vertical-align:middle"  title="Noviembre">'.$p->nov.'</td>';
        $tabla.='<td style="vertical-align:middle"  title="Diciembre">'.$p->dic.'</td>';
        $suma=$p->ene+$p->feb+$p->mar+$p->abr+$p->may+$p->jun+
              $p->jul+$p->ago+$p->sep+$p->oct+$p->nov+$p->dic;
        $tabla.='<td style="vertical-align:middle"  title="Total">'.$suma.'</td>';
        $tabla.='</tr>';
     }
     $tabla.='</tbody>';
     $tabla.='</table>';
     return $tabla;
  }
  
  function _gantt_proyecto($id_proyecto=0,$editable=0)
  {
  // PLANIFICACION DE ACTIVIDADES
    date_default_timezone_set('America/Caracas');      
    $plan=$this->Proyectos->gantt_proyecto($id_proyecto);
    if (!$plan)
    {
       $tabla='<center><h2>- Proyecto sin Actividades Programadas -</h2></center>';
       return $tabla;
    }
    
    $tabla='<table class="TablaNivel1 Zebrado">';
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
      if ($editable==1)
      {
        $accion=' onclick="ProgramarActividad('.$plan[$i]->id_accion.');" ';
        $img='nvaactividad.png';
        $tabla.='<img src="'.base_url().'imagenes/'.$img.'" class="BotonIco" ';
        $tabla.='title="Programar Actividad"'.$accion;
        $tabla.='/>';
      }
      
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
           $tabla.='Presupuesto: ';
           $tabla.='<span style="color:'.(($plan[$i]->total>0)?'black':'red').'">Bs. ';
           $tabla.=number_format($plan[$i]->total,2,',','.');
           $tabla.='</span> / <span title="Fuente Presupuestaria: '.
                            $plan[$i]->fuente.'">Fuente: '.$plan[$i]->cod_fuente.'</span>';
           $tabla.='</div>';
           $tabla.='<div>Responsable: '.trim($plan[$i]->nombre).' '.trim($plan[$i]->apellido).' /';
           $tabla.=' Duración: ';
           $tabla.=($duracion<1)?1:$duracion;
           $tabla.=($duracion<2)?' día ':' días ';
           $tabla.='</div>';
           $tabla.='</td>';
           
           $gantt=$this->comunes->mini_gantt($plan[$i]->fecha_ini, $plan[$i]->fecha_fin, 40);
           $titulo=' title="Desde: ';
           $titulo.=date("d/m/Y",strtotime($plan[$i]->fecha_ini));
           $titulo.=' Hasta: ';
           $titulo.=date("d/m/Y",strtotime($plan[$i]->fecha_fin));
           $titulo.='" ';
           
           $tabla.='<td colspan="12" class="Cuadricula" '.$titulo.' style="cursor:pointer" 
                     onclick="EditarActividad('.$plan[$i]->id_actividad.');">';           
           $tabla.='<div class="'.(($plan[$i]->total>0)?'gantt'.$plan[$i]->cod_fuente:'ganttPobre').'">'.$gantt.'</div>';           
           $tabla.='</td>';
           
           $tabla.='<td style="vertical-align:middle">';
           $accion=' onclick="listarPresupuesto('.$plan[$i]->id_actividad.','.$editable.');" ';
           $img='money.png';
           $tabla.='<img src="'.base_url().'imagenes/'.$img.'" class="BotonIco" ';
           $tabla.='title="Planificación Presupuestaria"'.$accion;
           $tabla.='/>';
                    
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
    
    return $tabla;
  }
  
  function programar_actividad()
  {
      if (!$this->input->is_ajax_request()) die('Acceso Denegado');
      
      $id_accion= intval($this->input->post('id_accion'));
      
      $nro_actividad=$this->Crud->contar_items('p_actividades', array('id_accion' => $id_accion))+1;
      
      $ae=$this->Proyectos->obtener_accion($id_accion);
      if (!$ae)die('error consultando proyecto');
      
      date_default_timezone_set('America/Caracas'); // Establece la Hora de Venezuela para funciones de fecha y hora
      
      $tabla='<table width="100%"><tr><td style="vertical-align:middle; text-align:left">';
      $tabla.='<h4>'.$ae->codigo.' - '.$ae->descripcion.'</h4>';
      $tabla.='<input type="hidden" id="id_estructura" value="';
      $tabla.=$ae->id_estructura;
      $tabla.='"/>';
      $tabla.='</td>';
      $tabla.='<td width="10%" style="vertical-align:middle; text-align:right;">';
      
      $img='abierto.png';        
        
      $tabla.='<img src="'.base_url().'imagenes/'.$img.'" ';
      $tabla.='title="Fecha Tope: '.date("d/m/Y",strtotime($ae->fecha_tope)).'"';
      $tabla.='/>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='</table>';
      $tabla.='<table width="100%">';
      $tabla.='<tr>';
      $tabla.='<td width="30px" class="BotonIco">';
      $accion=' onclick="javascript:planProyecto('.$ae->id_proyecto.',1);" ';        
      $tabla.='<img src="'.base_url().'imagenes/back.png"'.$accion;
      $tabla.='title="clic para regresar"';
      $tabla.='/>'; 
      $tabla.='</td>';
      $tabla.='<td width="150px" style="vertical-align:top; text-align:right">';
      $tabla.='<strong>PROYECTO: '.$ae->cod_proy.'&nbsp;-&nbsp;</strong>';
      $tabla.='</td>';
      $tabla.='<td style="vertical-align:top; text-align:left; padding:0 10px 10px 0">';
      $tabla.=$ae->obj_esp;
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>';
      $tabla.='<td>';      
      $tabla.='</td>';
      $tabla.='<td style="vertical-align:top; text-align:right!important">'; 
      $tabla.='<strong>Acción Específica: '.$ae->codigo_ae.'&nbsp;-&nbsp;</strong>';
      $tabla.='</td>';
      $tabla.='<td style="padding:0 10px 10px 0">';      
      $tabla.=$ae->descripcion_ae;
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='</table>';     
             
      $tabla.='<div class="EntraDatos">';
      $tabla.='<table style="margin-top:0px!important">';
      $tabla.='<thead>';
      $tabla.='<tr><th colspan="3">'; 
       
      $tabla.='Creación de Actividad Nº: ';
      $tabla.=$ae->codigo_ae.'.'.$nro_actividad;
      $tabla.='</th></tr>';           
      $tabla.='</thead>';
      $tabla.='<tbody>'; 
      $tabla.='<tr>'; 
      $tabla.='<td colspan="3">';
      $tabla.='<input type="hidden" id="codigo_act" value="';
      $tabla.=$nro_actividad;
      $tabla.='"/>';
      
      $tabla.='<label>Descripción de la Actividad:</label>';
      $tabla.='<textarea rows="2" class="CampoFicha Editable" id="descripcion_act" title="Actividad">';      
      $tabla.='</textarea>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>';
      $tabla.='<td width="1px">';      
      $tabla.='</td>';
      $tabla.='<td>';
      $tabla.='<label>Unidad de Medida</label>';
      $tabla.='<input type=text" class="CampoFicha Editable" id="um_act" title="Unidad de Medida" />';
      $tabla.='</td>';
      $tabla.='<td>';
      $tabla.='<label>Cantidad</label>';
      $tabla.='<input type="text" class="Editable" id="cantidad_act" value="1" '.
              'title="Debe ser mayor a uno (1)" maxlength="5" size="5" style="text-align:center" ';   
      //onlyDigits(e, value, allowDecimal, allowNegative, allowThousand, decSep, thousandSep, decLength)
      $tabla.=' onblur="this.value=formatNumber(this.value,0);"';
      $tabla.=' onkeyup="formatNumber(this.value,0);" ';
      $tabla.=' onkeypress="return onlyDigits(event, this.value, false,false,false,\',\',\'.\',0);" ';
      $tabla.=' />';
      $tabla.='</td>';
      $tabla.='</tr>'; 
      $tabla.='<tr>';
      $tabla.='<td width="1px">';      
      $tabla.='</td>';
      $tabla.='<td colspan="2">';
      $tabla.='<label>Medios de Verificación de la actividad</label>';
      $tabla.='<textarea rows="2" class="CampoFicha Editable" id="mv_act" title="Medios de Verificación de la Actividad">';
      $tabla.='</textarea>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>';
      $tabla.='<td width="1px">';
      $tabla.='</td>';
      $tabla.='<td colspan="2">';
      $tabla.='<label>Supuestos</label>';
      $tabla.='<textarea rows="2" class="CampoFicha Editable" id="supuestos_act" title="Supuestos de la Actividad">';
      $tabla.='</textarea>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr style="border-bottom:#576F89 solid 1px">';
      $tabla.='<td colspan="3">';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>';
      $tabla.='<td>';
      $tabla.='</td>';
      $tabla.='<td width="50%">';
      $tabla.='<label>Responsable de la Actividad:</label>';
      $tabla.='<select class="Campos Editable" id="id_responsable" title="Seleccione el Responsable">';
      $tabla.=$this->_listar_usuarios($this->session->userdata('id_usuario'));
      $tabla.='</select>';
      $tabla.='</td>';
      $tabla.='<td>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<td>';
      $tabla.='</td>';
      $tabla.='<td>';
      $tabla.='<label>Fecha de Inicio:</label>';
      $tabla.='<input type="text" class="Fechas Editable" id="fechaI" title="Fecha Inicial" ';
      $tabla.='value="" ';
      $tabla.='readonly="readonly"/>';
      $tabla.='</td>';
      $tabla.='<td>';
      $tabla.='<label>Fecha de Culminación:</label>';
      $tabla.='<input type="text" class="Fechas Editable" id="fechaF" title="Fecha Final" ';
      $tabla.='value="" ';
      $tabla.='readonly="readonly" />';
      $tabla.='</td>';
      $tabla.='</tr>';
          
      $tabla.='</tbody>';
      
      $tabla.='<tfoot>';
      $tabla.='<tr><td colspan="3">';
     
      $tabla.='<div class="BotonIco" onclick="javascript:GuardarActividad('.$ae->id_accion.','.$ae->id_proyecto.')" title="Guardar Actividad">';
      $tabla.='<img src="imagenes/guardar32.png"/>&nbsp;';   
      $tabla.='Guardar';
      $tabla.= '</div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

      $tabla.='<div class="BotonIco" onclick="javascript:planProyecto('.$ae->id_proyecto.',1);" title="Salir">';
      $tabla.='<img src="imagenes/cancel.png"/>&nbsp;';
      $tabla.='Cancelar';
      $tabla.= '</div>';
      $tabla.='</td></tr>';
      $tabla.='</tfoot>';
      $tabla.='</table>';
      $tabla.='</div>';
      die ($tabla);
  } 
  
  function guardar_actividad()
  {   
     if (!$this->input->is_ajax_request()) die('Acceso Denegado');
    
     $datos=array(
              'id_accion' =>$this->input->post('id_accion'),
              'codigo_act'=>$this->input->post('codigo_act'),
              'descripcion_act'=>$this->input->post('descripcion_act'),
              'um_act'=>$this->input->post('um_act'),     
              'cantidad_act'=>intval(str_ireplace(",",".",str_ireplace(".","",$this->input->post('cantidad_act')))),
              'mv_act'=>$this->input->post('mv_act'),              
              'supuestos_act'=>$this->input->post('supuestos_act'),
              'id_responsable'=>$this->input->post('id_responsable'),
              'fecha_ini'=>$this->input->post('fecha_ini'),
              'fecha_fin'=>$this->input->post('fecha_fin')
              );              
        
     $insertado=$this->Crud->insertar_registro('p_actividades', $datos);
     if (!$insertado){die('Error');}
     else
     {
        $registro='id_actividad: '.$this->db->insert_id();
        $registro.='. '.$datos['descripcion_act'];
        $registro.='. Registrado por: '.$this->session->userdata('usuario');
        $bitacora=array(
            'direccion_ip'   =>$this->session->userdata('ip_address'),
            'navegador'      =>$this->session->userdata('user_agent'),
            'id_usuario'     =>$this->session->userdata('id_usuario'),
            'controlador'    =>$this->uri->uri_string(),
            'tabla_afectada' =>'p_actividades',
            'tipo_accion'    =>'INSERT',
            'registro'       =>$registro
        );
        $this->Crud->insertar_registro('z_bitacora', $bitacora);             
     }    
  }
  
  function editar_actividad()
  {
      if (!$this->input->is_ajax_request()) die('Acceso Denegado');
      
      $id_actividad= intval($this->input->post('id_actividad'));
      
      $act=$this->Proyectos->obtener_actividad($id_actividad);
      if (!$act)die('error consultando proyecto');
      
      date_default_timezone_set('America/Caracas'); // Establece la Hora de Venezuela para funciones de fecha y hora
      
      $tope=strtotime($act->fecha_tope);
      $ahora=time();      
      $editable=($ahora>$tope)?false:true;
      $readonly=($ahora>$tope)?' readonly="readonly" ':'';
      $img=($ahora>$tope)?'cerrado.png':'abierto.png';
    
      $tabla='<table width="100%"><tr><td style="vertical-align:middle; text-align:left">';
      $tabla.='<h4>'.$act->codigo.' - '.$act->descripcion.'</h4>';
      $tabla.='<input type="hidden" id="id_estructura" value="';
      $tabla.=$act->id_estructura;
      $tabla.='"/>';
      $tabla.='<input type="hidden" id="id_responsable" value="';
      $tabla.=$act->id_usuario;
      $tabla.='"/>';
      $tabla.='</td>';
      $tabla.='<td width="10%" style="vertical-align:middle; text-align:right;">';        
      $tabla.='<img src="'.base_url().'imagenes/'.$img.'" ';
      $tabla.='title="Fecha Tope: '.date("d/m/Y",strtotime($act->fecha_tope)).'"';
      $tabla.='/>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='</table>';
      $tabla.='<table width="100%">';
      $tabla.='<tr>';
      $tabla.='<td width="30px" class="BotonIco">';
      $accion=' onclick="javascript:planProyecto('.$act->id_proyecto.',1);" ';        
      $tabla.='<img src="'.base_url().'imagenes/back.png"'.$accion;
      $tabla.='title="clic para regresar"';
      $tabla.='/>'; 
      $tabla.='</td>';
      $tabla.='<td width="150px" style="vertical-align:top; text-align:right">';
      $tabla.='<strong>PROYECTO: '.$act->cod_proy.'&nbsp;-&nbsp;</strong>';
      $tabla.='</td>';
      $tabla.='<td style="vertical-align:top; text-align:left; padding:0 10px 10px 0">';
      $tabla.=$act->obj_esp;
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>';
      $tabla.='<td>';      
      $tabla.='</td>';
      $tabla.='<td style="vertical-align:top; text-align:right!important">'; 
      $tabla.='<strong>Acción Específica: '.$act->codigo_ae.'&nbsp;-&nbsp;</strong>';
      $tabla.='</td>';
      $tabla.='<td style="padding:0 10px 10px 0">';      
      $tabla.=$act->descripcion_ae;
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='</table>';     
             
      $tabla.='<div class="EntraDatos">';
      $tabla.='<table style="margin-top:0px!important">';
      $tabla.='<thead>';
      $tabla.='<tr><th colspan="3">'; 
       
      $tabla.='Edición de Actividad Nº: ';
      $tabla.=$act->codigo_ae.'.'.$act->codigo_act;
      $tabla.='</th></tr>';           
      $tabla.='</thead>';
      $tabla.='<tbody>'; 
      $tabla.='<tr>'; 
      $tabla.='<td colspan="2">';      
      $tabla.='<label>Nº:</label>';
      $tabla.='<input type="text" class="Editable" id="codigo_act" title="Nº de la Acción Específica" value="';
      $tabla.=$act->codigo_ae.'.'.$act->codigo_act.'" maxlength="5" size="5" style="text-align:center"';
      //onlyDigits(e, value, allowDecimal, allowNegative, allowThousand, decSep, thousandSep, decLength)
      $tabla.=' onblur="this.value=formatNumber(this.value,0);"';
      $tabla.=' onkeyup="formatNumber(this.value,0);" ';
      $tabla.=' onkeypress="return onlyDigits(event, this.value, false,false,false,\',\',\'.\',0);" ';
      $tabla.=$readonly.' />';
      $tabla.='</td>';
      $tabla.='<td>';
      $tabla.='Fuente Presupuestaria:';
      $tabla.='<select class="Editable" id="id_fuente" title="Seleccione la Fuente">';

      // Caja Combo para Fuentes Presupuestarias
      $opciones='<option value="0">ERROR AL CARGAR LA DATA</option>';
      $fuentes=$this->Crud->listar_registros('p_fuentes');
      $a=array();
      $b=array();
      if ($fuentes->num_rows()>0)
      {         
         foreach ($fuentes->result() as $fila)
         {
           array_push($a,$fila->id_fuente);
           array_push($b,($fila->cod_fuente.' - '.$fila->fuente));
         }         
         $opciones=array_combine($a,$b);
         ksort($opciones);
         $opciones=$this->comunes->construye_opciones($opciones, $act->id_fuente);
      }
      $tabla.=$opciones;
      $tabla.='</select>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>';
      $tabla.='<td colspan="3">';
      $tabla.='<label>Descripción de la Actividad:</label>';
      $tabla.='<textarea rows="2" class="CampoFicha Editable" id="descripcion_act" title="Actividad"'.
               $readonly.'>';
      $tabla.=$act->descripcion_act;
      $tabla.='</textarea>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>';
      $tabla.='<td width="1px">';      
      $tabla.='</td>';
      $tabla.='<td>';
      $tabla.='<label>Unidad de Medida</label>';
      $tabla.='<input type="text" class="CampoFicha Editable" id="um_act" title="Unidad de Medida"'.
              $readonly;
      $tabla.=' value="'.$act->um_act.'" />';
      $tabla.='</td>';
      $tabla.='<td>';
      $tabla.='<label>Cantidad</label>';
      $tabla.='<input type="text" class="Editable" id="cantidad_act" '.$readonly.
              'title="Debe ser mayor a uno (1)" maxlength="5" size="5" style="text-align:center" ';
      $tabla.=' value="'.number_format($act->cantidad_act,0,',','.').'" ';
      //onlyDigits(e, value, allowDecimal, allowNegative, allowThousand, decSep, thousandSep, decLength)
      $tabla.=' onblur="this.value=formatNumber(this.value,0);"';
      $tabla.=' onkeyup="formatNumber(this.value,0);" ';
      $tabla.=' onkeypress="return onlyDigits(event, this.value, false,false,false,\',\',\'.\',0);" ';
      $tabla.=' />';
      $tabla.='</td>';
      $tabla.='</tr>'; 
      $tabla.='<tr>';
      $tabla.='<td width="1px">';      
      $tabla.='</td>';
      $tabla.='<td colspan="2">';
      $tabla.='<label>Medios de Verificación de la Actividad</label>';
      $tabla.='<textarea rows="2" class="CampoFicha Editable" '.$readonly.
              'id="mv_act" title="Medios de Verificación de la Actividad">';
      $tabla.=$act->mv_act;
      $tabla.='</textarea>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>';
      $tabla.='<td width="1px">';
      $tabla.='</td>';
      $tabla.='<td colspan="2">';
      $tabla.='<label>Supuestos</label>';
      $tabla.='<textarea rows="2" class="CampoFicha Editable" '.$readonly.
              'id="supuestos_act" title="Supuestos de la Actividad">';
      $tabla.=$act->supuestos_act;
      $tabla.='</textarea>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr style="border-bottom:#576F89 solid 1px">';
      $tabla.='<td colspan="3">';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>';
      $tabla.='<td>';
      $tabla.='</td>';
      $tabla.='<td width="50%">';
      $tabla.='<label>Responsable de la Actividad:</label>';
      $tabla.='<select class="Campos Editable" id="Responsable" title="Seleccione el Responsable">';
      $tabla.=$this->_listar_usuarios($act->id_usuario);
      $tabla.='</select>';
      $tabla.='</td>';
      $tabla.='<td>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<td>';
      $tabla.='</td>';
      $tabla.='<td>';
      $tabla.='<label>Fecha de Inicio:</label>';
      $tabla.='<input type="text" class="Fechas Editable" id="fechaI" title="Fecha Inicial" value="';
      $tabla.=date("d/m/Y",strtotime($act->fecha_ini));            
      $tabla.='" readonly="readonly"/>';
      $tabla.='</td>';
      $tabla.='<td>';
      $tabla.='<label>Fecha de Culminación:</label>';
      $tabla.='<input type="text" class="Fechas Editable" id="fechaF" title="Fecha Final" value="';
      $tabla.=date("d/m/Y",strtotime($act->fecha_fin));
      $tabla.='" readonly="readonly" />';
      $tabla.='</td>';
      $tabla.='</tr>';
          
      $tabla.='</tbody>';
      
      $tabla.='<tfoot>';
      $tabla.='<tr><td colspan="3">';
     
      if ($editable)
      {      
         $tabla.='<div class="BotonIco" onclick="javascript:EliminarActividad('.$act->id_actividad.','.$act->id_proyecto.')" title="Eliminar Actividad">';
         $tabla.='<img src="imagenes/delactividad.png"/>&nbsp;';   
         $tabla.='Eliminar';
         $tabla.= '</div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';  
      
         $tabla.='<div class="BotonIco" onclick="javascript:ActualizarActividad('.$act->id_actividad.','.$act->id_proyecto.')" title="Guardar Cambios">';
         $tabla.='<img src="imagenes/guardar32.png"/>&nbsp;';   
         $tabla.='Guardar';
        $tabla.= '</div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
      }
      $tabla.='<div class="BotonIco" onclick="javascript:planProyecto('.$act->id_proyecto.',1);" title="Salir">';
      $tabla.='<img src="imagenes/cancel.png"/>&nbsp;';
      $tabla.='Cancelar';
      $tabla.= '</div>';
      $tabla.='</td></tr>';
      $tabla.='</tfoot>';
      $tabla.='</table>';
      $tabla.='</div>';
      die ($tabla);
  } 

  function actualizar_actividad()
  {
      if (!$this->input->is_ajax_request()) die('Acceso Denegado');
      
      $donde=array(
                'id_actividad'  => intval($this->input->post('id_actividad'))
                  );
      
      $codigo= trim($this->input->post('codigo_act'));
      $pos = strpos($codigo, '.');
      $codigo= ($pos===false)?$codigo:intval(substr("$codigo", ($pos+1)));
      $codigo=intval(str_replace($this->input->post('codigo_ae'),'',$codigo));
      
      $datos=array(                            
              'codigo_act'=>$codigo,
              'id_fuente'=>$this->input->post('id_fuente'),
              'descripcion_act'=>$this->input->post('descripcion_act'),
              'um_act'=>$this->input->post('um_act'),     
              'cantidad_act'=>  intval(str_ireplace(",",".",str_ireplace(".","",$this->input->post('cantidad_act')))),
              'mv_act'=>$this->input->post('mv_act'),              
              'supuestos_act'=>$this->input->post('supuestos_act'),
              'id_responsable'=>$this->input->post('id_responsable'),
              'fecha_ini'=>$this->input->post('fecha_ini'),
              'fecha_fin'=>$this->input->post('fecha_fin')
              );        
      $actualizado=$this->Crud->actualizar_registro('p_actividades', $datos, $donde);        
      if (!$actualizado){die('Error');}
      else 
      {           
         $registro='id_actividades: '.$donde['id_actividad'];         
         $registro.='. Actualizado por: '.$this->session->userdata('usuario');
         $bitacora=array(
             'direccion_ip'   =>$this->session->userdata('ip_address'),
             'navegador'      =>$this->session->userdata('user_agent'),
             'id_usuario'     =>$this->session->userdata('id_usuario'),
             'controlador'    =>$this->uri->uri_string(),
             'tabla_afectada' =>'p_actividades',
             'tipo_accion'    =>'UPDATE',
             'registro'       =>$registro
         );
         $this->Crud->insertar_registro('z_bitacora', $bitacora);            
      };   
  }  
         
  function _listar_usuarios($id_responsable=0)
  {       
    $id_estructura=$this->session->userdata('id_estructura');    
           
    $usuarios=$this->Usuarios->listar_usuarios($id_estructura);
    if (!$usuarios)die('<option selected="selected" value="0">No Existen Usuarios</option>');
    $lista=($id_responsable==0)?'<option selected="selected" value="0">[Seleccione Usuario]</option>':'';
    foreach ($usuarios as $fila)
    {
      $user=$fila['nombre'].' '.$fila['apellido'];
      $iduser=$fila['id_usuario'];
      if ($fila['id_usuario']==$id_responsable)
      {
        $lista.='<option selected="selected" value="'.$iduser.'">'.$user.'</option>';
      }
      else
      {
        $lista.='<option value="'.$iduser.'">'.$user.'</option>';
      }
    }        
    return($lista);
  }
  
  function eliminar_actividad()
  {
    if (!$this->input->is_ajax_request()) die('Acceso Denegado');
    $donde=array(
        'id_actividad' => intval($this->input->post('id_actividad'))
                   );        
    $borrado=$this->Crud->eliminar_registro('p_actividades', $donde);
    if (!$borrado){die('Error');}
    else
    {
       $registro='id_actividades: '.$donde['id_actividades'];
       $registro.='. Borrado por: '.$this->session->userdata('usuario');
       $bitacora=array(
           'direccion_ip'   =>$this->session->userdata('ip_address'),
           'navegador'      =>$this->session->userdata('user_agent'),
           'id_usuario'     =>$this->session->userdata('id_usuario'),
           'controlador'    =>$this->uri->uri_string(),
           'tabla_afectada' =>'p_actividades',
           'tipo_accion'    =>'DELETE',
           'registro'       =>$registro
       );
       $this->Crud->insertar_registro('z_bitacora', $bitacora); 
    }
  }
  
  function listar_presupuesto()
  {
     if (!$this->input->is_ajax_request()) die('Acceso Denegado');

      $id_actividad= intval($this->input->post('id_actividad'));
      $editable= intval($this->input->post('editable'));
      
      $act=$this->Proyectos->obtener_actividad($id_actividad);
      if (!$act)die('error consultando proyecto');
      
      date_default_timezone_set('America/Caracas'); // Establece la Hora de Venezuela para funciones de fecha y hora
      
      $tope=strtotime($act->fecha_tope);
      $ahora=time();      
      $editable=($ahora>$tope)?0:1;
      $readonly=($ahora>$tope)?' readonly="readonly" ':'';
      $img=($ahora>$tope)?'cerrado.png':'abierto.png';
      
      $tabla='<table width="100%"><tr><td style="vertical-align:middle; text-align:left">';
      $tabla.='<h4>'.$act->codigo.' - '.$act->descripcion.'</h4>';
      $tabla.='<input type="hidden" id="id_estructura" value="';
      $tabla.=$act->id_estructura;
      $tabla.='"/>';
      $tabla.='<input type="hidden" id="id_responsable" value="';
      $tabla.=$act->id_usuario;
      $tabla.='"/>';
      $tabla.='</td>';
      $tabla.='<td width="10%" style="vertical-align:middle; text-align:right;">';
             
      $tabla.='<img src="'.base_url().'imagenes/'.$img.'" ';
      $tabla.='title="Fecha Tope: '.date("d/m/Y",strtotime($act->fecha_tope)).'"';
      $tabla.='/>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='</table>';
      $tabla.='<table width="100%">';
      $tabla.='<tr>';
      $tabla.='<td width="30px" class="BotonIco">';
      $accion=' onclick="javascript:planProyecto('.$act->id_proyecto.','.$editable.');" ';        
      $tabla.='<img src="'.base_url().'imagenes/back.png"'.$accion;
      $tabla.='title="clic para regresar"';
      $tabla.='/>'; 
      $tabla.='</td>';
      $tabla.='<td width="150px" style="vertical-align:top; text-align:right">';
      $tabla.='<strong>PROYECTO: '.$act->cod_proy.'&nbsp;-&nbsp;</strong>';
      $tabla.='</td>';
      $tabla.='<td style="vertical-align:top; text-align:left; padding:0 10px 10px 0">';
      $tabla.=$act->obj_esp;
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>';
      $tabla.='<td>';      
      $tabla.='</td>';
      $tabla.='<td style="vertical-align:top; text-align:right!important">'; 
      $tabla.='<strong>Acción Específica: '.$act->codigo_ae.'&nbsp;-&nbsp;</strong>';
      $tabla.='</td>';
      $tabla.='<td style="padding:0 10px 10px 0">';      
      $tabla.=$act->descripcion_ae;
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>';
      $tabla.='<td>';      
      $tabla.='</td>';
      $tabla.='<td style="vertical-align:top; text-align:right!important">'; 
      $tabla.='<strong>Actividad: '.$act->codigo_ae.'.'.$act->codigo_act.'&nbsp;-&nbsp;</strong>';
      $tabla.='</td>';
      $tabla.='<td style="padding:0 10px 10px 0">';      
      $tabla.=$act->descripcion_act;
      $tabla.='</td>';
      $tabla.='</tr>';      
      $tabla.='<tr>';
      $tabla.='<td colspan="3">';
      $tabla.='<h4>PLANIFICACIÓN PRESUPUESTARIA</h4>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='</table>';
      
      $botonagregar='';
      if ($editable==1)
      {
        $botonagregar='<center><div class="BotonIco" onclick="javascript:agregarPresupuesto('.$id_actividad.')" title="Agregar Presupuesto">';
        $botonagregar.='<img src="imagenes/addgasto.png"/>&nbsp;';   
        $botonagregar.='Agregar Gasto';
        $botonagregar.= '</div></center>';
      }
  
      $presupuesto=$this->Proyectos->listar_presupuesto($id_actividad);
      
      if (!$presupuesto) // SI NO HAY PRESUPUESTO PARA ESTA ACTIVIDAD
      { 
        $tabla.='<div id="presupuesto">';  
        $tabla.='<h2><center>- Actividad sin Prespuesto -</center></h2>';
        $tabla.=$botonagregar;
        $tabla.='</div>';
        if ($this->input->is_ajax_request()) die($tabla); // Si la peticion vino por AJAX
        else return $tabla;
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
         $tabla.='<td style="text-align:right">';
         $tabla.=number_format($p->cantidad,2,',','.');
         $tabla.='</td>';
         $tabla.='<td style="text-align:right">';
         $tabla.=number_format($p->costo_unitario,4,',','.');
         $tabla.='</td>';
         $tabla.='<td style="text-align:right;">';
         $tabla.=number_format((($p->cantidad)*($p->costo_unitario)),2,',','.');
         $tabla.='</td>';
         $tabla.='<td>';
         if ($editable==1)
         {
           $accion=' onclick="editarPresupuesto('.$p->id_presupuesto.','.$p->id_actividad.');" ';
           $img='editar.png';
           $tabla.='<img src="'.base_url().'imagenes/'.$img.'" class="BotonIco" ';
           $tabla.='title="Editar Presupuesto"'.$accion;
           $tabla.='/>';
         }
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
      $tabla.=$botonagregar;
      $tabla.='</div>';
      die($tabla);      
  }
  
  function agregar_presupuesto()
  {
      if (!$this->input->is_ajax_request()) die('Acceso Denegado');
      
      $id_actividad= intval($this->input->post('id_actividad'));
      
      $tabla='<div class="EntraDatos">';
      $tabla.='<table style="margin-top:0px!important">';
      $tabla.='<thead>';
      $tabla.='<tr><th colspan="3">';        
      $tabla.='Agregar Presupuesto';      
      $tabla.='</th></tr>';           
      $tabla.='</thead>';
      $tabla.='<tbody>'; 
      $tabla.='<tr>'; 
      $tabla.='<td colspan="3">';
      $tabla.='<label>Partida Presupuestaria</label>';
      $tabla.='<input type="hidden" id="id_partida" value="0" />';
      $tabla.='<input class="Nom Editable" id="partida" title="Clasificador Presupuestario" />';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>'; 
      $tabla.='<td colspan="3">';
      $tabla.='<label>Descripción del Gasto</label>';
      $tabla.='<input class="Nom Editable" id="descripcion_gasto" title="Descripción del Gasto" />';
      $tabla.='</td>';
      $tabla.='</tr>';      
      $tabla.='<tr>'; 
      $tabla.='<td>';
      $tabla.='<label>Cantidad</label>';
      $tabla.='<input class="Nom Editable" id="cantidad" title="Cantidad"';
      //onlyDigits(e, value, allowDecimal, allowNegative, allowThousand, decSep, thousandSep, decLength)
      $tabla.=' onblur="this.value=formatNumber(this.value);"';
      $tabla.=' onkeyup="formatNumber(this.value);actualizaMensaje('.$id_actividad.');" ';
      $tabla.=' onkeypress="return onlyDigits(event, this.value,true,false,true,\',\',\'.\',2);" ';      
      $tabla.=' style="text-align:right" maxlength="20"/>';
      $tabla.='</td>';      
      $tabla.='<td>';      
      $tabla.='<label>Costo Unitario (Bs.)</label>';
      $tabla.='<input class="Nom Editable" id="costo_unitario" title="Costo Unitario"';
      //onlyDigits(e, value, allowDecimal, allowNegative, allowThousand, decSep, thousandSep, decLength)
      $tabla.=' onblur="this.value=formatNumber(this.value,4);"';
      $tabla.=' onkeyup="formatNumber(this.value,4);actualizaMensaje('.$id_actividad.');" ';
      $tabla.=' onkeypress="return onlyDigits(event, this.value,true,false,true,\',\',\'.\',4);" ';      
      $tabla.=' style="text-align:right" maxlength="20"/>';
      $tabla.='</td>';
      $tabla.='<td>';
      $tabla.='<label>Unidad de Medida</label>';
      $tabla.='<input class="Nom Editable" id="um" title="Unidad de Medida" />';     
      $tabla.='</td>';      
      $tabla.='</tr>'; 
      $tabla.='<tr>'; 
      $tabla.='<td colspan="3">';
      $tabla.='<div id="msjPresup">'.$this->_msjPresup($id_actividad).'</div>';
      $tabla.='</td>';
      $tabla.='</tr>'; 
      $tabla.='</tbody>';      
      $tabla.='<tfoot>';
      $tabla.='<tr><td colspan="3">';
      $tabla.='<div class="BotonIco" onclick="javascript:guardarPresupuesto('.$id_actividad.')" title="Guardar Presupuesto">';
      $tabla.='<img src="imagenes/guardar32.png"/>&nbsp;';   
      $tabla.='Guardar';
      $tabla.= '</div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

      $tabla.='<div class="BotonIco" onclick="javascript:listarPresupuesto('.$id_actividad.',1);" title="Salir">';
      $tabla.='<img src="imagenes/cancel.png"/>&nbsp;';
      $tabla.='Cancelar';
      $tabla.= '</div>';
      $tabla.='</td></tr>';
      $tabla.='</tfoot>';
      $tabla.='</table>';
      $tabla.='</div>';
      die ($tabla);
  }  
  
  // ACTUALIZA EL MENSAJE DEL PRESUPUESTO DE LA ACTIVIDAD DE UN PROYECTO VIA AJAX
  function actualizaMensaje()
  {
     if (!$this->input->is_ajax_request()) die('Acceso Denegado');
     $id_actividad = $this->input->post('id_actividad'); 
     $montoActividad = floatval($this->input->post('montoActividad'));
     
     die($this->_msjPresup($id_actividad, $montoActividad));
  }
  
  // DEVUELVE EL MENSAJE DEL PRESUPUESTO DE LA ACTIVIDAD DE UN PROYECTO
  function _msjPresup($id_actividad, $montoActividad=0)
  {
     $presupuesto = $this->Proyectos->getProyectoByIdActividad($id_actividad);
     if ($presupuesto->num_rows()==0) return 'ERROR. ACTIVIDAD SIN PROYECTO ASOCIADO';
     $p=$presupuesto->row();
     if ($p->monto_aprobado == 0) // SI NO HAY PRESUPUESTO APROBADO
         return '<input type="hidden" id="permisoGuardar" value="1" />';
     $diferencia = ($p->monto_aprobado - $p->monto_planificado - $montoActividad);
     $msj='';
     switch ($diferencia)
     {
         case 0  :           $msj ="<strong>Presupuesto Totalmente Planificado<strong>";
                             $msj.='<input type="hidden" id="permisoGuardar" value="1" />';
                             break;
         case $diferencia>0: // MIENTRAS QUEDE PRESUPUESTO
                             $msj='Presupuesto Disponible por Planificar: <strong>Bs. '.
                                    number_format($diferencia,2,',','.');
                             $msj.='</strong><input type="hidden" id="permisoGuardar" value="1" />';
                             break;
         default: // SI LA PLANIFICACION EXCEDE LO APROBADO
                             $msj ='<img src="imagenes/advertencia.png" style="padding-bottom:5px" /> &nbsp;';
                             $msj.='El monto planificado excede en ';
                             $msj.='<span style="font-weight:bold; color:red">Bs. '.
                                     number_format(abs($diferencia),2,',','.');
                             $msj.='</span> al presupuesto aprobado';
                             $msj.='<input type="hidden" id="permisoGuardar" value="0" />';
                             break;
     }     
     return $msj;
  }
  
  function guardar_presupuesto()
  {   
     if (!$this->input->is_ajax_request()) die('Acceso Denegado');
    
     $datos=array(
              'id_actividad' =>$this->input->post('id_actividad'),
              'id_partida'=>$this->input->post('id_partida'),
              'descripcion_gasto'=>$this->input->post('descripcion_gasto'),
              'um'=>$this->input->post('um'),         
              'cantidad'=>str_ireplace(",",".",str_ireplace(".","",$this->input->post('cantidad'))),
              'costo_unitario'=>str_ireplace(",",".",str_ireplace(".","",$this->input->post('costo_unitario')))
              );              
        
     $insertado=$this->Crud->insertar_registro('p_presupuesto_actividad', $datos);
     if (!$insertado){die('Error');}
     else
     {
        $registro='id_presupuesto: '.$this->db->insert_id();
        $registro.='. '.$datos['descripcion_gasto'];
        $registro.='. Registrado por: '.$this->session->userdata('usuario');
        $bitacora=array(
            'direccion_ip'   =>$this->session->userdata('ip_address'),
            'navegador'      =>$this->session->userdata('user_agent'),
            'id_usuario'     =>$this->session->userdata('id_usuario'),
            'controlador'    =>$this->uri->uri_string(),
            'tabla_afectada' =>'p_presupuesto_actividad',
            'tipo_accion'    =>'INSERT',
            'registro'       =>$registro
        );
        $this->Crud->insertar_registro('z_bitacora', $bitacora);             
     }    
  }  

  function editar_presupuesto()
  {
      if (!$this->input->is_ajax_request()) die('Acceso Denegado');
      
      $id_presupuesto= intval($this->input->post('id_presupuesto'));
      $id_actividad= intval($this->input->post('id_actividad'));
      
      $p=$this->Proyectos->obtener_presupuesto($id_presupuesto);
      if (!$p)die('error consultando proyecto');
      
      $tabla='<div class="EntraDatos">';
      $tabla.='<table style="margin-top:0px!important">';
      $tabla.='<thead>';
      $tabla.='<tr><th colspan="3">';        
      $tabla.='Editar Presupuesto';      
      $tabla.='</th></tr>';           
      $tabla.='</thead>';
      $tabla.='<tbody>'; 
      $tabla.='<tr>'; 
      $tabla.='<td colspan="3">';
      $tabla.='<label>Partida Presupuestaria</label>';
      $tabla.='<input type="hidden" id="id_partida" value="'.$p->id_partida.'" />';
      $tabla.='<input class="Nom Editable" id="partida" title="Clasificador Presupuestario" ';
      $tabla.=' value="'.$p->id_partida.' '.$p->denominacion.'" ';
      $tabla.='/>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>'; 
      $tabla.='<td colspan="3">';
      $tabla.='<label>Descripción del Gasto</label>';
      $tabla.='<input class="Nom Editable" id="descripcion_gasto" title="Descripción del Gasto" ';
      $tabla.=' value="'.$p->descripcion_gasto.'" ';
      $tabla.='/>';
      $tabla.='</td>';
      $tabla.='</tr>';      
      $tabla.='<tr>'; 
      $tabla.='<td>';
      $tabla.='<label>Cantidad</label>';
      $montoActividadOrig= ($p->cantidad * $p->costo_unitario);
      $tabla.='<input class="Nom Editable" id="cantidad" title="Cantidad"';
      //onlyDigits(e, value, allowDecimal, allowNegative, allowThousand, decSep, thousandSep, decLength)
      $tabla.=' onblur="this.value=formatNumber(this.value);"';
      $tabla.=' onkeyup="formatNumber(this.value);actualizaMensaje('.
                            $id_actividad.','.$montoActividadOrig.');" ';
      $tabla.=' onkeypress="return onlyDigits(event, this.value,true,false,true,\',\',\'.\',2);" ';               
      $tabla.=' value="'.number_format($p->cantidad,2,',','.').'"';
      $tabla.=' style="text-align:right" maxlength="20"/>';
      $tabla.='</td>';      
      $tabla.='<td>';      
      $tabla.='<label>Costo Unitario (Bs.)</label>';
      $tabla.='<input class="Nom Editable" id="costo_unitario" title="Costo Unitario"';
      //onlyDigits(e, value, allowDecimal, allowNegative, allowThousand, decSep, thousandSep, decLength)
      $tabla.=' onblur="this.value=formatNumber(this.value,4);"';
      $tabla.=' onkeyup="formatNumber(this.value,4);actualizaMensaje('.
                            $id_actividad.','.$montoActividadOrig.');" ';
      $tabla.=' onkeypress="return onlyDigits(event, this.value,true,false,true,\',\',\'.\',4);" ';    
      $tabla.=' value="'.number_format($p->costo_unitario,4,',','.').'"';
      $tabla.=' style="text-align:right" maxlength="20"/>';
      $tabla.='</td>';
      $tabla.='<td>';
      $tabla.='<label>Unidad de Medida</label>';
      $tabla.='<input class="Nom Editable" id="um" title="Unidad de Medida" ';
      $tabla.=' value="'.$p->um.'" ';
      $tabla.='/>';
      $tabla.='</td>';      
      $tabla.='</tr>'; 
      $tabla.='<tr>'; 
      $tabla.='<td colspan="3">';
      $tabla.='<div id="msjPresup">'.$this->_msjPresup($id_actividad).'</div>';
      $tabla.='</td>';
      $tabla.='</tr>';      
      $tabla.='</tbody>';
      $tabla.='<tfoot>';
      $tabla.='<tr><td colspan="3">';      
      $tabla.='<div class="BotonIco" onclick="javascript:eliminarPresupuesto('.$id_presupuesto.','.$p->id_actividad.')" title="Eliminar Gasto">';
      $tabla.='<img src="imagenes/delgasto.png"/>&nbsp;';   
      $tabla.='Eliminar';
      $tabla.= '</div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';        
     
      $tabla.='<div class="BotonIco" onclick="javascript:actualizarPresupuesto('.$id_presupuesto.','.$p->id_actividad.')" title="Guardar Presupuesto">';
      $tabla.='<img src="imagenes/guardar32.png"/>&nbsp;';   
      $tabla.='Guardar';
      $tabla.= '</div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

      $tabla.='<div class="BotonIco" onclick="javascript:listarPresupuesto('.$p->id_actividad.',1);" title="Salir">';
      $tabla.='<img src="imagenes/cancel.png"/>&nbsp;';
      $tabla.='Cancelar';
      $tabla.= '</div>';
      $tabla.='</td></tr>';
      $tabla.='</tfoot>';
      $tabla.='</table>';
      $tabla.='</div>';
      die ($tabla);
  }  
  
  function actualizar_presupuesto()
  {
      if (!$this->input->is_ajax_request()) die('Acceso Denegado');
     
      $donde=array(
                 'id_presupuesto'  => intval($this->input->post('id_presupuesto'))
                 );
      
      $datos=array(     
              'id_partida'=>$this->input->post('id_partida'),
              'descripcion_gasto'=>$this->input->post('descripcion_gasto'),
              'um'=>$this->input->post('um'),         
              'cantidad'=>str_ireplace(",",".",str_ireplace(".","",$this->input->post('cantidad'))),
              'costo_unitario'=>str_ireplace(",",".",str_ireplace(".","",$this->input->post('costo_unitario')))
              ); 
     
      $actualizado=$this->Crud->actualizar_registro('p_presupuesto_actividad', $datos, $donde);        
      if (!$actualizado){die('Error');}
      else 
      {           
         $registro='id_presupuesto: '.$donde['id_presupuesto'];         
         $registro.='. Actualizado por: '.$this->session->userdata('usuario');
         $bitacora=array(
             'direccion_ip'   =>$this->session->userdata('ip_address'),
             'navegador'      =>$this->session->userdata('user_agent'),
             'id_usuario'     =>$this->session->userdata('id_usuario'),
             'controlador'    =>$this->uri->uri_string(),
             'tabla_afectada' =>'p_presupuesto_actividad',
             'tipo_accion'    =>'UPDATE',
             'registro'       =>$registro
         );
         $this->Crud->insertar_registro('z_bitacora', $bitacora);            
      };   
  }  
  
  function eliminar_presupuesto()
  {
    if (!$this->input->is_ajax_request()) die('Acceso Denegado');
    $donde=array(
        'id_presupuesto' => intval($this->input->post('id_presupuesto'))
                   );        
    $borrado=$this->Crud->eliminar_registro('p_presupuesto_actividad', $donde);
    if (!$borrado){die('Error');}
    else
    {
       $registro='id_presupuesto: '.$donde['id_presupuesto'];
       $registro.='. Borrado por: '.$this->session->userdata('usuario');
       $bitacora=array(
           'direccion_ip'   =>$this->session->userdata('ip_address'),
           'navegador'      =>$this->session->userdata('user_agent'),
           'id_usuario'     =>$this->session->userdata('id_usuario'),
           'controlador'    =>$this->uri->uri_string(),
           'tabla_afectada' =>'p_presupuesto_actividad',
           'tipo_accion'    =>'DELETE',
           'registro'       =>$registro
       );
       $this->Crud->insertar_registro('z_bitacora', $bitacora); 
    }
  }
  
  function listado_partidas()
  {
      if (!$this->input->is_ajax_request()) die('Acceso Denegado');
      $frase= $this->input->post('frase');      
      die(json_encode($this->Proyectos->listado_partidas($frase)));
  }    

}