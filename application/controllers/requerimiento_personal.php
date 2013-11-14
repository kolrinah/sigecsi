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
class Requerimiento_personal extends CI_Controller {
  function __construct() 
  {
     parent::__construct();
     $this->load->helper('form');
     //$this->load->library('form_validation');
     $this->load->model('Usuarios');
     $this->load->model('Estructura');
     $this->load->model('Personal');
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
    $data['titulo']='Requerimiento de Personal';
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
    
    $data['contenido']='requerimiento_personal/requerimiento_personal';    
    $data['script']='<!-- Cargamos CSS de DataTables -->'."\n";    
    $data['script'].="\t".'<link rel="stylesheet" type="text/css" media="all" href="'.base_url().'css/dataTables.css"/>'."\n";
    $data['script'].='<!-- Cargamos JS para DataTables -->'."\n";
    $data['script'].="\t".'<script type="text/javascript" src="'.base_url().'js/jquery.dataTables.js"></script>'."\n";
    $data['script'].='<!-- Cargamos Nuestro JS -->'."\n";
    $data['script'].="\t".'<script type="text/javascript" src="'.base_url().'js/requerimiento_personal.js"></script>'."\n";
       
     $data['tabla']=$this->listar_personal($year_poa);    
    
 // CARGAMOS LA VISTA   
    $this->load->view('plantillas/plantilla_general',$data);  
  }  
  
  //////////////////////////////////////////////////////////////////////////////////////////
  
