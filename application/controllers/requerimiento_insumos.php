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
class Requerimiento_insumos extends CI_Controller {
  function __construct() 
  {
     parent::__construct();
     $this->load->helper('form');
     //$this->load->library('form_validation');
     $this->load->model('Usuarios');
     $this->load->model('Estructura');
     $this->load->model('Insumos');
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
    $data['titulo']='Requerimiento de Insumos';
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
    
    $data['contenido']='requerimiento_insumos/requerimiento_insumos';    
    $data['script']='<!-- Cargamos CSS de DataTables -->'."\n";    
    $data['script'].="\t".'<link rel="stylesheet" type="text/css" media="all" href="'.base_url().'css/dataTables.css"/>'."\n";
    $data['script'].='<!-- Cargamos JS para DataTables -->'."\n";
    $data['script'].="\t".'<script type="text/javascript" src="'.base_url().'js/jquery.dataTables.js"></script>'."\n";
    $data['script'].='<!-- Cargamos Nuestro JS -->'."\n";
    $data['script'].="\t".'<script type="text/javascript" src="'.base_url().'js/requerimiento_insumos.js"></script>'."\n";
       
     $data['tabla']=$this->listar_insumos($year_poa);    
    
 // CARGAMOS LA VISTA   
    $this->load->view('plantillas/plantilla_general',$data);  
  }  
  
  //////////////////////////////////////////////////////////////////////////////////////////
  
  function listar_insumos($yearpoa=0)
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
      $botonrequerir='<center><div class="BotonIco" onclick="javascript:RequerirInsumo('.$id_estructura.')" title="Requerir Insumo">';
      $botonrequerir.='<img src="imagenes/insumos.png"/>&nbsp;&nbsp;&nbsp;';   
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
    $insumos=$this->Insumos->listar_requerimiento_insumos($estructuras,$yearpoa);  
    
    if ($insumos->num_rows() === 0) // SI NO HAY REQUERIMIENTO DE INSUMOS EN LA UNIDAD
    { 
      $tabla='<h2><center>La Unidad No Posee Requerimiento de Insumos para el año indicado</center></h2>';
      $tabla.=$botonrequerir;
      if ($this->input->is_ajax_request()) die($tabla); // Si la peticion vino por AJAX
      else return $tabla;
    }  

    $n=$insumos->num_rows();
    $insumos=$insumos->result();
    
    // CONSTRUIMOS LA TABLA     
    $i=0;
    $id_estructura=$insumos[$i]->id_estructura; // OBTENGO LA PRIMERA ESTRUCTURA
    $ti=$insumos[$i]->partida_generica;
    
    $tabla='';
    $tabla.=$botonrequerir;
    while($i<$n)
    {
        // ENCABEZADOS DE TABLA
        $tabla.='<table width="100%"><tr><td style="vertical-align:middle; text-align:left">';
        $tabla.='<h4>'.$insumos[$i]->codigo.' - '.$insumos[$i]->descripcion.'</h4>';
        $tabla.='</td>';
        $tabla.='<td width="10%" style="vertical-align:middle; text-align:right; padding:0 10px 5px 0">';
        $tope=strtotime($insumos[$i]->fecha_tope);
        $ahora=time();        
        $img=($ahora>$tope)?'cerrado.png':'abierto.png';        
        
        $tabla.='<img src="'.base_url().'imagenes/'.$img.'" ';
        $tabla.='title="Fecha Tope: '.date("d/m/Y",strtotime($insumos[$i]->fecha_tope)).'"';
        $tabla.='/>';
        $tabla.='</td>';
        $tabla.='</tr></table>';                
        
        while($id_estructura==($insumos[$i]->id_estructura)) 
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
          
          while ($ti==$insumos[$i]->partida_generica && $id_estructura==($insumos[$i]->id_estructura))
          {
             $tope=strtotime($insumos[$i]->fecha_tope);
             $ahora=time();
             $editable=0;
             if (($ahora<$tope) && ($yearpoa>=$fecha['year']))
             {
                $editable=1;
             }
             
             $accion=' onclick="RevisarFicha('.$insumos[$i]->id_requerimiento_insumo.','.$editable.');" ';
             
             $tabla.='<tr class="Resaltado"'.$accion.'>';
             $tabla.='<td title="Código Sigesp">';
             $tabla.=trim($insumos[$i]->codart);
             $tabla.='</td>';
             $tabla.='<td title="Partida Presupuestaria">';
             $tabla.=$insumos[$i]->spg_cuenta;
             $tabla.='</td>';              
             $tabla.='<td title="Nombre del Insumo" style="text-align:left!important">';
             $tabla.=mb_convert_case(trim($insumos[$i]->denart), MB_CASE_UPPER);
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
             if ($editable==1)
             {
               $accion=' onclick="EliminarRequerimiento('.$insumos[$i]->id_requerimiento_insumo.');" ';
               $img='borrar.png';
               $tabla.='<img src="'.base_url().'imagenes/'.$img.'" ';
               $tabla.='title="Eliminar Requerimiento"'.$accion;
               $tabla.='/>';   
             }             
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
          $tabla.='<td>'; 
          $tabla.='</td>';
          $tabla.='<td>';
          $tabla.='</td>';           
          $tabla.='</tr>';
          $tabla.='</tfoot>';
          $tabla.='</table><br/><br/>';   
          if ($i==$n) break;          
        }
        @$id_estructura = $insumos[$i]->id_estructura;
    }

