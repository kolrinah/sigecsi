<?php if (!defined('BASEPATH')) exit('Sin Acceso Directo al Script');      
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *  SISTEMA DE GESTION Y CONTROL DEL SERVICIO INTERNO                *
 *  DESARROLLADO POR: ING.REIZA GARCÍA                               *
 *                    ING.HÉCTOR MARTÍNEZ                            *
 *  PARA:  MINISTERIO DEL PODER POPULAR PARA RELACIONES EXTERIORES   *
 *  FECHA: JULIO DE 2013                                             *
 *  FRAMEWORK PHP UTILIZADO: CodeIgniter Version 2.1.3               *
 *                           http://ellislab.com/codeigniter         *
 *  TELEFONOS PARA SOPORTE: 0416-9052533 / 0212-5153033              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
class Enviar_poa extends CI_Controller {
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
    // * EL USUARIO DEBE TENER NIVEL SUPERIOR A COORDINADOR id_nivel=<4
    if (!($this->session->userdata('administrador') || $this->session->userdata('id_nivel')<5)) exit('Sin Acceso al Script');

    // RECUPERA LA FECHA Y HORA DEL SISTEMA
    date_default_timezone_set('America/Caracas'); // Establece la Hora de Venezuela para funciones de fecha y hora              
    $ahora=  getdate(time());
    $yearmax=$ahora['year']+1; // El año máximo del POA es el año siguiente al año en curso
    $yearpoa=$this->session->userdata('yearpoa');
    
