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
class Plan_productos extends CI_Controller {
  function __construct() 
  {
     parent::__construct();
     $this->load->helper('form');
     $this->load->library('Comunes');
     $this->load->model('Usuarios');
     $this->load->model('Estructura');
     $this->load->model('Productos');
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
    $data['titulo']='Planificación de Sub-Productos Determinados';
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
    
    
    // BOTON TABLA/GANTT
    $datos=array(
             'img'  =>base_url()."imagenes/tabla.png",
             'title' => 'Visualizar Planificación en Diagrama Gantt',
             'valor'=>'f');
        
         $boton='<div class="ToggleBoton" onclick="javascript:ToggleBotonGantt();Actualiza();">';
         $boton.='<img id="imgGantt" src="'.$datos['img'].'" title="'.$datos['title'].'"/>';
         $boton.='</div>';
         $boton.='<input type="hidden" id="hideGantt" value="'.$datos['valor'].'" />';
    // FIN BOTON TABLA/GANTT        
    
    $data['contenido']='plan_productos/plan_productos';    
    $data['script']='<!-- Cargamos CSS de DataTables -->'."\n";    
    $data['script'].="\t".'<link rel="stylesheet" type="text/css" media="all" href="'.base_url().'css/dataTables.css"/>'."\n";
    $data['script'].='<!-- Cargamos JS para DataTables -->'."\n";
    $data['script'].="\t".'<script type="text/javascript" src="'.base_url().'js/jquery.dataTables.js"></script>'."\n";
    $data['script'].='<!-- Cargamos Nuestro JS -->'."\n";
    $data['script'].="\t".'<script type="text/javascript" src="'.base_url().'js/plan_productos.js"></script>'."\n";
       
    $data['tabla']=$this->generar_tabla_planes($year_poa);
    
    $data['boton_gantt']=$boton;
    
 // CARGAMOS LA VISTA   
    $this->load->view('plantillas/plantilla_general',$data);  
  }  
  
  //////////////////////////////////////////////////////////////////////////////////////////
  