  function listar_personal($yearpoa=0)
  {
    if ($this->input->is_ajax_request()) $yearpoa=$this->input->post('yearpoa'); // Si la peticion vino por AJAX
    date_default_timezone_set('America/Caracas');
    $id_estructura=$this->session->userdata('id_estructura');
    $estructura=$this->Estructura->obtener_estructura($id_estructura);
    if (!$estructura)die('error consultando estructuras');
    $tope=strtotime($estructura['fecha_tope']);
    $ahora=time();
    $fecha=getdate(time());
    
    $botonrequerir='';
    if (($ahora<$tope) && ($yearpoa>=$fecha['year']))
    {
      $botonrequerir='<center><div class="BotonIco" onclick="javascript:RequerirPersonal('.$id_estructura.')" title="Requerir Personal">';
      $botonrequerir.='<img src="imagenes/requerir.png"/>&nbsp;&nbsp;&nbsp;';   
      $botonrequerir.='Requerir';
      $botonrequerir.= '</div></center>';
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
    $personal=$this->Personal->listar_requerimiento_personal($estructuras,$yearpoa);  
    
    if ($personal->num_rows() === 0) // SI NO HAY REQUERIMIENTO DE PERSONAL EN LA UNIDAD
    { 
      $tabla='<h2><center>La Unidad No Posee Requerimiento de Personal para el año indicado</center></h2>';
      $tabla.=$botonrequerir;
      if ($this->input->is_ajax_request()) die($tabla); // Si la peticion vino por AJAX
      else return $tabla;
    }  

    $n=$personal->num_rows();
    $personal=$personal->result();
    
    // CONSTRUIMOS LA TABLA     
    $i=0;
    $id_estructura=$personal[$i]->id_estructura; // OBTENGO LA PRIMERA ESTRUCTURA
    $ac=$personal[$i]->accion_centralizada;
    
    $tabla='';
    $tabla.=$botonrequerir;
    while($i<$n)
    {
        // ENCABEZADOS DE TABLA
        $tabla.='<table width="100%"><tr><td style="vertical-align:middle; text-align:left">';
        $tabla.='<h4>'.$personal[$i]->codigo.' - '.$personal[$i]->descripcion.'</h4>';
        $tabla.='</td>';
        $tabla.='<td width="10%" style="vertical-align:middle; text-align:right; padding:0 10px 5px 0">';
        $tope=strtotime($personal[$i]->fecha_tope);
        $ahora=time();        
        $img=($ahora>$tope)?'cerrado.png':'abierto.png';        
        
        $tabla.='<img src="'.base_url().'imagenes/'.$img.'" ';
        $tabla.='title="Fecha Tope: '.date("d/m/Y",strtotime($personal[$i]->fecha_tope)).'"';
        $tabla.='/>';
        $tabla.='</td>';
        $tabla.='</tr></table>';                
        
        while($id_estructura==($personal[$i]->id_estructura)) 
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
          while (($ac==$personal[$i]->accion_centralizada) && ($id_estructura==($personal[$i]->id_estructura)))
          {
             $tope=strtotime($personal[$i]->fecha_tope);
             $ahora=time();
             $editable=0;
             if (($ahora<$tope) && ($yearpoa>=$fecha['year']))
             {
                $editable=1;
             }
             
             $accion=' onclick="RevisarFicha('.$personal[$i]->id_requerimiento_personal.','.$editable.');" ';
             
             $tabla.='<tr class="Resaltado"'.$accion.'>';
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
             if ($editable==1)
             {
               $accion=' onclick="EliminarRequerimiento('.$personal[$i]->id_requerimiento_personal.');" ';
               $img='borrar.png';
               $tabla.='<img src="'.base_url().'imagenes/'.$img.'" ';
               $tabla.='title="Eliminar Requerimiento"'.$accion;
               $tabla.='/>';   
             }             
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
        @$id_estructura = $personal[$i]->id_estructura;
    }
    if ($this->input->is_ajax_request()) die($tabla); // Si la peticion vino por AJAX
    else return $tabla;
  }
  
  function revisar_ficha()
  {
      if (!$this->input->is_ajax_request()) die('Acceso Denegado');
      date_default_timezone_set('America/Caracas'); // Establece la Hora de Venezuela para funciones de fecha y hora
      
      $id_requerimiento_personal= intval($this->input->post('id_requerimiento_personal'));
      $editable=intval($this->input->post('editable'));
      
      $rp=$this->Personal->obtener_rp($id_requerimiento_personal);
      if (!$rp)die('error consultando rp');
      
      $id_estructura=$this->session->userdata('id_estructura');
      $yearpoa=intval($this->input->post('yearpoa'));      
      $proyectos=$this->Crud->contar_items('p_proyectos',array('id_estructura'=>$id_estructura,
                                                               'yearpoa'=>$yearpoa));
                  
      $tabla='<table width="100%"><tr>';
      $tabla.='<td width="30px" class="BotonIco">';
      $accion=' onclick="javascript:Actualiza();" ';        
      $tabla.='<img src="'.base_url().'imagenes/back.png"'.$accion;
      $tabla.='title="clic para regresar"';
      $tabla.='/>'; 
      $tabla.='</td>';
      $tabla.='<td style="vertical-align:middle; text-align:left">';
      $tabla.='<h4>'.$rp->codigo.' - '.$rp->descripcion.'</h4>';
      $tabla.='</td>';
      $tabla.='<td width="10%" style="vertical-align:middle; text-align:right;">';
      
      $img=($editable==1)?'abierto.png':'cerrado.png';        
        
      $tabla.='<img src="'.base_url().'imagenes/'.$img.'" ';
      $tabla.='title="Fecha Tope: '.date("d/m/Y",strtotime($rp->fecha_tope)).'"';
      $tabla.='/>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='</table>';     
      
      $readonly=($editable==0)?" readonly='readonly'":'';
      $edit=($editable==0)?'':' Editable';
      
      $tabla.='<div class="EntraDatos">';
      $tabla.='<table style="margin-top:0px!important">';
      $tabla.='<thead>';
      $tabla.='<tr><th>';            
      $tabla.='Requerimiento de Personal';  
      $tabla.='</th></tr>';           
      $tabla.='</thead>';
      $tabla.='<tbody>'; 
      $tabla.='<tr>'; 
      $tabla.='<td>';
      $tabla.='<label>Fuente Presupuestaria:</label>';
      $tabla.='<select class="Campos'.$edit.
              '" id="FuentePresupuestaria" title="Fuente Presupuestaria" tabindex="1" ';
      $tabla.=' onchange="CambioFuente();">';
      switch ($rp->accion_centralizada)
      {
          case 't': // Accion Centralizada
               $tabla.='<option selected="selected" value="t">Acción Centralizada</option>';               
               $tabla.=($proyectos>0)?'<option value="f">Proyecto</option>':'';  
               
               break;
          default: // Proyecto
               $tabla.=($proyectos>0)?'<option selected="selected" value="f">Proyecto</option>':'';
               $tabla.='<option value="t">Acción Centralizada</option>';
               break; 
      }
      $tabla.='</select>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>';    
      $tabla.='<td>';
      $opciones='<option value="0">ERROR AL CARGAR LA DATA</option>';
      $tipos=$this->Crud->listar_registros('rp_tipo_personal');      
      if ($tipos->num_rows()>0)
      {
         $a=array('0'); $b=array('[Seleccione]');
         foreach ($tipos->result() as $fila)
         {
           array_push($a,$fila->id_tipo_personal);
           array_push($b,$fila->tipo_personal);
         }         
         $opciones=array_combine($a,$b);
         ksort($opciones);
         $opciones=$this->_construye_opciones($opciones, $rp->id_tipo_personal);
      }      
      $tabla.='<label>Tipo de Personal:</label>';
      $tabla.='<select class="Campos'.$edit.
              '" id="TipoPersonal" title="Tipo de Personal" tabindex="2" ';
      $tabla.=' onchange="CambioTipo($(this).val());">';
      $tabla.=$opciones;
      $tabla.='</select>'; 
      $tabla.='</td>';      
      $tabla.='</tr>';
      $tabla.='<tr>';    
      $tabla.='<td>';
      $opciones='<option value="0">ERROR AL CARGAR LA DATA</option>';
      $tipos=$this->Crud->listar_registros('rp_personal',array('id_tipo_personal'=>$rp->id_tipo_personal));      
      if ($tipos->num_rows()>0)
      {
         $a=array('0'); $b=array('[Seleccione]');
         foreach ($tipos->result() as $fila)
         {
           array_push($a,$fila->id_personal);
           array_push($b,$fila->personal);
         }         
         $opciones=array_combine($a,$b);
         ksort($opciones);
         $opciones=$this->_construye_opciones($opciones, $rp->id_personal);
      }      
      $tabla.='<label>Personal:</label>';
      $tabla.='<select class="Campos'.$edit.
              '" id="Personal" title="Personal Requerido" tabindex="3">';
      $tabla.=$opciones;
      $tabla.='</select>'; 
      $tabla.='</td>';
      $tabla.='</tr>';  
      $tabla.='<tr>';  
      $tabla.='<td>';
      $tabla.='<img src="'.base_url().'imagenes/femenino.png" title="Cantidad de Personal Femenino"/>'; 
      $tabla.='&nbsp;&nbsp;&nbsp;<input type="text" id="Femenino" size="4" class="Nro'.$edit.'" value="';
      $tabla.=number_format($rp->femenino,0,',','.').'" tabindex="4" title="Cantidad de Personal Femenino"'.$readonly;
      //onlyDigits(e, value, allowDecimal, allowNegative, allowThousand, decSep, thousandSep, decLength)
      $tabla.=' onblur="this.value=formatNumber(this.value,0);"';
      $tabla.=' onkeyup="formatNumber(this.value,0);" ';
      $tabla.=' onkeypress="return onlyDigits(event, this.value, false,false,false,\',\',\'.\',0);" ';
      $tabla.=' />';  
      $tabla.='</td>';        
      $tabla.='</tr>';  
      $tabla.='<tr>';        
      $tabla.='<td>';  
      $tabla.='<img src="'.base_url().'imagenes/masculino.png" title="Cantidad de Personal Masculino"/>'; 
      $tabla.='&nbsp;&nbsp;&nbsp;<input type="text" id="Masculino" size="4" class="Nro'.$edit.'" value="';
      $tabla.=number_format($rp->masculino,0,',','.').'" tabindex="5" title="Cantidad de Personal Masculino"'.$readonly;
      //onlyDigits(e, value, allowDecimal, allowNegative, allowThousand, decSep, thousandSep, decLength)
      $tabla.=' onblur="this.value=formatNumber(this.value,0);"';
      $tabla.=' onkeyup="formatNumber(this.value,0);" ';
      $tabla.=' onkeypress="return onlyDigits(event, this.value, false,false,false,\',\',\'.\',0);" ';
      $tabla.=' />';  
      $tabla.='</td>';        
      $tabla.='</tr>';   
      $tabla.='</tbody>';
      
      $tabla.='<tfoot>';
      $tabla.='<tr><td colspan="2">';
      if ($editable==1)
      {
        $tabla.='<div class="BotonIco" onclick="javascript:ActualizarFicha('.$rp->id_requerimiento_personal.')" title="Guardar Cambios">';
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

  function cambiar_fuente_presupuestaria()
  {
      if (!$this->input->is_ajax_request()) die('Acceso Denegado');
      
      $fuente = trim($this->input->post('fuente'));
      
      $opciones='<option value="0">ERROR AL CARGAR LA DATA</option>';
      $tipos=($fuente=='t')?$this->Crud->listar_registros('rp_tipo_personal'):
                            $this->Crud->listar_registros('rp_tipo_personal',array('id_tipo_personal'=>4),
                                                                             array('id_tipo_personal'=>7)); 
      if ($tipos->num_rows()>0)
      {
         $a=array('0'); $b=array('[Seleccione]');
         foreach ($tipos->result() as $fila)
         {
           array_push($a,$fila->id_tipo_personal);
           array_push($b,$fila->tipo_personal);
         }         
         $opciones=array_combine($a,$b);
         ksort($opciones);
         $opciones=$this->_construye_opciones($opciones, 0);
      }
      
      die($opciones);
  }  
  
  function cambiar_tipo_personal()
  {
      if (!$this->input->is_ajax_request()) die('Acceso Denegado');
      $id_tipo_personal  = intval($this->input->post('id_tipo_personal'));
      $fuente  = trim($this->input->post('fuente'));
      
      $opciones='<option value="0">ERROR AL CARGAR LA DATA</option>';
      $tipos=$this->Crud->listar_registros('rp_personal',array('id_tipo_personal'=>$id_tipo_personal));      
      if ($tipos->num_rows()>0)
      {
         $a=array('0'); $b=array('[Seleccione]');
         foreach ($tipos->result() as $fila)
         {
           array_push($a,$fila->id_personal);
           array_push($b,$fila->personal);
         }         
         $opciones=array_combine($a,$b);
         ksort($opciones);
         $opciones=$this->_construye_opciones($opciones);
      } 
      
      // CONDICION ESPECIAL PARA PROYECTOS Y OBREROS
      if ($fuente=='f' and $id_tipo_personal==4) 
          $opciones='<option value="29" selected="selected">Obrero Temporal</option>';
      
      die($opciones);
  }
  
  function actualizar_ficha()
  {
      if (!$this->input->is_ajax_request()) die('Acceso Denegado');
      
      $donde=array(
                'id_requerimiento_personal' => intval($this->input->post('id_requerimiento_personal'))
                  );
      
      $datos=array(            
                'accion_centralizada'=>$this->input->post('accion_centralizada'),
                'id_personal'=>intval($this->input->post('id_personal')),
                'femenino'=>str_ireplace(",",".",str_ireplace(".","",$this->input->post('femenino'))),
                'masculino'=>str_ireplace(",",".",str_ireplace(".","",$this->input->post('masculino')))
                  );
      
      $actualizado=$this->Crud->actualizar_registro('rp_requerimiento_personal', $datos, $donde);        
      if (!$actualizado){die('Error');}
      else 
      {           
         $registro='id_requerimiento_personal: '.$donde['id_requerimiento_personal'];         
         $registro.='. Actualizado por: '.$this->session->userdata('usuario');
         $bitacora=array(
             'direccion_ip'   =>$this->session->userdata('ip_address'),
             'navegador'      =>$this->session->userdata('user_agent'),
             'id_usuario'     =>$this->session->userdata('id_usuario'),
             'controlador'    =>$this->uri->uri_string(),
             'tabla_afectada' =>'rp_requerimiento_personal',
             'tipo_accion'    =>'UPDATE',
             'registro'       =>$registro
         );
         $this->Crud->insertar_registro('z_bitacora', $bitacora);            
      };   
  }
  
  function requerir_personal()
  {
      if (!$this->input->is_ajax_request()) die('Acceso Denegado');
      
      $yearpoa=intval($this->input->post('yearpoa'));
      $id_estructura=intval($this->input->post('id_estructura'));
      
      $proyectos=$this->Crud->contar_items('p_proyectos',array('id_estructura'=>$id_estructura,
                                                               'yearpoa'=>$yearpoa));              
         
      $tabla='<table width="100%"><tr>';
      $tabla.='<td style="vertical-align:middle; text-align:left">';
      $tabla.='<h4>'.$this->session->userdata('cod_estruct').' - '.$this->session->userdata('nombre_estruct').'</h4>';
      $tabla.='</td>';      
      $tabla.='</tr>';
      $tabla.='</table>';     
      
      $tabla.='<div class="EntraDatos">';
      $tabla.='<table style="margin-top:0px!important">';
      $tabla.='<thead>';
      $tabla.='<tr><th>';            
      $tabla.='Requerimiento de Personal';  
      $tabla.='</th></tr>';           
      $tabla.='</thead>';
      $tabla.='<tbody>'; 
      $tabla.='<tr>'; 
      $tabla.='<td>';
      $tabla.='<label>Fuente Presupuestaria:</label>';
      $tabla.='<select class="Campos Editable" id="FuentePresupuestaria" title="Fuente Presupuestaria" tabindex="1" ';
      $tabla.=' onchange="CambioFuente();">';
      $tabla.='<option selected="selected" value="t">Acción Centralizada</option>';
      $tabla.=($proyectos>0)?'<option value="f">Proyecto</option>':'';
      $tabla.='</select>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>';    
      $tabla.='<td>';    
      $tabla.='<label>Tipo de Personal:</label>';
      $tabla.='<select class="Campos Editable" id="TipoPersonal" title="Tipo de Personal" tabindex="2"';
      $tabla.=' onchange="CambioTipo($(this).val());">';
      $tabla.='<option value="0">[Seleccione]</option>'; 
      $tabla.='</select>'; 
      $tabla.='</td>';      
      $tabla.='</tr>';
      $tabla.='<tr>';    
      $tabla.='<td>';
      $tabla.='<label>Personal:</label>';
      $tabla.='<select class="Campos Editable" id="Personal" title="Personal Requerido" tabindex="3">';
      $tabla.='<option value="0">[Seleccione]</option>'; 
      $tabla.='</select>'; 
      $tabla.='</td>';
      $tabla.='</tr>';  
      $tabla.='<tr>';  
      $tabla.='<td>';
      $tabla.='<img src="'.base_url().'imagenes/femenino.png" title="Cantidad de Personal Femenino"/>'; 
      $tabla.='&nbsp;&nbsp;&nbsp;<input type="text" id="Femenino" size="4" class="Nro Editable" value="0"';
      $tabla.=' tabindex="4" title="Cantidad de Personal Femenino"';
      //onlyDigits(e, value, allowDecimal, allowNegative, allowThousand, decSep, thousandSep, decLength)
      $tabla.=' onblur="this.value=formatNumber(this.value,0);"';
      $tabla.=' onkeyup="formatNumber(this.value,0);" ';
      $tabla.=' onkeypress="return onlyDigits(event, this.value, false,false,false,\',\',\'.\',0);" ';
      $tabla.=' />';      
      $tabla.='</td>';        
      $tabla.='</tr>';  
      $tabla.='<tr>';        
      $tabla.='<td>';  
      $tabla.='<img src="'.base_url().'imagenes/masculino.png" title="Cantidad de Personal Masculino"/>'; 
      $tabla.='&nbsp;&nbsp;&nbsp;<input type="text" id="Masculino" size="4" class="Nro Editable" value="0"';
      $tabla.=' tabindex="5" title="Cantidad de Personal Masculino"';
      //onlyDigits(e, value, allowDecimal, allowNegative, allowThousand, decSep, thousandSep, decLength)
      $tabla.=' onblur="this.value=formatNumber(this.value,0);"';
      $tabla.=' onkeyup="formatNumber(this.value,0);" ';
      $tabla.=' onkeypress="return onlyDigits(event, this.value, false,false,false,\',\',\'.\',0);" ';
      $tabla.=' />';       
      $tabla.='</td>';        
      $tabla.='</tr>';   
      $tabla.='</tbody>';
      
      $tabla.='<tfoot>';
      $tabla.='<tr><td colspan="2">';
      $tabla.='<div class="BotonIco" onclick="javascript:GuardarFicha('.$this->session->userdata("id_estructura").')" title="Guardar">';
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
  
  function guardar_ficha()
  {   
      if (!$this->input->is_ajax_request()) die('Acceso Denegado');

      $datos=array(            
                'id_estructura'=>intval($this->input->post('id_estructura')),
                'yearpoa'=>intval($this->input->post('yearpoa')),
                'accion_centralizada'=>trim($this->input->post('accion_centralizada')),
                'id_personal'=>intval($this->input->post('id_personal')),
                'femenino'=>str_ireplace(",",".",str_ireplace(".","",$this->input->post('femenino'))),
                'masculino'=>str_ireplace(",",".",str_ireplace(".","",$this->input->post('masculino')))
                  );    
        
      $insertado=$this->Crud->insertar_registro('rp_requerimiento_personal', $datos);
      if (!$insertado){die('Error');}
      else
      {
        $registro='id_requerimiento_personal: '.$this->db->insert_id();          
        $registro.='. Registrado por: '.$this->session->userdata('usuario');
        $bitacora=array(
            'direccion_ip'   =>$this->session->userdata('ip_address'),
            'navegador'      =>$this->session->userdata('user_agent'),
            'id_usuario'     =>$this->session->userdata('id_usuario'),
            'controlador'    =>$this->uri->uri_string(),
            'tabla_afectada' =>'rp_requerimiento_personal',
            'tipo_accion'    =>'INSERT',
            'registro'       =>$registro
        );
        $this->Crud->insertar_registro('z_bitacora', $bitacora);             
      }    
  }
  
  function eliminar_requerimiento()
  {
    if (!$this->input->is_ajax_request()) die('Acceso Denegado');
    $donde=array(
        'id_requerimiento_personal' => intval($this->input->post('id_requerimiento_personal'))
                   );        
    $borrado=$this->Crud->eliminar_registro('rp_requerimiento_personal', $donde);
    if (!$borrado){die('Error');}
    else
    {
       $registro='id_requerimiento_personal: '.$donde['id_requerimiento_personal'];           
       $registro.='. Borrado por: '.$this->session->userdata('usuario');
       $bitacora=array(
           'direccion_ip'   =>$this->session->userdata('ip_address'),
           'navegador'      =>$this->session->userdata('user_agent'),
           'id_usuario'     =>$this->session->userdata('id_usuario'),
           'controlador'    =>$this->uri->uri_string(),
           'tabla_afectada' =>'rp_requerimiento_personal',
           'tipo_accion'    =>'DELETE',
           'registro'       =>$registro
       );
       $this->Crud->insertar_registro('z_bitacora', $bitacora); 
    }
  }
  
  // Construye las opciones de Combo-Select a partir de una matriz
  function _construye_opciones($opciones, $seleccionada=0)  
  {
    if (!isset($seleccionada)){$seleccionada=0;}
    $combo='';
    foreach ($opciones as $value => $text)
    {
      if ($value == $seleccionada)
      {
        $combo.='<option value="'.$value.'" selected="selected">'.$text.'</option>';
      }
      else
      {
        $combo.='<option value="'.$value.'">'.$text.'</option>';
      }
    }
    return $combo;
  }  
}