    $data=array();
    $data['titulo']='Resúmen de la Planificación Operativa';
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
                    'value' => $this->session->userdata('id_estructura'));  
      
    $data['contenido']='enviar_poa/enviar_poa';    
    $data['script']='<!-- Cargamos CSS de DataTables -->'."\n";    
    $data['script'].="\t".'<link rel="stylesheet" type="text/css" media="all" href="'.base_url().'css/dataTables.css"/>'."\n";
    $data['script'].='<!-- Cargamos JS para DataTables -->'."\n";
    $data['script'].="\t".'<script type="text/javascript" src="'.base_url().'js/jquery.dataTables.js"></script>'."\n";
    $data['script'].='<!-- Cargamos Nuestro JS -->'."\n";
    $data['script'].="\t".'<script type="text/javascript" src="'.base_url().'js/enviar_poa.js"></script>'."\n";
       
 
    $data['tabla']=$this->planificacion_unidades($yearpoa, $this->session->userdata('id_estructura')); 
    
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
    $tiempo=  getdate(time()); // Fecha del día de hoy
    $yearenvio= $tiempo['year']+1;
      
    $unidad=$this->Estructura->obtener_estructura($id_estructura);
    if (!$unidad)die('error consultando estructura');
    
    // ENCABEZADOS DE TABLA
    $tabla='<table width="100%"><tr>';
    
    $u=($unidad['id_sup']<=($this->session->userdata('id_estructura')))?
                            $this->session->userdata('id_estructura'):
                            $unidad['id_sup'];
    
    $accion=' onclick="javascript:cambiaUnidad('.$u.');" ';
    $tabla.='<td width="30px" class="BotonIco">'; 
    
    if ($unidad['id_sup']>=($this->session->userdata('id_estructura')) || $verUnidad==1)
    {           
       $tabla.='<img src="'.base_url().'imagenes/back.png"'.$accion;
       $tabla.='title="clic para regresar a Unidad Superior"';
       $tabla.='/>';
    }
    
    // BOTON PARA RECHAZAR EL ENVIO DE POA DE LA UNIDAD INFERIOR
    $enviado='';
    $poaenviado = $this->Estructura->poaEnviado($id_estructura, $yearpoa);
    if ($poaenviado->num_rows()==0) $yaEnviado='f';
    else 
    {
       $y=$poaenviado->row();
       $yaEnviado=$y->enviado;
       if ($unidad['id_sup']==$this->session->userdata('id_estructura')&&
           ($this->session->userdata('id_nivel')==3 || $this->session->userdata('administrador')))
       {
          $enviado = ($yaEnviado=='t')?
                  '<img src="imagenes/enviarpoa22.png" class="btnRechazo" title="Clic para Rechazar" 
                        onclick="rechazarPOA('.$id_estructura.')" />':
                  '<img src="imagenes/poarechazado.png" class="btnRechazo" title="Envío Rechazado" />';
       }
    }    
    
    $tabla.='</td>';
    $tabla.='<td style="width:100px; text-align:right">';
    $tabla.='<h4>'.trim($unidad['codigo']).'&nbsp;-&nbsp; </h4>';
    $tabla.='</td>';
    $tabla.='<td style="text-align:left">';        
    $tabla.='<h4>'.trim($unidad['descripcion']).$enviado.'</h4>';
    $tabla.='</td>';         
    
    $tabla.='<td width="10%" style="text-align:right; padding:0 10px 5px 0">';
    $tope=strtotime($unidad['fecha_tope']);
    $ahora=time();
    $img=($ahora>$tope)?'cerrado.png':'abierto.png';
        
    $tabla.='<img src="'.base_url().'imagenes/'.$img.'" ';
    $tabla.=' title="Fecha Tope: '.date("d/m/Y",strtotime($unidad['fecha_tope'])).'"';
    $tabla.='/>';
    $tabla.='</td>';
    $tabla.='</tr></table>';
  
    // BUSCAMOS SI LA ESTRUCTURA NO POSEE UNIDADES INFERIORES
    $consulta=$this->Estructura->unidades_inferiores($id_estructura);
    if (!$consulta || $verUnidad==1)
    {
        $tabla.= $this->_planificacion_unidad($id_estructura, $verUnidad);
        
        // PREPARAMOS BOTÓN DE ENVIO DE POA
        $botonEnviar='';
        if (($ahora<=$tope) && ($this->session->userdata('id_estructura')==$id_estructura)
                            && $verUnidad==0
                            && $yaEnviado!='t'
                            && $yearenvio==$yearpoa)
        {
          $botonEnviar='<center><div class="BotonIco" onclick="javascript:enviarPOA('.
                                              $id_estructura.')" title="Enviar POA">';
          $botonEnviar.='<img src="imagenes/enviarpoa32.png" />&nbsp;&nbsp;&nbsp;';   
          $botonEnviar.='Enviar POA';
          $botonEnviar.= '</div></center>';
        }    
        $tabla.=$botonEnviar;        
        
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
    $tabla.=$unidad['codigo'];
    $tabla.='</td>';
    $tabla.='<td style="text-align:left"'.$titulo.$accion.'>';
    $tabla.=$unidad['descripcion'];
    $tabla.='</td>';
    $tabla.='<td'.$accion.'>';
      // PREPARAMOS LA CONSULTA 
      $donde="where id_estructura='".$unidad['id_estructura']."'";
      $plan=$this->Productos->estado_planificacion($donde,$yearpoa);
      if (!$plan)die('error en unidad');
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
    
    $tabla.='<img src="'.base_url().'imagenes/'.$img.'" ';
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
      
      $tabla.='<img src="'.base_url().'imagenes/'.$img.'" ';
      $tabla.=' title="Fecha Tope: '.date("d/m/Y",$tope).'"';
      $tabla.='/>';      
      $tabla.='</td>';
      $tabla.='</tr>';
    }

    //PIE DE TABLA
    $tabla.='</tbody></table><br/><br/>';  

    // PREPARAMOS BOTÓN DE ENVIO DE POA
    $poaenviado = $this->Estructura->poaEnviado($id_estructura, $yearpoa);
    if ( $poaenviado->num_rows()==0) $yaEnviado='f';
    else
    {
        $y=$poaenviado->row();
        $yaEnviado=$y->enviado;
    }    
  
    $botonEnviar='';
    if (($ahora<=$tope) && $yearenvio==$yearpoa && ($yaEnviado!='t'))
    {
      $botonEnviar='<center><div class="BotonIco" onclick="javascript:enviarPOA('.$id_estructura.')" title="Enviar POA">';
      $botonEnviar.='<img src="imagenes/enviarpoa32.png" />&nbsp;&nbsp;&nbsp;';   
      $botonEnviar.='Enviar POA';
      $botonEnviar.= '</div></center>';
    }    
    $tabla.=$botonEnviar;
    
    if ($this->input->is_ajax_request()) die($tabla); // Si la peticion vino por AJAX
    else return $tabla;
  }
  
  function rechazarEnvio()
  {
     if (!$this->input->is_ajax_request()) die('Acceso denegado'); // Si la peticion no vino por AJAX
     $yearpoa=$this->input->post('yearpoa'); 
     $id_estructura=$this->input->post('id_estructura');  
   
     date_default_timezone_set('America/Caracas'); // Establece la Hora de Venezuela para funciones de fecha y hora
     $tiempo=  getdate(time()); // Fecha del día de hoy
     $yearpoa= $tiempo['year']+1;
     $fecha=$tiempo['year'].'-'.$tiempo['mon'].'-'.$tiempo['mday'];      
     
     $donde=array( 'id_estructura' => $id_estructura,
                         'yearpoa' => $yearpoa );
     
     $datos=array( 'enviado' => 'f',
                       'fecha_envio' => $fecha );
     
     if (!$this->Crud->actualizar_registro('e_envio_poa', $datos, $donde)) die('Error');
     
     $this->_enviar_email_rechazo($id_estructura);
     
      // REGISTRA EN BITACORA  
      $registro='Rechazo del POA de la Unidad id_estructura: '.$id_estructura;      
      $registro.='. Registrado por: '.$this->session->userdata('usuario');
      $bitacora=array(
               'direccion_ip'   =>$this->session->userdata('ip_address'),
               'navegador'      =>$this->session->userdata('user_agent'),
               'id_usuario'     =>$this->session->userdata('id_usuario'),
               'controlador'    =>$this->uri->uri_string(),
               'tabla_afectada' =>'e_envio_poa',
               'tipo_accion'    =>'UPDATE',
               'registro'       =>$registro
           );
      $this->Crud->insertar_registro('z_bitacora', $bitacora);      
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
        $r = $this->comunes->analisis_proyecto($p->monto_aprobado, $p->total, $p->estatus);           
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
      
      $tabla.='</div><br/><br/>';
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
       $sp=$this->Productos->obtener_subproducto($id_subprod);
       if (!$sp) die('Error');
       
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
       $data.=$sp['ecodigo'].' - '.$sp['estructura'];
       $data.='</td>';
       $data.='</tr>';
       $data.='<tr><td style="text-align:right">';
       $data.='<label>Producto Administrativo:</label>';
       $data.='</td>';
       $data.='<td>';
       $data.=$sp['pcodigo'].'. '.$sp['pnombre'];
       $data.='</td>';
       $data.='</tr>';
       $data.='<tr>';
       $data.='<td style="text-align:right">';
       $data.='<label>Definición del Producto:</label>';
       $data.='</td>';
       $data.='<td>';
       $data.=$sp['pdefinicion'];         
       $data.='</td></tr>';
       $data.='<tr>';
       $data.='<td style="text-align:right">';
       $data.='<label>Sub-Producto Administrativo:</label>';
       $data.='</td>';
       $data.='<td>';
       $data.=$sp['pcodigo'].'.'.$sp['scodigo'].' '.$sp['nombre'];
       $data.='</td></tr>';
       $data.='<tr>';
       $data.='<td style="text-align:right">';
       $data.='<label>Definición del Sub-Producto:</label>';
       $data.='</td>';
       $data.='<td>';
       $data.=$sp['definicion'];         
       $data.='</td></tr>';
       $data.='<tr>';
       $data.='<td style="text-align:right">';
       $data.='<label>Unidad de Medida:</label>';
       $data.='</td>';
       $data.='<td>';
       $data.=$sp['unidad_medida'];
       $data.='</td></tr>';
       $data.='<tr>';
       $data.='<td style="text-align:right">';
       $data.='<label>Clasificación del Sub-Producto:</label>';
       $data.='</td>';
       $data.='<td>';
       // DETERMINADO/INDETERMINADO
            if ($sp['es_determinado']=='t')
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
            if ($sp['es_extraordinario']=='t')
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
            if ($sp['es_tramite']=='t')
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
             
       die($data);
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
      $accion=' onclick="javascript:actualiza('.
               ($proy->id_estructura==$this->session->userdata('id_estructura')?1:0).
                          ');" ';      
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
      $tabla.='<label>Nombre del Proyecto (Objetivo Específico)</label>';      
      $tabla.=trim($proy->obj_esp);
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>';
      $tabla.='<td colspan="2">';      
      $tabla.='<label>Presupuesto Aprobado:  Bs.</label>';      
      $tabla.=number_format($proy->monto_aprobado,2,',','.');      
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr style="border-bottom:#576F89 solid 1px">';
      $tabla.='<td colspan="2">';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>'; 
      $tabla.='<td colspan="3">';
      $tabla.='<label>Objetivo General</label>';      
      $tabla.=trim($proy->obj_gen);
      $tabla.='</td>';
      $tabla.='</tr>';      
      $tabla.='<tr>';      
      $tabla.='<td colspan="2">';
      $tabla.='<label>Descripción Breve del Proyecto</label>';
      $tabla.=trim($proy->descripcion_breve);
      $tabla.='</td>';
      $tabla.='</tr>'; 
      $tabla.='<tr style="border-bottom:#576F89 solid 1px">';
      $tabla.='<td colspan="2">';
      $tabla.='</td>';
      $tabla.='</tr>';      
      $tabla.='<tr>'; 
      $tabla.='<td colspan="2">';
      $tabla.='<label>Enunciado del Problema o Necesidad que Origina el Proyecto</label>';   
      $tabla.=trim($proy->problema);
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>'; 
      $tabla.='<td colspan="2">';
      $tabla.='<label>Indicador de la situación actual del problema</label>';
      $tabla.=trim($proy->indicador_problema);      
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr style="border-bottom:#576F89 solid 1px">';
      $tabla.='<td colspan="2">';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>'; 
      $tabla.='<td colspan="2">';
      $tabla.='<label>Indicador de la situación Objetivo del Proyecto</label>';      
      $tabla.=trim($proy->indicador_obj_proy);      
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>'; 
      $tabla.='<td colspan="2">';
      $tabla.='<label>Resultado del Proyecto</label>';
      $tabla.=trim($proy->resultado);
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr style="border-bottom:#576F89 solid 1px">';
      $tabla.='<td colspan="2">';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr>'; 
      $tabla.='<td width="50%">';
      $tabla.='<label>Responsable del Proyecto</label>';      
      $tabla.=trim($proy->nombre).' '.trim($proy->apellido).' ('.trim($proy->correo).')';
      $tabla.='</td>';      
      $tabla.='<td style="padding-right:35px">';
      $tabla.='<label>Teléfonos de Contacto</label>';      
      $tabla.=trim($proy->telefonos);
      $tabla.='</td>';
      $tabla.='</tr>';            
      $tabla.='</tbody>';      
      $tabla.='<tfoot>';
      $tabla.='<tr><td colspan="3">';
      $tabla.='<div class="BotonIco" onclick="javascript:actualiza('.
               ($proy->id_estructura==$this->session->userdata('id_estructura')?1:0).
                          ');"  title="Salir">';
      $tabla.='<img src="imagenes/cancel.png"/>&nbsp;';
      $tabla.='Cancelar';
      $tabla.= '</div>';
      $tabla.='</td></tr>';
      $tabla.='</tfoot>';
      $tabla.='</table>';
      $tabla.='</div><br/><br/>';
      die ($tabla);      
  }
  
  function plan_proyecto()
  {
    if (!$this->input->is_ajax_request())die('Sin Acceso al Script');// Si la peticion No es AJAX
    
    $id_proyecto=$this->input->post('id_proyecto');
     
    date_default_timezone_set('America/Caracas');      
    $plan=$this->Proyectos->obtener_proyecto($id_proyecto);
    if (!$plan) die('ERROR');
    
    // DATOS DEL PROYECTO
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
    $tabla.='<strong>PROYECTO: '.trim($plan->cod_proy).'&nbsp;-&nbsp;</strong>';    
    $tabla.=trim($plan->obj_esp);
    $tabla.='</td>';    
    $tabla.='</tr>';
    $tabla.='</table>';
    $tabla.='<input type="hidden" id="idProyecto" value="'.$plan->id_proyecto.'" />';    
    
    $tabla.='<table>';
    $tabla.='<tr>';    
    $tabla.='<td>';
    $tabla.='<h4>PLANIFICACIÓN DE ACTIVIDADES</h4>';
    $tabla.='</td>';
    
    $g=$this->session->userdata('gantt')?' checked="checked" ':'';
    $o=' onchange="actualizaGanttProyecto($(this).attr(\'checked\'));" ';
    $tabla.='<td style="text-align:center;">';
    $tabla.='<input type="checkbox" id="botonGantt" '.$g.$o.' />';
    $tabla.='<label for="botonGantt" title="clic para cambiar">Gantt</label>';    
    $tabla.='</td>';    
    $tabla.='</tr>';
    $tabla.='</table>';
    $tabla.= ($this->session->userdata('gantt'))?$this->_gantt_proyecto($id_proyecto):
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
  
  function _gantt_proyecto($id_proyecto)
  {   
    date_default_timezone_set('America/Caracas');
    
    $plan=$this->Proyectos->gantt_proyecto($id_proyecto);
        
    if (!$plan)
    {
       $tabla='<center><h2>- Proyecto sin Actividades Programadas -</h2></center>';
       return $tabla;
    }    
        
    // PLANIFICACION DE ACTIVIDADES
    $tabla='<table class="TablaNivel1 Zebrado">';
    $tabla.=$this->_cabecetabla_proy();
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
      
      $tabla.='<td>';      
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
                     onclick="revisarActividad('.$plan[$i]->id_actividad.');">';           
           $tabla.='<div class="'.(($plan[$i]->total>0)?'gantt'.$plan[$i]->cod_fuente:'ganttPobre').'">'.$gantt.'</div>';           
           $tabla.='</td>';
           
           $tabla.='<td style="vertical-align:middle">';
           $accion=' onclick="listarPresupuesto('.$plan[$i]->id_actividad.');" ';
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
    $tabla.='</table><br/><br/>'; 
    
    return $tabla;
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
    $accion=' onclick="javascript:actualiza('.
            ($proyecto->id_estructura==$this->session->userdata('id_estructura')?1:0).
                          ');" ';
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
      $tabla.='</div><br/><br/>';
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
      $tabla.='</div><br/><br/>';
      die($tabla);      
  }
  
  function enviarPOA()
  {
      if (!$this->input->is_ajax_request()) die('Acceso Denegado');

      $id_estructura= intval($this->input->post('id_estructura'));
      
      date_default_timezone_set('America/Caracas'); // Establece la Hora de Venezuela para funciones de fecha y hora
      $tiempo=  getdate(time()); // Fecha del día de hoy
      $yearpoa= $tiempo['year']+1;
      
      // PREPARAMOS LA CONSULTA 
      $donde="where id_estructura='".$id_estructura."'";
      $plan=$this->Productos->estado_planificacion($donde,$yearpoa);
      if (!$plan)die('error en unidad');
      
      $aprobado=TRUE; // Bandera para mostrar botón Confirmar
      $productos=(($plan['sp_det']==0)?'-':($plan['sp_plan'] / $plan['sp_det']*100));
      $aprobado=($aprobado && (($plan['sp_det']==0) || ($productos==100)) );
      
      $tabla='<div class="EntraDatos ajustePad">';
      $tabla.='<table style="margin-top:0px!important">';
      $tabla.='<thead>';
      $tabla.='<tr><th colspan="5" >';
      $tabla.='Revisión de la Planificación'; 
      $tabla.='</th></tr>';           
      $tabla.='</thead>';
      $tabla.='<tbody>'; 
      $tabla.='<tr class="Resaltado">'; 
      $tabla.='<td>';
      $tabla.='<img src="imagenes/programacion20.png"/>';
      $tabla.='</td>';
      $tabla.='<td style="width:300px">';
      $tabla.='Planificación de Productos Administrativos';
      $tabla.='</td>';
      $tabla.='<td style="width:130px; color:'.(($productos<100)?'red':'black').'">';
      $tabla.=(($plan['sp_det']==0)?'No Posee':intval($productos).'%');
      $tabla.='</td>';
      $tabla.='<td style="width:50px">';
      $tabla.=($productos==100 || $plan['sp_det']==0 )?
                    '<img src="imagenes/activo16.png" title="Aprobado"/>':
                    '<img src="imagenes/cancel16.png" title="Reprobado"/>';
      $tabla.='</td>'; 
      $tabla.='<td>';
      $tabla.='</td>';
      $tabla.='</tr>'; 
      $tabla.='<tr class="Resaltado">'; 
      $tabla.='<td>';
      $tabla.='<img src="imagenes/gantt20.png" />';   
      $tabla.='</td>'; 
      $tabla.='<td>'; 
      $tabla.='Planificación de Proyectos';      
      $tabla.='</td>'; 
      $tabla.='<td>'; 
      $tabla.=intval($plan['proyectos']).' proyectos'; 
      $tabla.='</td>'; 
      $tabla.='<td>';
      $estatus=$this->_revisar_proyectos($id_estructura, $yearpoa);
      $tabla.=($estatus)? '<img src="imagenes/activo16.png" title="Aprobado"/>':
                          '<img src="imagenes/cancel16.png" title="Programación incompleta"/>'; 
      
      $aprobado=($aprobado && $estatus);

      $tabla.='</td>';
      $tabla.='<td>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr class="Resaltado">'; 
      $tabla.='<td>';
      $tabla.='<img src="imagenes/user16.png"/>';
      $tabla.='</td>'; 
      $tabla.='<td>'; 
      $tabla.='Requerimiento de Personal';      
      $tabla.='</td>'; 
      $tabla.='<td>'; 
      $tabla.=intval($plan['personal']).' personas'; 
      $tabla.='</td>'; 
      $tabla.='<td>';
      $tabla.='<img src="imagenes/activo16.png" title="Aprobado"/>';
      $tabla.='</td>'; 
      $tabla.='<td>';
      $tabla.='</td>';
      $tabla.='</tr>';
      $tabla.='<tr class="Resaltado" style="border-bottom:#576F89 solid 1px">'; 
      $tabla.='<td>';
      $tabla.='<img src="imagenes/insumos16.png" />';
      $tabla.='</td>'; 
      $tabla.='<td>'; 
      $tabla.='Requerimiento de Insumos';      
      $tabla.='</td>'; 
      $tabla.='<td>'; 
      $tabla.=intval($plan['insumos']).' solicitudes';
      $tabla.='</td>'; 
      $tabla.='<td>';
      $tabla.='<img src="imagenes/activo16.png" title="Aprobado"/>';
      $tabla.='</td>'; 
      $tabla.='<td>';
      $tabla.='</td>';
      $tabla.='</tr>';      
      // PLANIFICACION DE UNIDADES INFERIORES
      $inferiores=$this->Estructura->unidades_inferiores($id_estructura);
      if ($inferiores)
      {
         $tabla.='<tr>'; 
         $tabla.='<td colspan="5" style="text-align:left; font-weight:bold; font-size:1.3em" >';
         $tabla.='Planificación de Unidades Subordinadas';
         $tabla.='</td>';
         $tabla.='</tr>';
         foreach ($inferiores as $unidad)  
         {
           $tabla.='<tr class="Resaltado">';
           $tabla.='<td>';
           $tabla.=$unidad['codigo'];
           $tabla.='</td>';
           $tabla.='<td colspan="2">';
           $tabla.=$unidad['descripcion'];
           $tabla.='</td>';
           $tabla.='<td>';
           
           $donde="where id_estructura='".$unidad['id_estructura']."'";
           $plan=$this->Productos->estado_planificacion($donde,$yearpoa);
           if (!$plan)die('error en unidad');
           
           $tabla.=($plan['enviado']=='t')?
                    '<img src="imagenes/activo16.png" title="POA enviado"/>':
                    '<img src="imagenes/cancel16.png" title="POA no enviado"/>'; 
      
           $aprobado=($aprobado && ($plan['enviado']=='t'));
           
           $tabla.='</td>';
           $tabla.='<td>';           
           $tabla.='</td>';
           $tabla.='</tr>';       
         }
      }            
      $tabla.='</tbody>';      
      $tabla.='<tfoot>';
      $tabla.='<tr><td colspan="5">';
      // BOTON COMFIRMAR ENVÍO
      if ($aprobado)
      {
        $tabla.='<div class="BotonIco" onclick="javascript:confirmarEnvio('
                 .$id_estructura.')" title="Confirmar Envío">';
        $tabla.='<img src="imagenes/enviarpoa32.png"/>&nbsp;';   
        $tabla.='Confirmar';
        $tabla.= '</div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';   
      }      
      $tabla.='<div class="BotonIco" onclick="javascript:actualiza()" title="Cancelar">';
      $tabla.='<img src="imagenes/cancel.png"/>&nbsp;';
      $tabla.='Cancelar';
      $tabla.= '</div>';
      $tabla.='</td></tr>';
      $tabla.='</tfoot>';
      $tabla.='</table>';
      $tabla.='</div><br/><br/>';
      die ($tabla);              
  }
  
  function confirmarEnvio()
  {
      if (!$this->input->is_ajax_request()) die('Acceso Denegado');

      $id_estructura= intval($this->input->post('id_estructura'));
      
      date_default_timezone_set('America/Caracas'); // Establece la Hora de Venezuela para funciones de fecha y hora
      $tiempo=  getdate(time()); // Fecha del día de hoy
      $yearpoa= $tiempo['year']+1;
      $fecha=$tiempo['year'].'-'.$tiempo['mon'].'-'.$tiempo['mday'];
      
      $donde=array( 'id_estructura' => $id_estructura,
                          'yearpoa' => $yearpoa );
      $existe=$this->Crud->listar_registros('e_envio_poa', $donde);
      
      if ($existe->num_rows()==0) // SI NO EXISTE EL REGISTRO DE ENVIO DE POA LO INSERTAMOS
      {    
         // REGISTRAMOS EL ENVIO
         $datos=array( 'id_estructura' => $id_estructura,
                           'yearpoa' => $yearpoa,
                           'enviado' => 't',                            
                       'fecha_envio' => $fecha
         );
         if (!$this->Crud->insertar_registro('e_envio_poa', $datos)) die('Error');
      }      
      else // SI YA EXISTE REGISTRO LO ACTUALIZAMOS
      {
         $datos=array( 'enviado' => 't',                            
                       'fecha_envio' => $fecha );
         if (!$this->Crud->actualizar_registro('e_envio_poa', $datos, $donde)) die('Error');
      }
      
      // ENVIAMOS EL EMAIL AL SUPERIOR
      $this->_enviar_email_poa();      
      
      // CIERRE DEL MODO DE EDICIÓN (DE DIRECCION DE LINEA EN ADELANTE id_nivel<4)
      if ($this->session->userdata('id_nivel')<4)
      {
        $donde=array('id_estructura'=> $id_estructura);
        unset($datos);
        $datos =array('fecha_tope' => $fecha);
        if (!$this->Crud->actualizar_registro('e_estructura', $datos, $donde)) die('Error');          
      }
      
      // REGISTRA EN BITACORA  
      $registro='Envío de POA de la Unidad id_estructura: '.$id_estructura;      
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
  
  function _enviar_email_poa()
  {      
      date_default_timezone_set('America/Caracas'); // Establece la Hora de Venezuela para funciones de fecha y hora
      $hora=getdate();
      if (($hora['hours']) <12 && $hora['hours']>=5){$saludo='Buenos días,';}
      elseif ($hora['hours']<19 && $hora['hours']>=12){$saludo='Buenas tardes,';}
      elseif ($hora['hours']>=19) {$saludo= 'Buenas noches';}
      elseif ($hora['hours']>=0 && $hora['hours']<5) {$saludo='Buenas noches,';}
	      
      $this->load->library('email'); // CARGAMOS LA LIBRERIA DE EMAIL
      
      // IDENTIFICAMOS AL USUARIO SUPERIOR
      $jefe=$this->Usuarios->obtener_jefe_inmediato($this->session->userdata('id_estructura'));      
      $para=array();
      // DIRIGE EL EMAIL A PLANIFICACIÓN A PARTIR DE DIRECTOR DE LÍNEA id_nivel<4
      // O SI NO ENCUENTRA JEFE
      if ($jefe->num_rows()==0 || ($this->session->userdata('id_nivel')<4) )$para[]='sigecsi@mppre.gob.ve';
      
      // SI ENCUENTRA JEFE
      if ($jefe->num_rows()>0)
      {
          foreach ($jefe->result() as $j)
          {
              $para[]=$j->correo;
          }
      }
      
      $correo=trim($this->session->userdata('correo'));
      $nombre=trim($this->session->userdata('usuario'));
      $asunto='PLANIFICACIÓN OPERATIVA - '.trim($this->session->userdata('nombre_estruct'));
      $mensaje="<html><body>";
      $mensaje.="$saludo,<br/>";
      $mensaje.="La unidad administrativa:<br/>";
      $mensaje.="<h3>".trim($this->session->userdata('nombre_estruct'))."</h3>";
      $mensaje.="ha cargado y enviado su planificación operativa del año ".($hora['year']+1);
      $mensaje.=".<br/>Muchas gracias por su amable atención,<br/>";
      $mensaje.="===============================================================<br/>";
      $mensaje.="Mensaje enviado por el Sistema de Gestión y Control del Servicio Interno.";
      $mensaje.="</body></html>";
      
      $this->email->to($para);
      $this->email->reply_to($correo, $nombre);
      
      $this->email->from('sigecsi@mppre.gob.ve', '*** SIGECSI ***');
      $this->email->cc('sigecsi@mppre.gob.ve');
      
      $this->email->subject($asunto);
      $this->email->message($mensaje);	
      $this->email->send();
  }
  
  function _enviar_email_rechazo($id_estructura)
  {      
      date_default_timezone_set('America/Caracas'); // Establece la Hora de Venezuela para funciones de fecha y hora
      $hora=getdate();
      if (($hora['hours']) <12 && $hora['hours']>=5){$saludo='Buenos días,';}
      elseif ($hora['hours']<19 && $hora['hours']>=12){$saludo='Buenas tardes,';}
      elseif ($hora['hours']>=19) {$saludo= 'Buenas noches';}
      elseif ($hora['hours']>=0 && $hora['hours']<5) {$saludo='Buenas noches,';}
	      
      $this->load->library('email'); // CARGAMOS LA LIBRERIA DE EMAIL
      
      // IDENTIFICAMOS AL COORDINADOR DE LA UNIDAD RECHAZADA
      $jefe=$this->Usuarios->obtener_jefe_unidad($id_estructura);
      $para=array();
      
      if ($jefe->num_rows()==0)$para[]='sigecsi@mppre.gob.ve';
      
      // SI ENCUENTRA JEFE
      if ($jefe->num_rows()>0)
      {
          foreach ($jefe->result() as $j)
          {
              $para[]=$j->correo;
          }
      }
      
      $correo=trim($this->session->userdata('correo'));
      $nombre=trim($this->session->userdata('usuario'));
      $asunto='PLANIFICACIÓN OPERATIVA RECHAZADA';
      $mensaje="<html><body>";
      $mensaje.="$saludo,<br/>";
      $mensaje.="La Planificación operativa de su unidad, enviada recientemente ";
      $mensaje.="ha sido rechazada por su superior.<br/>";      
      $mensaje.="Muchas gracias por su amable atención,<br/>";
      $mensaje.="===============================================================<br/>";
      $mensaje.="Mensaje enviado por el Sistema de Gestión y Control del Servicio Interno.";
      $mensaje.="</body></html>";
      
      $this->email->to($para);
      $this->email->reply_to($correo, $nombre);
      
      $this->email->from('sigecsi@mppre.gob.ve', '*** SIGECSI ***');
      $this->email->cc('sigecsi@mppre.gob.ve');
      
      $this->email->subject($asunto);
      $this->email->message($mensaje);	
      $this->email->send();
  }
  
  // REVISA PLANIFICACION DE PROYECTOS EN LA ESTRUCTURA DADA
  function _revisar_proyectos($id_estructura, $yearpoa)
  {
      $estructura= " where id_estructura=$id_estructura ";
      $aprobado=true;
      $proyectos=$this->Proyectos->listar_proyectos($estructura,$yearpoa);  
      if ($proyectos->num_rows()==0) return $aprobado;
      
      foreach ($proyectos->result() as $p)
      {
          $r = $this->comunes->analisis_proyecto($p->monto_aprobado, $p->total, $p->estatus);
          $aprobado= ($aprobado and $r['ok']);
      }      
      
      return $aprobado;
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