  function generar_tabla_planes($yearpoa=0)
  {
    if ($this->input->is_ajax_request()) $yearpoa=$this->input->post('yearpoa'); // Si la peticion vino por AJAX
    date_default_timezone_set('America/Caracas');
    $id_estructura=$this->session->userdata('id_estructura');
    $consulta=$this->Estructura->obtener_estructuras_inferiores($id_estructura);
    if (!$consulta)die('error consultando estructuras inferiores');
    $estructuras='where';
    foreach ($consulta as $fila)
    {
        $estructuras.= ' id_estructura='.$fila['id_estructura'].' or';
    }
    $estructuras=  substr($estructuras, 0, -3);
    unset($fila);    
    $planificacion=$this->Productos->planificacion_productos($estructuras,$yearpoa);  
    
    if ($planificacion->num_rows()==0) // SI NO HAY SUB-PRODUCTOS DETERMINADOS EN LA UNIDAD
    { 
      $tabla='<h2><center>La Unidad No Posee Sub-Productos Determinados</center></h2>';      
      if ($this->input->is_ajax_request()) die($tabla); // Si la peticion vino por AJAX
      else return $tabla;
    }      
    
    $planificacion=$planificacion->result_array();
    // CONSTRUIMOS LA TABLA CON EL RESUMEN DE LA PLANIFICACION DE SUB-PRODUCTOS DETERMINADOS    
    $estructura=$planificacion[0]['id_estructura']; // OBTENGO LA PRIMERA ESTRUCTURA
    $i=0;
    $n=count($planificacion);
    $tabla='';
    while($i<$n)
    {
        // ENCABEZADOS DE TABLA
        $tabla.='<table width="100%"><tr><td style="vertical-align:middle; text-align:left">';
        $tabla.='<h4>'.$planificacion[$i]['codigo'].' - '.$planificacion[$i]['descripcion'].'</h4>';
        $tabla.='</td>';
        $tabla.='<td width="10%" style="vertical-align:middle; text-align:right; padding:0 10px 5px 0">';
        $tope=strtotime($planificacion[$i]['fecha_tope']);
        $ahora=time();        
        $img=($ahora>$tope)?'cerrado.png':'abierto.png';        
        
        $tabla.='<img src="'.base_url().'imagenes/'.$img.'" ';
        $tabla.='title="Fecha Tope: '.date("d/m/Y",strtotime($planificacion[$i]['fecha_tope'])).'"';
        $tabla.='/>';
        $tabla.='</td>';
        $tabla.='</tr></table>';
        $tabla.='<table class="TablaNivel1 Zebrado">';
        $tabla.=$this->_cabecetabla(true);
        $tabla.='<tbody>';
        while(($planificacion[$i]['id_estructura']==$estructura))
        {
           $tabla.='<tr class="Resaltado">';
           $titulo= trim($planificacion[$i]['pcodigo']).'. '.trim($planificacion[$i]['pnombre']);
           $idsp=$planificacion[$i]['id_subproducto'];
           $tabla.='<td title="'.$titulo.'" onclick="javascript:VerInfo('.$idsp.')">';
           $tabla.=trim($planificacion[$i]['pcodigo']).'.'.trim($planificacion[$i]['scodigo']);
           $tabla.='</td>';          
           $tabla.='<td style="text-align:left" title="'.$titulo.'" onclick="javascript:VerInfo('.$idsp.')">';
           $tabla.=trim($planificacion[$i]['snombre']);
           $tabla.='</td>';
           $tabla.='<td title="Enero">';
           $tabla.=trim($planificacion[$i]['ene'])==''?0:trim($planificacion[$i]['ene']);
           $tabla.='</td>';
           $tabla.='<td title="Febrero">';
           $tabla.=trim($planificacion[$i]['feb'])==''?0:trim($planificacion[$i]['feb']);
           $tabla.='</td>';
           $tabla.='<td title="Marzo">';
           $tabla.=trim($planificacion[$i]['mar'])==''?0:trim($planificacion[$i]['mar']);
           $tabla.='</td>';
           $tabla.='<td title="Abril">';
           $tabla.=trim($planificacion[$i]['abr'])==''?0:trim($planificacion[$i]['abr']);
           $tabla.='</td>';
           $tabla.='<td title="Mayo">';
           $tabla.=trim($planificacion[$i]['may'])==''?0:trim($planificacion[$i]['may']);
           $tabla.='</td>';
           $tabla.='<td title="Junio">';
           $tabla.=trim($planificacion[$i]['jun'])==''?0:trim($planificacion[$i]['jun']);
           $tabla.='</td>';
           $tabla.='<td title="Julio">';
           $tabla.=trim($planificacion[$i]['jul'])==''?0:trim($planificacion[$i]['jul']);
           $tabla.='</td>';
           $tabla.='<td title="Agosto">';
           $tabla.=trim($planificacion[$i]['ago'])==''?0:trim($planificacion[$i]['ago']);
           $tabla.='</td>';
           $tabla.='<td title="Septiembre">';
           $tabla.=trim($planificacion[$i]['sep'])==''?0:trim($planificacion[$i]['sep']);
           $tabla.='</td>';
           $tabla.='<td title="Octubre">';
           $tabla.=trim($planificacion[$i]['oct'])==''?0:trim($planificacion[$i]['oct']);
           $tabla.='</td>';
           $tabla.='<td title="Noviembre">';
           $tabla.=trim($planificacion[$i]['nov'])==''?0:trim($planificacion[$i]['nov']);
           $tabla.='</td>';
           $tabla.='<td title="Diciembre">';
           $tabla.=trim($planificacion[$i]['dic'])==''?0:trim($planificacion[$i]['dic']);
           $tabla.='</td>';
           $tabla.='<td title="Total Anual">';
           $tabla.=trim($planificacion[$i]['anual'])==''?0:trim($planificacion[$i]['anual']);
           $tabla.='</td>';
           $tabla.='</tr>';        
           $i++;
           if ($i==$n) break;
        }
        //PIE DE TABLA
        $tabla.='</tbody></table><br/><br/>';   
        if ($i==$n) break;
        $estructura=$planificacion[$i]['id_estructura'];                         
    }         
    
    if ($this->input->is_ajax_request()) die($tabla); // Si la peticion vino por AJAX
    else return $tabla;
  }
  