    if ($this->input->is_ajax_request()) die($tabla); // Si la peticion vino por AJAX
    else return $tabla;
  }
  
  function revisar_ficha()
  {
      if (!$this->input->is_ajax_request()) die('Acceso Denegado');
      date_default_timezone_set('America/Caracas'); // Establece la Hora de Venezuela para funciones de fecha y hora
      
      $id_requerimiento_insumo= intval($this->input->post('id_requerimiento_insumo'));
      $editable=intval($this->input->post('editable'));
      
      $ri=$this->Insumos->obtener_ri($id_requerimiento_insumo);
      if (!$ri)die('error consultando ri');
      
      $id_estructura=$this->session->userdata('id_estructura');
      $yearpoa=intval($this->input->post('yearpoa'));      
                  
      $tabla='<table width="100%"><tr>';
      $tabla.='<td width="30px" class="BotonIco">';
      $accion=' onclick="javascript:Actualiza();" ';        
      $tabla.='<img src="'.base_url().'imagenes/back.png"'.$accion;
      $tabla.='title="clic para regresar"';
      $tabla.='/>'; 
      $tabla.='</td>';
      $tabla.='<td style="vertical-align:middle; text-align:left">';
      $tabla.='<h4>'.$ri->codigo.' - '.$ri->descripcion.'</h4>';
      $tabla.='</td>';
      $tabla.='<td width="10%" style="vertical-align:middle; text-align:right;">';
      
      $img=($editable==1)?'abierto.png':'cerrado.png';        
        
      $tabla.='<img src="'.base_url().'imagenes/'.$img.'" ';
      $tabla.='title="Fecha Tope: '.date("d/m/Y",strtotime($ri->fecha_tope)).'"';
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
      $tabla.='Requerimiento de Insumo';  
      $tabla.='</th></tr>';           
      $tabla.='</thead>';
      $tabla.='<tbody>';      
      $tabla.='<tr>';    
      $tabla.='<td>';
      $opciones='<option value="0">ERROR AL CARGAR LA DATA</option>';
      $tipos=$this->Crud->listar_registros('ri_tipo_insumos');      
      if ($tipos->num_rows()>0)
      {
         $a=array('0'); $b=array('[Seleccione]');
         foreach ($tipos->result() as $fila)
         {
           array_push($a,$fila->partida_generica);
           array_push($b,$fila->tipo_insumo);
         }         
         $opciones=array_combine($a,$b);
         ksort($opciones);
         $opciones=$this->_construye_opciones($opciones, $ri->partida_generica);
      }      
      $tabla.='<label>Tipo de Insumo:</label>';
      $tabla.='<select class="Campos'.$edit.
              '" id="TipoInsumo" title="Tipo de Insumo" tabindex="1" ';
      $tabla.=' onchange="CambioTipo($(this).val());">';
      $tabla.=$opciones;
      $tabla.='</select>'; 
      $tabla.='</td>';      
      $tabla.='</tr>';
      $tabla.='<tr>';    
      $tabla.='<td>';  
      $tabla.='<label>Insumo:</label>';
      $tabla.='<input type="hidden" id="codart" name="codart" value="'.$ri->codart.'" />';
      $tabla.='<input type="hidden" id="codunimed" name="codunimed" value="'.$ri->codunimed.'" />';
      $tabla.='<input type="hidden" id="spg_cuenta" name="spg_cuenta" value="'.$ri->spg_cuenta.'" />';
      $tabla.='<input class="Campos'.$edit.'" value="'.$ri->denart.
              '" id="Insumo" title="Insumo Requerido" tabindex="3"  ';
      $tabla.=' onchange="CambioInsumo($(this).val());"/>';  
      $tabla.='</td>';
      $tabla.='</tr>';  
      $tabla.='<tr>';  
      $tabla.='<td>';
      $tabla.='<label style="display: inline">Cantidad Existencia:</label>';
      $tabla.='&nbsp;&nbsp;&nbsp;<input type="text" id="Existencia" size="4" class="Nro'.$edit.'" value="';
      $tabla.=number_format($ri->existencia,0,',','.').'" tabindex="4" title="Cantidad en Existencia"'.$readonly;
      //onlyDigits(e, value, allowDecimal, allowNegative, allowThousand, decSep, thousandSep, decLength)
      $tabla.=' onblur="this.value=formatNumber(this.value,0);"';
      $tabla.=' onkeyup="formatNumber(this.value,0);" ';
      $tabla.=' onkeypress="return onlyDigits(event, this.value, false,false,false,\',\',\'.\',0);" ';
      $tabla.=' />'; 
      $tabla.='&nbsp;&nbsp;&nbsp;<span class="um" title="Unidad de Medida">'.$ri->denunimed.'</span>';
      $tabla.='</td>';        
      $tabla.='</tr>';  
      $tabla.='<tr>';        
      $tabla.='<td>';  
      $tabla.='<label style="display: inline">Cantidad Requerida:</label>'; 
      $tabla.='&nbsp;&nbsp;&nbsp;<input type="text" id="Requerido" size="4" class="Nro'.$edit.'" value="';
      $tabla.=number_format($ri->requerido,0,',','.').'" tabindex="5" title="Cantidad Adicional Requerida"'.$readonly;
      //onlyDigits(e, value, allowDecimal, allowNegative, allowThousand, decSep, thousandSep, decLength)
      $tabla.=' onblur="this.value=formatNumber(this.value,0);"';
      $tabla.=' onkeyup="formatNumber(this.value,0);" ';
      $tabla.=' onkeypress="return onlyDigits(event, this.value, false,false,false,\',\',\'.\',0);" ';
      $tabla.=' />'; 
      $tabla.='&nbsp;&nbsp;&nbsp;<span class="um" title="Unidad de Medida">'.$ri->denunimed.'</span>';
      $tabla.='</td>';        
      $tabla.='</tr>';
      $tabla.='<tr>';
      $tabla.='<td>';
      $tabla.='<label>Observaciones:</label>';
      $tabla.='<textarea id="Observaciones" class="CampoFicha '.$edit.'"'.$readonly.'>'; 
      $tabla.=$ri->observaciones; 
      $tabla.='</textarea>'; 
      $tabla.='</td>';        
      $tabla.='</tr>';
      $tabla.='</tbody>';
      
      $tabla.='<tfoot>';
      $tabla.='<tr><td colspan="2">';
      if ($editable==1)
      {
        $tabla.='<div class="BotonIco" onclick="javascript:ActualizarFicha('.$ri->id_requerimiento_insumo.')" title="Guardar Cambios">';
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

  function cambiar_insumo()
  {
      if (!$this->input->is_ajax_request()) die('Acceso Denegado');
      
      $id_insumo = trim($this->input->post('id_insumo'));
      
      $um='<option value="0">ERROR AL CARGAR LA DATA</option>';
      $insumo=$this->Crud->listar_registros('ri_insumos',array('id_insumo'=>$id_insumo));
      if ($insumo->num_rows()>0)
      {
          $fila=$insumo->row();
          $um=$fila->unidad_medida;
      }
      
      die($um);
  }  

  function actualizar_ficha()
  {
      if (!$this->input->is_ajax_request()) die('Acceso Denegado');
      
      $donde=array(
                'id_requerimiento_insumo' => intval($this->input->post('id_requerimiento_insumo'))
                  );
      
      $datos=array(                                
                'codart'=>$this->input->post('codart'),
                'partida_generica'=>$this->input->post('partida_generica'),          
                'denart'=>$this->input->post('denart'),
                'codunimed'=>$this->input->post('codunimed'),
                'denunimed'=>$this->input->post('denunimed'),
                'spg_cuenta'=>$this->input->post('spg_cuenta'),
                'existencia'=>str_ireplace(",",".",str_ireplace(".","",$this->input->post('existencia'))),
                'requerido'=>str_ireplace(",",".",str_ireplace(".","",$this->input->post('requerido'))),
                'observaciones'=>$this->input->post('observaciones') 
                  );
      
      $actualizado=$this->Crud->actualizar_registro('ri_requerimiento_insumos', $datos, $donde);        
      if (!$actualizado){die('Error');}
      else 
      {           
         $registro='id_requerimiento_insumo: '.$donde['id_requerimiento_insumo'];         
         $registro.='. Actualizado por: '.$this->session->userdata('usuario');
         $bitacora=array(
             'direccion_ip'   =>$this->session->userdata('ip_address'),
             'navegador'      =>$this->session->userdata('user_agent'),
             'id_usuario'     =>$this->session->userdata('id_usuario'),
             'controlador'    =>$this->uri->uri_string(),
             'tabla_afectada' =>'rp_requerimiento_insumos',
             'tipo_accion'    =>'UPDATE',
             'registro'       =>$registro
         );
         $this->Crud->insertar_registro('z_bitacora', $bitacora);            
      };   
  }
  
  function requerir_insumo()
  {
      if (!$this->input->is_ajax_request()) die('Acceso Denegado');
      
      $yearpoa=intval($this->input->post('yearpoa'));
      $id_estructura=intval($this->input->post('id_estructura'));            
         
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
      $tabla.='Requerimiento de Insumo';  
      $tabla.='</th></tr>';           
      $tabla.='</thead>';
      $tabla.='<tbody>';      
      $tabla.='<tr>';    
      $tabla.='<td>';
      $opciones='<option value="0">ERROR AL CARGAR LA DATA</option>';
      $tipos=$this->Crud->listar_registros('ri_tipo_insumos');      
      if ($tipos->num_rows()>0)
      {
         $a=array('0'); $b=array('[Seleccione]');
         foreach ($tipos->result() as $fila)
         {
           array_push($a,$fila->partida_generica);
           array_push($b,$fila->tipo_insumo);
         }         
         $opciones=array_combine($a,$b);
         ksort($opciones);
         $opciones=$this->_construye_opciones($opciones);
      }      
      $tabla.='<label>Tipo de Insumo:</label>';
      $tabla.='<select class="Campos Editable" id="TipoInsumo" title="Tipo de Insumo" tabindex="1" ';
      $tabla.=' onchange="CambioTipo();">';
      $tabla.=$opciones;
      $tabla.='</select>'; 
      $tabla.='</td>';      
      $tabla.='</tr>';
      $tabla.='<tr>';    
      $tabla.='<td>';
      $tabla.='<label>Insumo:</label>';
      $tabla.='<input type="hidden" id="codart" name="codart" value="0" />';
      $tabla.='<input type="hidden" id="codunimed" name="codunimed" value="0" />';
      $tabla.='<input type="hidden" id="spg_cuenta" name="spg_cuenta" value="0" />';
      $tabla.='<input class="Campos Editable" id="Insumo" title="Insumo Requerido" tabindex="3" ';
      $tabla.=' disabled="disabled" value="-- Seleccione Primero el tipo de Insumo --" onchange="CambioInsumo($(this).val());"/>'; 
      $tabla.='</td>';
      $tabla.='</tr>';  
      $tabla.='<tr>';  
      $tabla.='<td>';
      $tabla.='<label style="display: inline">Cantidad Existencia:</label>';
      $tabla.='&nbsp;&nbsp;&nbsp;<input type="text" id="Existencia" size="4" class="Nro Editable" value="';
      $tabla.='0" tabindex="4" title="Cantidad en Existencia"';
      //onlyDigits(e, value, allowDecimal, allowNegative, allowThousand, decSep, thousandSep, decLength)
      $tabla.=' onblur="this.value=formatNumber(this.value,0);"';
      $tabla.=' onkeyup="formatNumber(this.value,0);" ';
      $tabla.=' onkeypress="return onlyDigits(event, this.value, false,false,false,\',\',\'.\',0);" ';
      $tabla.=' />'; 
      $tabla.='&nbsp;&nbsp;&nbsp;<span class="um" title="Unidad de Medida"></span>';
      $tabla.='</td>';        
      $tabla.='</tr>';  
      $tabla.='<tr>';        
      $tabla.='<td>';  
      $tabla.='<label style="display: inline">Cantidad Requerida:</label>'; 
      $tabla.='&nbsp;&nbsp;&nbsp;<input type="text" id="Requerido" size="4" class="Nro Editable" value="';
      $tabla.='0" tabindex="5" title="Cantidad Adicional Requerida"';
      //onlyDigits(e, value, allowDecimal, allowNegative, allowThousand, decSep, thousandSep, decLength)
      $tabla.=' onblur="this.value=formatNumber(this.value,0);"';
      $tabla.=' onkeyup="formatNumber(this.value,0);" ';
      $tabla.=' onkeypress="return onlyDigits(event, this.value, false,false,false,\',\',\'.\',0);" ';
      $tabla.=' />'; 
      $tabla.='&nbsp;&nbsp;&nbsp;<span class="um" title="Unidad de Medida"></span>';
      $tabla.='</td>';        
      $tabla.='</tr>';
      $tabla.='<tr>';
      $tabla.='<td>';
      $tabla.='<label>Observaciones:</label>';
      $tabla.='<textarea id="Observaciones" class="Editable CampoFicha">'; 
      $tabla.='</textarea>'; 
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
  
  // ARMA EL AUTOCOMPLETAR DE INSUMOS
  function listar_articulos()
  {
     if (!$this->input->is_ajax_request()) die('Acceso Denegado');
       
     $frase= mb_convert_case($this->input->post('frase'), MB_CASE_UPPER, 'UTF-8');
     $tipo_insumo= $this->input->post('tipo_insumo');  
     // CONVERTIMOS A UTF8
     $r=$this->Insumos->listar_articulos($frase, $tipo_insumo);
     
     foreach ($r as $a)
     {
         $a['denart'] = trim(mb_convert_case(utf8_encode($a['denart']),MB_CASE_UPPER,'UTF-8'));
         $a['denunimed'] = trim(mb_convert_case(utf8_encode($a['denunimed']),MB_CASE_UPPER,'UTF-8'));
         $a['codart'] = trim(mb_convert_case(utf8_encode($a['codart']),MB_CASE_UPPER,'UTF-8'));
     } 
     die(json_encode($r));
  }
    
  function guardar_ficha()
  {   
      if (!$this->input->is_ajax_request()) die('Acceso Denegado');

      $datos=array(            
                'id_estructura'=>intval($this->input->post('id_estructura')),
                'yearpoa'=>intval($this->input->post('yearpoa')),  
                'partida_generica'=>$this->input->post('partida_generica'),
                'codart'=>$this->input->post('codart'),
                'denart'=>$this->input->post('denart'),
                'codunimed'=>$this->input->post('codunimed'),
                'denunimed'=>$this->input->post('denunimed'),
                'spg_cuenta'=>$this->input->post('spg_cuenta'),
                'existencia'=>str_ireplace(",",".",str_ireplace(".","",$this->input->post('existencia'))),
                'requerido'=>str_ireplace(",",".",str_ireplace(".","",$this->input->post('requerido'))),
                'observaciones'=>$this->input->post('observaciones') 
                  );    
        
      $insertado=$this->Crud->insertar_registro('ri_requerimiento_insumos', $datos);
      if (!$insertado){die('Error');}
      else
      {
        $registro='id_requerimiento_insumo: '.$this->db->insert_id();          
        $registro.='. Registrado por: '.$this->session->userdata('usuario');
        $bitacora=array(
            'direccion_ip'   =>$this->session->userdata('ip_address'),
            'navegador'      =>$this->session->userdata('user_agent'),
            'id_usuario'     =>$this->session->userdata('id_usuario'),
            'controlador'    =>$this->uri->uri_string(),
            'tabla_afectada' =>'rp_requerimiento_insumos',
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
        'id_requerimiento_insumo' => intval($this->input->post('id_requerimiento_insumo'))
                   );        
    $borrado=$this->Crud->eliminar_registro('ri_requerimiento_insumos', $donde);
    if (!$borrado){die('Error');}
    else
    {
       $registro='id_requerimiento_insumo: '.$donde['id_requerimiento_insumo'];           
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