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
class Ejecucion_productos extends CI_Controller {
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
    $year_poa=$ahora['year']; // El año inicial del POA es el año en curso para ejecución
    
    $data=array();
    $data['titulo']='Ejecución de Sub-Productos Administrativos';
    $data['subtitulo']='Ejecución Operativa del Año:';
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
    
    $data['contenido']='ejecucion_productos/ejecucion_productos';    
    $data['script']='<!-- Cargamos CSS de DataTables -->'."\n";    
    $data['script'].="\t".'<link rel="stylesheet" type="text/css" media="all" href="'.base_url().'css/dataTables.css"/>'."\n";
    $data['script'].='<!-- Cargamos JS para DataTables -->'."\n";
    $data['script'].="\t".'<script type="text/javascript" src="'.base_url().'js/jquery.dataTables.js"></script>'."\n";
    $data['script'].='<!-- Cargamos Nuestro JS -->'."\n";
    $data['script'].="\t".'<script type="text/javascript" src="'.base_url().'js/ejecucion_productos.js"></script>'."\n";
       
    $data['tabla']=$this->generar_tabla_ejecucion($year_poa);    
        
 // CARGAMOS LA VISTA   
    $this->load->view('plantillas/plantilla_general',$data);  
  }  
  
  //////////////////////////////////////////////////////////////////////////////////////////
  
  function generar_tabla_ejecucion($yearpoa=0)
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
    $planificacion=$this->Productos->ejecucion_productos($estructuras,$yearpoa);  
    
    if ($planificacion->num_rows()==0) // SI NO HAY SUB-PRODUCTOS EN LA UNIDAD
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
           $tabla.='<td style="text-align:left" >';
           $tabla.='<img src="'.base_url().'imagenes/meta.png" '.'
                     style="width:20px"
                     onclick="javascript:revisarEjecucion('.$idsp.')"
                     class="BotonIco botoncito" title="Clic para Revisar ejecución"
                     />';
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
  
  // Carga la ejecucion registrada para el subproducto y año dados
  function revisarEjecucion()
  {
    if (!$this->input->is_ajax_request()) die('Acceso Denegado');//Si la peticion NO vino por AJAX
    date_default_timezone_set('America/Caracas');
    
    $ahora=  getdate(time());
    $year=$ahora['year']; // El año Actual
    
    $yearpoa = $this->input->post('yearpoa');
    $id_subproducto = $this->input->post('id_subproducto');
    
    $subproducto = $this->Productos->obtener_subproducto($id_subproducto);
    if (!$subproducto) die('ERROR: SUB-PRODUCTO NO EXISTE');
    
    // Boton de Registro de Ejecución
    $botonRegistrar='';
    if ($year == $yearpoa)
    {
      $botonRegistrar='<br/><center><div class="BotonIco" onclick="javascript:registrarEjecucion('.
                        $id_subproducto.')" title="Registrar Ejecución">';
      $botonRegistrar.='<img src="imagenes/meta.png" style="padding-top:4px"/>&nbsp;';   
      $botonRegistrar.='Registrar';
      $botonRegistrar.= '</div></center>';
    }
    
    $encabezado = $this->load->view('ejecucion_productos/encabezadoSubproducto', $subproducto, true);  
    
    $ejecucion = $this->Productos->revisarEjecucion($id_subproducto, $yearpoa);
    
    $cuerpo='';
    if ($ejecucion->num_rows()==0) // SI NO HAY REGISTROS DE EJECUCION
        $cuerpo='<h2><center>Sub-Producto sin Registros de ejecución para este año</center></h2>';      
    else 
    {
        $data['ejecucion'] = $ejecucion->result();
        $cuerpo = $this->load->view('ejecucion_productos/registroEjecucion', $data, true);  
    }    
    
    die($encabezado.$cuerpo.$botonRegistrar);
  }
  
  // Revisión del registro de ejecución
  function revisarRegistro()
  {
    if (!$this->input->is_ajax_request()) die('Acceso Denegado');//Si la peticion NO vino por AJAX
    date_default_timezone_set('America/Caracas');
    
    $ahora=  getdate(time());
    $year = $ahora['year']; // El año Actual
    
    $yearpoa = $this->input->post('yearpoa');
    $idEjecucion = $this->input->post('idEjecucion');
    
    $registro = $this->Productos->obtenerRegistro($idEjecucion);
    if (!$registro) die('ERROR: REGISTRO NO EXISTE');
    
    $u = $this->_listar_usuarios($registro->id_estructura, $registro->id_usuario);
    
    $data['u'] = $u;
    $data['registro'] = $registro;
    $data['edicion'] = ($year == $yearpoa)? TRUE:FALSE;
    
    $this->load->view('ejecucion_productos/revisarRegistro',$data);
  }
  
  function actualizarRegistro()
  {
      if (!$this->input->is_ajax_request()) die('Acceso Denegado');
      
      $donde=array(
                'id_ejecucion'  => intval($this->input->post('idEjecucion'))       
                  );
      $datos=array(                
              'descripcion'        => $this->input->post('descripcion'),
              'cantidad_ejecutada' => $this->input->post('cantidadEjecutada'),
              'id_usuario'         => $this->input->post('idUsuario'),
              'fecha_ejecucion'    => $this->input->post('fechaEjecucion')              
              );        
      $actualizado=$this->Crud->actualizar_registro('c_ejec_productos', $datos, $donde);        
      if (!$actualizado){die('Error');}
      else 
      {           
         $registro='id_ejecucion: '.$donde['id_ejecucion'];         
         $registro.='. Actualizado por: '.$this->session->userdata('usuario');
         $bitacora=array(
             'direccion_ip'   =>$this->session->userdata('ip_address'),
             'navegador'      =>$this->session->userdata('user_agent'),
             'id_usuario'     =>$this->session->userdata('id_usuario'),
             'controlador'    =>$this->uri->uri_string(),
             'tabla_afectada' =>'c_ejec_productos',
             'tipo_accion'    =>'UPDATE',
             'registro'       =>$registro
         );
         $this->Crud->insertar_registro('z_bitacora', $bitacora);            
      };   
  }
  
  // Registrar ejecución
  function registrarEjecucion()
  {
    if (!$this->input->is_ajax_request()) die('Acceso Denegado');//Si la peticion NO vino por AJAX
     
    $idSp = $this->input->post('idSp');
    
    $sp = $this->Productos->obtener_subproducto($idSp);
    if (!$sp) die('ERROR: SUB-PRODUCTO NO EXISTE');
    
    $u = $this->_listar_usuarios($sp['id_estructura']);
    
    $data['u'] = $u;
    $data['sp'] = $sp;
    
    $this->load->view('ejecucion_productos/registrarEjecucion',$data);
  }  
  
  // Guardar Registro de Ejecución
  function guardarRegistro()
  {   
    if (!$this->input->is_ajax_request()) die('Acceso Denegado');        
        $datos=array(
                'id_subproducto'=> $this->input->post('idSp'),
            'cantidad_ejecutada'=> $this->input->post('cantidadEjecutada'),
                'descripcion'   => $this->input->post('descripcion'),
                'id_usuario'    => $this->input->post('idUsuario'),
              'fecha_ejecucion' => $this->input->post('fechaEjecucion')                
                );
        $insertado=$this->Crud->insertar_registro('c_ejec_productos', $datos);
        if (!$insertado){die('Error');}
        else
        {
           $registro='id_ejecucion: '.$this->db->insert_id();
           $registro.='. '.$datos['descripcion'];           
           $registro.='. Registrado por: '.$this->session->userdata('usuario');
           $bitacora=array(
               'direccion_ip'   =>$this->session->userdata('ip_address'),
               'navegador'      =>$this->session->userdata('user_agent'),
               'id_usuario'     =>$this->session->userdata('id_usuario'),
               'controlador'    =>$this->uri->uri_string(),
               'tabla_afectada' =>'c_ejec_productos',
               'tipo_accion'    =>'INSERT',
               'registro'       =>$registro
           );
           $this->Crud->insertar_registro('z_bitacora', $bitacora);             
        }    
  }
  
  function _listar_usuarios($id_estructura, $id_usuario=0)
  {        
    $usuarios=$this->Usuarios->listar_usuarios($id_estructura);
    if (!$usuarios)return '<option selected="selected" value="0">No Existen Usuarios</option>';
    $lista=($id_usuario==0)?'<option selected="selected" value="0">[Seleccione Usuario]</option>':'';
    foreach ($usuarios as $fila)
    {
      $user = $fila['nombre'].' '.$fila['apellido'];
      $iduser = $fila['id_usuario'];
      if ($fila['id_usuario'] == $id_usuario)
      {
        $lista.='<option selected="selected" value="'.$iduser.'">'.$user.'</option>';
      }
      else
      {
        $lista.='<option value="'.$iduser.'">'.$user.'</option>';
      }
    }        
    return $lista;
  }
  
  function eliminarRegistro()
  {
    if (!$this->input->is_ajax_request()) die('Acceso Denegado');
    $quien=array(
        'id_ejecucion' => intval($this->input->post('idEjecucion'))
                   );        
    $borrado=$this->Crud->eliminar_registro('c_ejec_productos', $quien);
    if (!$borrado){die('Error');}
    else
    {
       $registro='id_ejecucion: '.$quien['id_ejecucion'];           
       $registro.='. Borrado por: '.$this->session->userdata('usuario');
       $bitacora=array(
           'direccion_ip'   =>$this->session->userdata('ip_address'),
           'navegador'      =>$this->session->userdata('user_agent'),
           'id_usuario'     =>$this->session->userdata('id_usuario'),
           'controlador'    =>$this->uri->uri_string(),
           'tabla_afectada' =>'c_ejec_productos',
           'tipo_accion'    =>'DELETE',
           'registro'       =>$registro
       );
       $this->Crud->insertar_registro('z_bitacora', $bitacora); 
    }
  }
    
  function _cabecetabla($total=false)
  {    
    $tabla='<thead><tr><th width="40px">Nº</th>';
    $tabla.='<th>Nombre del Sub-Producto</th>';
    $tabla.='<th width="40px" ></th>';
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
    $tabla.='<td ></td>';
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