  function generar_gantt_planes($yearpoa=0)
  {
    if ($this->input->is_ajax_request()) $yearpoa=$this->input->post('yearpoa'); // Si la peticion vino por AJAX
    date_default_timezone_set('America/Caracas');
    $id_estructura=$this->session->userdata('id_estructura');
    $consulta=$this->Estructura->obtener_estructuras_inferiores($id_estructura);
    if (!$consulta)die('error consultando estructuras inferiores');
    $estructuras='where';
    foreach ($consulta as $fila)
    {
        $estructuras.= ' id_estructura='.$fila['id_estructura'].' or';
    }
    $estructuras=  substr($estructuras, 0, -3);
    unset($fila);    
    $planificacion=$this->Productos->gantt_productos($estructuras,$yearpoa);  
    
    if ($planificacion->num_rows()==0) // SI NO HAY SUB-PRODUCTOS DETERMINADOS EN LA UNIDAD
    { 
      $tabla='<h2><center>La Unidad No Posee Sub-Productos Determinados</center></h2>';      
      if ($this->input->is_ajax_request()) die($tabla); // Si la peticion vino por AJAX
      else return $tabla;
    }  
    
    $planificacion=$planificacion->result_array();
    
    // CONSTRUIMOS LA TABLA CON EL RESUMEN DE LA PLANIFICACION DE SUB-PRODUCTOS DETERMINADOS    
    $estructura=$planificacion[0]['id_estructura']; // OBTENGO LA PRIMERA ESTRUCTURA
    $subpro=$planificacion[0]['id_subproducto'];  // OBTENGO EL PRIMER SUBPRODUCTO
    $i=0;
    $n=count($planificacion);
    $tabla='';
    while($i<$n)
    {
        // DATOS DE LA ESTRUCTURA
        $tabla.='<table width="100%"><tr><td style="vertical-align:middle; text-align:left">';
        $tabla.='<h4>'.$planificacion[$i]['codigo'].' - '.$planificacion[$i]['descripcion'].'</h4>';
        $tabla.='</td>';
        $tabla.='<td width="10%" style="vertical-align:middle; text-align:right; padding:0 10px 5px 0">';
        $tope=strtotime($planificacion[$i]['fecha_tope']);
        $ahora=time();        
        $img=($ahora>$tope)?'cerrado.png':'abierto.png';        
        $tabla.='<img src="'.base_url().'imagenes/'.$img.'" ';
        $tabla.='title="Fecha Tope: '.date("d/m/Y",strtotime($planificacion[$i]['fecha_tope'])).'"';
        $tabla.='/>';
        $tabla.='</td>';
        $tabla.='</tr></table>';
        // PLANIFICACION DE SUB-PRODUCTOS
        $tabla.='<table class="TablaNivel1 Zebrado">';
        $tabla.=$this->_cabecetabla();
        $tabla.='<tbody>';          
        // SUB-PRODUCTOS
        while(($planificacion[$i]['id_estructura']==$estructura))
        {           
           $titulo=trim($planificacion[$i]['pcodigo']).'. '.trim($planificacion[$i]['pnombre']);
           $idsp=$planificacion[$i]['id_subproducto'];   
           
           $tabla.='<tr class="Resaltado">';      
           $tabla.='<td title="'.$titulo.'" id="codSP_'.$idsp.'" onclick="javascript:VerInfo('.$idsp.')">';
           $tabla.=trim($planificacion[$i]['pcodigo']).'.'.trim($planificacion[$i]['scodigo']);
           $tabla.='</td>';                     
           $tabla.='<td style="text-align:left" title="'.$titulo.'" id="nomSP_'.$idsp.'" onclick="javascript:VerInfo('.$idsp.')">';
           $tabla.=trim($planificacion[$i]['snombre']);
           $tabla.='</td>';
           $tabla.='<td class="Cuadricula" colspan="12">';
           $tabla.='<input type="hidden" id="idEstructura_'.$idsp.'" ';
           $tabla.='value="'.$estructura.'" />';
           $tabla.='</td>';
           $tabla.='<td style="vertical-align:middle">';
           
           $thisdate=  getdate($ahora);
           if ($ahora<=$tope && $yearpoa>=$thisdate['year'])
           {
             $img='plan_add.png';
             $tabla.='<img src="'.base_url().'imagenes/'.$img.'" class="BotonIco" ';
             $tabla.='title="Clic para programar planificación" ';
             $tabla.='onclick="javascript:AgregarActividad('.$planificacion[$i]['id_subproducto'].');" ';
             $tabla.='/>';   
           }          
           $tabla.='</td>';
           $tabla.='</tr>';           
           // ACTIVIDADES
           while ($subpro==$planificacion[$i]['id_subproducto'])
           {
             $idplan=$planificacion[$i]['id_plan_producto'];
             $tabla.='<tr>';
             $tabla.='<td>';
             $tabla.='&nbsp';
             $tabla.='</td>';            
             $tabla.='<td style="text-align:left;">';             
              
              if ($planificacion[$i]['actividad']!='')
              {
                $fechai=date("d/m/Y",strtotime($planificacion[$i]['fecha_ini']));
                $fechaf=date("d/m/Y",strtotime($planificacion[$i]['fecha_fin']));
                $duracion=$this->comunes->diferenciaEntreFechas($planificacion[$i]['fecha_ini'], $planificacion[$i]['fecha_fin'], 'DIAS', true);
                $tabla.='<span id="act_'.$idplan.'">'.trim($planificacion[$i]['actividad']).'</span><br/>';
                $tabla.='<i>(Responsable: '.trim($planificacion[$i]['responsable']).' /';
                $tabla.=' Duración: ';
                $tabla.=($duracion<1)?1:$duracion;
                $tabla.=($duracion<1)?' día) </i>':' días) </i>';                
                $tabla.='<input type="hidden" id="fechaIni_'.$idplan.'" ';
                $tabla.='value="'.$fechai.'" />';
                $tabla.='<input type="hidden" id="fechaFin_'.$idplan.'" ';
                $tabla.='value="'.$fechaf.'" />';
                $tabla.='<input type="hidden" id="idResponsable_'.$idplan.'" ';
                $tabla.='value="'.$planificacion[$i]['id_responsable'].'" />';
              }
                         
             $tabla.='</td>';
             $titulo='';
             $gantt='*** SUB-PRODUCTO SIN PLANIFICACION PROGRAMADA ***';
              if ($planificacion[$i]['actividad']!='')
              {
                $gantt=$this->comunes->mini_gantt($planificacion[$i]['fecha_ini'], $planificacion[$i]['fecha_fin'], 40);
                $titulo='Desde: ';
                $titulo.=date("d/m/Y",strtotime($planificacion[$i]['fecha_ini']));
                $titulo.=' Hasta: ';
                $titulo.=date("d/m/Y",strtotime($planificacion[$i]['fecha_fin']));
              }
             $tabla.='<td class="Cuadricula" colspan="12" title="'.$titulo.'">';
             $tabla.='<div class="gantt01">';
             $tabla.=$gantt;
             $tabla.='</div>';
             $tabla.='</td>';
             $tabla.='<td style="vertical-align:middle">';
             
             $F=strtotime($planificacion[$i]['fecha_fin']);
              if ($planificacion[$i]['actividad']!='' && $ahora<=$F && $ahora<=$tope)
              {
                $img='edit.png';
                $tabla.='<img src="'.base_url().'imagenes/'.$img.'" class="BotonIco" ';
                $tabla.='title="Clic para editar la Actividad" ';
                $tabla.='onclick="javascript:EditarActividad('.$idsp.','.$idplan.');" />';
              }          
             $tabla.='</td>';
             $tabla.='</tr>';
             $i++;
             if ($i==$n) break;
           }
           if ($i==$n) break;
           $subpro=$planificacion[$i]['id_subproducto'];
        }
        //PIE DE TABLA
        $tabla.='</tbody></table><br/><br/>';   
        if ($i==$n) break;
        $estructura=$planificacion[$i]['id_estructura'];                         
    }         
    
    if ($this->input->is_ajax_request()) die($tabla); // Si la peticion vino por AJAX
    else return $tabla;
  }
  
  function listar_usuarios()
  {
    if (!$this->input->is_ajax_request()) die('Acceso Denegado');       
   
    $id_estructura=intval($this->input->post('id_estructura'));
    $id_responsable=intval($this->input->post('id_responsable'));
           
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
    die($lista);
  }
  
  function actualizar_actividad()
  {
      if (!$this->input->is_ajax_request()) die('Acceso Denegado');
      
      $donde=array(
                'id_plan_producto'  => intval($this->input->post('id_plan'))       
                  );
      $datos=array(                
              'descripcion'    => $this->input->post('actividad'),
              'id_responsable' => $this->input->post('id_responsable'),
              'fecha_ini'      => $this->input->post('fecha_ini'),
              'fecha_fin'      => $this->input->post('fecha_fin')                
              );        
      $actualizado=$this->Crud->actualizar_registro('c_plan_productos', $datos, $donde);        
      if (!$actualizado){die('Error');}
      else 
      {           
         $registro='id_plan_producto: '.$donde['id_plan_producto'];         
         $registro.='. Actualizado por: '.$this->session->userdata('usuario');
         $bitacora=array(
             'direccion_ip'   =>$this->session->userdata('ip_address'),
             'navegador'      =>$this->session->userdata('user_agent'),
             'id_usuario'     =>$this->session->userdata('id_usuario'),
             'controlador'    =>$this->uri->uri_string(),
             'tabla_afectada' =>'c_plan_productos',
             'tipo_accion'    =>'UPDATE',
             'registro'       =>$registro
         );
         $this->Crud->insertar_registro('z_bitacora', $bitacora);            
      };   
  }
 
  function eliminar_actividad()
  {
    if (!$this->input->is_ajax_request()) die('Acceso Denegado');
    $actividad=array(
        'id_plan_producto' => intval($this->input->post('id_plan'))
                   );        
    $borrado=$this->Crud->eliminar_registro('c_plan_productos', $actividad);
    if (!$borrado){die('Error');}
    else
    {
       $registro='id_plan_producto: '.$actividad['id_plan_producto'];           
       $registro.='. Borrado por: '.$this->session->userdata('usuario');
       $bitacora=array(
           'direccion_ip'   =>$this->session->userdata('ip_address'),
           'navegador'      =>$this->session->userdata('user_agent'),
           'id_usuario'     =>$this->session->userdata('id_usuario'),
           'controlador'    =>$this->uri->uri_string(),
           'tabla_afectada' =>'c_plan_productos',
           'tipo_accion'    =>'DELETE',
           'registro'       =>$registro
       );
       $this->Crud->insertar_registro('z_bitacora', $bitacora); 
    }
  }
  
  function guardar_actividad()
  {   
    if (!$this->input->is_ajax_request()) die('Acceso Denegado');        
        $datos=array(
                'id_subproducto'=> $this->input->post('id_subprod'),
                'descripcion'   => $this->input->post('descripcion'),
                'id_responsable'=> $this->input->post('id_responsable'),
                'fecha_ini'     => $this->input->post('fecha_ini'),
                'fecha_fin'     => $this->input->post('fecha_fin'),
                );
        $insertado=$this->Crud->insertar_registro('c_plan_productos', $datos);
        if (!$insertado){die('Error');}
        else
        {
           $registro='id_plan_producto: '.$this->db->insert_id();
           $registro.='. '.$datos['descripcion'];           
           $registro.='. Registrado por: '.$this->session->userdata('usuario');
           $bitacora=array(
               'direccion_ip'   =>$this->session->userdata('ip_address'),
               'navegador'      =>$this->session->userdata('user_agent'),
               'id_usuario'     =>$this->session->userdata('id_usuario'),
               'controlador'    =>$this->uri->uri_string(),
               'tabla_afectada' =>'c_plan_productos',
               'tipo_accion'    =>'INSERT',
               'registro'       =>$registro
           );
           $this->Crud->insertar_registro('z_bitacora', $bitacora);             
        }    
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
}