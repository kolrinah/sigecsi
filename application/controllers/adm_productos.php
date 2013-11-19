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
class Adm_productos extends CI_Controller {
    function __construct() 
    {
       parent::__construct();
       $this->load->helper('form');
       $this->load->library('form_validation');
       $this->load->model('Estructura');
       $this->load->model('Productos');
       $this->load->model('Crud');
    }
    
    function index()
    {
      // VERIFICAMOS SI EXISTE SESION ABIERTA    
       if (!$this->session->userdata('aprobado')) {redirect ('acceso', 'refresh'); exit();}
      
      // VERIFICACIÓN DE PERMISOS NECESARIOS PARA ACCESAR EL CONTROLADOR:
      // * DEBE PERTENECER AL AREA DE ORGANIZACIÓN Y SISTEMAS DE LA DIRECCIÓN DE PLANIFICACIÓN 
      //   (id_estructura= 50)
      // * O DEBE TENER ROL DE ADMINISTRADOR
       
       if (!($this->session->userdata('administrador') || $this->session->userdata('id_estructura')=='50'))exit('Sin Acceso al Script');
      
      
       $data=array();
       $data['titulo']='Administración de Productos';
       $data['contenido']='adm_productos/adm_productos';
       $data['formulario']= array(               
                 'codigo'=> array(
                            'name'      => 'codigo',
                            'id'        => 'codigo',
                            'tabindex'  => '1',
                            'title'     => 'Código de la Unidad',
                            'value'     => '',
                            'style'    =>  'text-align:center',
                            'maxlength' => '6',
                            'size'      => '6'    
                            ),
                 'unidad'=> array(
                            'name'      => 'unidad',
                            'id'        => 'unidad',
                            'tabindex'  => '2',
                            'title'     => 'Nombre de la Unidad',
                            'value'     => '',
                            'maxlength' => '110'
                            ),                                                   
                
               // CAMPOS OCULTOS
                 
                'id_unidad'=> array(
                            'name'      => 'id_unidad',
                            'id'        => 'id_unidad',
                            'type'      => 'hidden',
                            'value'     => '0'
                            ),   
                
                'base_url'=> array(
                            'name'      => 'base_url',
                            'id'        => 'base_url',
                            'type'      => 'hidden',
                            'value'     => base_url()
                            ),   
           );    
           
           $data['script']='<!-- Cargamos Nuestro JS -->'."\n";
           $data['script'].="\t".'<script type="text/javascript" src="'.base_url().'js/adm_productos.js"></script>'."\n";
           
           $this->load->view('plantillas/plantilla_general',$data);  
    }
    
    function armar_lista()
    {
       if (!$this->input->is_ajax_request()) die('Acceso Denegado');
       
        $frase=  $this->input->post('frase');
        die(json_encode($this->Estructura->busca_oficina($frase)));       
    }
    
    function listar_productos()
    { 
       if (!$this->input->is_ajax_request()) die('Acceso Denegado'); 
       $id_estructura=  $this->input->post('id'); 
       $productos=$this->Productos->listar_productos($id_estructura);       
       
       //Encabezado de la tabla
       $data='<table class="TablaNivel1">';
       $data.='<thead><tr><th width="40px">Nº</th>';
       $data.='<th width="200px">Nombre del Producto</th>';
       $data.='<th>Definición del Producto</th>';
       $data.='<th width="30px">';
       if (count($productos)>3)
       {         
         $data.='<img class="BotonIco" onclick="javascript:AgregaProducto('.intval($id_estructura).');" ';
         $data.='title="Agregar Producto" src="'.base_url().'imagenes/agregar16.png"/>';
       }       
       $data.='</th>';
       $data.='</tr></thead>';
       $data.='<tfoot><tr><td colspan="4">';
       
       $data.='<img class="BotonIco" onclick="javascript:AgregaProducto('.intval($id_estructura).');" ';
       $data.='title="Agregar Producto" src="'.base_url().'imagenes/agregar.png"/>';
       
       $data.='</td></tr></tfoot>';
       $data.='<tbody>';
       
       if (!$productos) // SI NO HAY PRODUCTOS ADMINISTRATIVOS
       {           
         $data.='<tr><td colspan="4" title="Para Agregar Productos Haga clic en el ícono">';
         $data.='<h2><center> No Posee Productos Administrativos Activos</center></h2>';
         $data.='</td></tr>';
       }   
       else 
       {    
       foreach ($productos as $fila)
       {
        $data.='<tr><td rowspan="2" id="codigoP'.$fila['id_producto'].'" title="Nro del Producto">';
        $data.=trim($fila['codigo']);
        $data.='</td>';
        $data.='<td title="Nombre del Producto" id="nombreP'.$fila['id_producto'].'" class="TextoTabla">';
        $data.=$fila['nombre'];
        $data.='</td>';
        $data.='<td id="definicionP'.$fila['id_producto'].'" title="Definición del Producto" class="TextoTabla">';        
        $data.=$fila['definicion'];
        $data.='</td>';
        $data.='<td class="CeldaIconos">'; // ICONOS DE ACCION
        // BOTON EDITAR
        $data.='<img class="BotonIco" onclick="javascript:EditarProducto('.intval($fila['id_producto']).');" ';
        $data.='src="'.base_url().'imagenes/editar.png" title="Editar Producto" ';
        $data.='/>';
        $data.='<br/>';
        // BOTON Eliminar
        $data.='<img class="BotonIco" onclick="javascript:EliminarProducto('.intval($fila['id_producto']).');" ';
        $data.='src="'.base_url().'imagenes/borrar.png" title="Eliminar Producto" ';
        $data.='/>';
        
        $data.='</td>';
        $data.='</tr>';
        $data.='<tr>';
        $data.='<td colspan="3">';

        $data.='<div class="Botonera">';  // INICIO DE DIV BOTONERA        
        $data.='<div id="BotonSubproductos_'.$fila['id_producto'].'" onclick="javascript:listar_subproductos('.$fila['id_producto'].');" title="clic para ver Sub-Productos">';
        $data.=($fila['subproductos']=='t')?'<img src="'.base_url().'imagenes/hayalgo.png"/>':'';        
        $data.='&nbsp;&nbsp;Sub-Productos';
        $data.='</div>'; // CIERRE DE BOTON SUBPRODUCTOS
        $data.='</div>'; //CIERRE DEL DIV BOTONERA
        $data.='<div class="clear"></div>';
        $data.='<div class="TablaNivel2" id="subproductos_'.$fila['id_producto'].'">'; // DIV CONTENEDOR SUBPRODUCTOS
        $data.='</div>';  //CIERRE DEL DIV DE SUBPRODUCTOS      
        $data.='</td></tr>';
       }
       }
       $data.='</tbody></table>';
       die($data);
    }    
  
    function listar_subproductos()
    {     
       if (!$this->input->is_ajax_request()) die('Acceso Denegado');
       $id_prod= intval($this->input->post('id')); 
       $subproductos=$this->Productos->listar_subproductos($id_prod);
      
       //Encabezado de la tabla
       $data='<table id="TablaSubproductos_'.intval($id_prod).'">';
       $data.='<thead>';       
       $data.='<tr><th width="50px">Nº</th>';
       $data.='<th width="30px"></th>';
       $data.='<th width="200px">Nombre del Sub-Producto</th>';       
       $data.='<th>Definición del Sub-Producto</th>';
       $data.='<th width="200px">Unidad Medida</th>';
       $data.='<th width="30px"></th>';
       $data.='</tr>';
       $data.='</thead>';
       
       $data.='<tfoot>';
       $data.='<tr>';
       $data.='<td colspan="7">&nbsp;';
       $data.='<img class="BotonIco" onclick="javascript:AgregarSubproducto('.intval($id_prod).');" ';
       $data.='src="'.base_url().'imagenes/add_subproducto.png" title="Agregar Sub-Producto" ';
       $data.='/>';       
       $data.='</td>';
       $data.='</tr>';
       $data.='</tfoot>';
       
       $data.='<tbody>';
       
       if (!$subproductos) 
       {
         $data.='<tr><td colspan="6" title="Para Agregar Sub-Productos Haga clic en el ícono">';
         $data.='<h6><center> No Posee Sub-Productos Activos</center></h6>';
         $data.='</td></tr>';         
       }
       else
       {
       foreach ($subproductos as $fila)
       {
          $data.=(trim($fila['activo'])=='t')?'<tr>':'<tr class="inactivo">';             
          $data.='<td rowspan="2" id="SubP'.$fila['id_subproducto'].'" title="Nro del Sub-Producto">';
          $data.=($fila['activo']=='f')?'<img src="'.base_url().'imagenes/cancel.png" title="Sub-Producto Inactivo"/>':'';
          $data.=trim($fila['pcodigo']).'.'.trim($fila['scodigo']).'</td>';
          
          $data.='<td class="CeldaIconos">';          
          $data.='<img src="'.base_url().'imagenes/';          
          $data.=($fila['es_determinado']=='t')?'determinado.png"':'indeterminado.png"';
          $data.=($fila['es_determinado']=='t')?' title="Sub-Producto Determinado"':' title="Sub-Producto Indeterminado"';
          $data.='/>';
          $data.=($fila['es_tramite']=='t')?'<img src="'.base_url().'imagenes/tramite.png" title="Trámite Administrativo a Terceros"/>':'';
          $data.=($fila['es_extraordinario']=='t')?'<img src="'.base_url().'imagenes/medalla.png" title="Sub-Producto Extraordinario"/>':'';
          $data.='</td>'; 
     
          $data.='<td title="Nombre del Sub-Producto">'.$fila['nombre'].'</td>';          
          $data.='<td title="Definición del Sub-Producto">'.$fila['definicion'].'</td>';
          $data.='<td title="Unidad de Medida">'.$fila['unidad_medida'].'</td>';
          $data.='<td class="CeldaIconos">'; // ICONOS DE ACCION
          // BOTON EDITAR
          $data.='<img class="BotonIco" onclick="javascript:EditarSubproducto('.$fila['id_subproducto'].');"';
          $data.='src="'.base_url().'imagenes/edit_subproducto.png" title="Editar Sub-Producto" ';
          $data.='/>';
          $data.='<br/>';
          // BOTON BORRAR
          $data.='<img class="BotonIco" onclick="javascript:EliminarSubproducto('.$fila['id_subproducto'].','.$id_prod.');" ';
          $data.='src="'.base_url().'imagenes/del_subproducto.png" title="Eliminar Sub-Producto" ';
          $data.='/>';
          $data.='</td>';
          
          $data.='<tr>';
          $data.='<td colspan="6">';
          $data.='<div class="Botonera Nivel3">';  // INICIO DE DIV BOTONERA
          $data.='<div id="BotonInsumos_'.$fila['id_subproducto'].'" onclick="javascript:listar_insumos('.$fila['id_subproducto'].');" title="clic para ver Insumo">';
          $data.=($fila['insumo']>0)?'<img src="'.base_url().'imagenes/hayalgo.png"/>':'';
          $data.='&nbsp;&nbsp;Insumo';
          $data.='</div>'; // CIERRE DE BOTON Insumos
          $data.='<div id="BotonDependencias_'.$fila['id_subproducto'].'" onclick="javascript:listar_dependencias('.$fila['id_subproducto'].');" title="clic para ver Dependencias">';
          $data.=($fila['dependencia']>0)?'<img src="'.base_url().'imagenes/hayalgo.png"/>':'';        
          $data.='&nbsp;&nbsp;Dependencias';
          $data.='</div>'; // CIERRE DE BOTON Dependencias
 
          $data.='</div>'; //CIERRE DEL DIV BOTONERA
          $data.='<div class="clear"></div>';
          $data.='<div class="TablaNivel3" id="detalles_'.$fila['id_subproducto'].'">'; // DIV CONTENEDOR DETALLES
          $data.='</div>';  //CIERRE DEL DIV DE DETALLES        
          $data.='</td>';                    
          $data.='</tr>';
       }
       }
       $data.='</tbody>';
       $data.='</table>';
       die($data);
    }
    
    function editar_subproducto()
    {     
       if (!$this->input->is_ajax_request()) die('Acceso Denegado');
       $id_subprod= intval($this->input->post('id_subproducto')); 
       $sp=$this->Productos->obtener_subproducto($id_subprod);
       if (!$sp) die('Error');       
              
       $data='<div class="EntraDatos">';
       $data.='<table>';
       $data.='<thead>';
       $data.='<tr><th colspan="2">';            
       $data.='Editar Información del Sub-Producto Administrativo';         
       $data.='</th></tr>';           
       $data.='</thead>';            
       $data.='<tbody>';
       
       $data.='<tr><td>';
       $data.='<label>Código:</label>';
       $data.='<input type="hidden" id="codProd" value="'.$sp['pcodigo'].'."/>';
       $data.='<input type="text" class="Nom Editable" id="codSubProd" value="'.$sp['pcodigo'].'.'.$sp['scodigo'].'"';
       $data.=' maxlength="9"';
       //onlyDigits(e, value, allowDecimal, allowNegative, allowThousand, decSep, thousandSep, decLength)
       $data.=' onkeypress="return onlyDigits(event, this.value,true,false,false,\'.\',\',\',2);"';
       $data.=' onkeyup="return onlyDigits(event, this.value,true,false,false,\'.\',\',\',2);"';
       $data.=' onblur="return onlyDigits(event, this.value,true,false,false,\'.\',\',\',2);"';
       
       $data.='/>';
       $data.='<td>';
       $data.='<label>Unidad de Medida:</label>';
       $data.='<input type="text" class="Nom Editable" id="UMNvoS" tabindex="1002" title="Escriba la Unidad de Medida del Sub-Producto"';
       $data.='value="'.$sp['unidad_medida'].'"/>';
       $data.='</td></tr>';
       
       $data.='<tr><td colspan="2">';
       $data.='<label>Nombre del Sub-Producto:</label>';
       $data.='<textarea class="Nom Editable" id="nombreNvoS" rows="1" tabindex="1000" title="Escriba el Nombre del Sub-Producto">';
       $data.=$sp['nombre'];
       $data.='</textarea>';
       $data.='</td></tr>';
       $data.='<tr><td colspan="2">';
       $data.='<label>Definición del Sub-Producto:</label>';
       $data.='<textarea class="Nom Editable" id="defNvoS" rows="4" tabindex="1001" title="Escriba la Definición del Sub-Producto">';
       $data.=$sp['definicion'];
       $data.='</textarea>';
       $data.='</td></tr>';            
       $data.='<tr>';
       $data.='<td width="40%">';
       // BOTON DETERMINADO/INDETERMINADO
            if ($sp['es_determinado']=='t')
            {
                $datos=array(
                            'img'  =>base_url()."imagenes/determinado.png",
                            'span' => 'Sub-Producto Determinado',
                            'valor'=>'t');
            }
            else
            {
                $datos=array(
                            'img'  =>base_url()."imagenes/indeterminado.png",
                            'span' => 'Sub-Producto Indeterminado',
                            'valor'=>'f');
            }                 
       $data.='<div class="ToggleBoton" onclick="javascript:ToggleBotonDet()" title="Haga clic para cambiar">';
       $data.='<img id="imgDet" src="'.$datos['img'].'"/>';
       $data.='</div>';
       $data.='<span id="spanDet">&nbsp;'.$datos['span'].'</span>';
       $data.='<input type="hidden" id="hideDet" value="'.$datos['valor'].'" />';
       // FIN BOTON DETERMINADO/INDETERMINADO         
       $data.='</td>';
       $data.='<td>';         
       // BOTON TRAMITE/NO TRAMITE
            if ($sp['es_tramite']=='t')
            {
                $datos=array(
                            'img'  =>base_url()."imagenes/tramite.png",
                            'span' => 'Trámite Administrativo a Terceros',
                            'valor'=>'t');
            }
            else
            {
                $datos=array(
                            'img'  =>base_url()."imagenes/notramite.png",
                            'span' => 'No es Trámite Administrativo a Terceros',
                            'valor'=>'f');
            }                 
       $data.='<div class="ToggleBoton" onclick="javascript:ToggleBotonTra()" title="Haga clic para cambiar">';
       $data.='<img id="imgTra" src="'.$datos['img'].'"/>';
       $data.='</div>';
       $data.='<span id="spanTra">&nbsp;'.$datos['span'].'</span>';
       $data.='<input type="hidden" id="hideTra" value="'.$datos['valor'].'" />';
       // FIN BOTON TRAMITE/NO TRAMITE         
       $data.='</td>';
       $data.='</tr>'; 
       $data.='<tr>';
       $data.='<td>';
       // BOTON EXTRAORDINARIO/ORDINARIO
            if ($sp['es_extraordinario']=='t')
            {
                $datos=array(
                            'img'  =>base_url()."imagenes/medalla.png",
                            'span' => 'Sub-Producto Extraordinario',
                            'valor'=>'t');
            }
            else
            {
                $datos=array(
                            'img'  =>base_url()."imagenes/lego.png",
                            'span' => 'Sub-Producto Ordinario',
                            'valor'=>'f');
            }                 
       $data.='<div class="ToggleBoton" onclick="javascript:ToggleBotonExtra()" title="Haga clic para cambiar">';
       $data.='<img id="imgExtra" src="'.$datos['img'].'"/>';
       $data.='</div>';
       $data.='<span id="spanExtra">&nbsp;'.$datos['span'].'</span>';
       $data.='<input type="hidden" id="hideExtra" value="'.$datos['valor'].'" />';
       // FIN BOTON EXTRAORDINARIO/ORDINARIO
       $data.='</td>';                  
       $data.='<td>';          
       // BOTON ACTIVO/INACTIVO
            if ($sp['activo']=='t')
            {
                $datos=array(
                            'img'  =>base_url()."imagenes/activo16.png",
                            'span' => 'Sub-Producto Activo',
                            'valor'=>'t');
            }
            else
            {
                $datos=array(
                            'img'  =>base_url()."imagenes/cancel16.png",
                            'span' => 'Sub-Producto Inactivo',
                            'valor'=>'f');
            }                 
       $data.='<div class="ToggleBoton" onclick="javascript:ToggleBotonActivo()" title="Haga clic para cambiar">';
       $data.='<img id="imgActivo" src="'.$datos['img'].'"/>';
       $data.='</div>';
       $data.='<span id="spanActivo">&nbsp;'.$datos['span'].'</span>';
       $data.='<input type="hidden" id="hideActivo" value="'.$datos['valor'].'" />';
       // FIN BOTON ACTIVO/INACTIVO         
       $data.='</td>';        
       $data.='</tr>';        
       $data.='</tbody>';
       $data.='<tfoot>';
       $data.='<tr><td colspan="2">';
       $data.='<div class="BotonIco" onclick="javascript:ActualizarSubproducto('.$id_subprod.','.$sp['id_producto'].')" title="Guardar Cambios">';
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
    
    function listar_dependencias()
    {     
       if (!$this->input->is_ajax_request()) die('Acceso Denegado');
       $id_subprod=  $this->input->post('id'); 
       $subprod_depen=$this->Productos->listar_dependencias($id_subprod);
       
       //Encabezado de la tabla      
       $data='<table id="TablaDependencias_'.intval($id_subprod).'">';
       $data.='<thead>';
       $data.='<tr><th width="50px">Nº</th>';
       $data.='<th width="300px">Nombre del Sub-Producto</th>';
       $data.='<th>Unidad a la que Pertence el Sub-Producto</th>';
       $data.='<th width="40px"></th>';
       $data.='</tr>';   
       $data.='</thead>';
       
       $data.='<tfoot>';
       $data.='<tr>';
       $data.='<td colspan="4">';
       $data.='<a href="#SubP'.$id_subprod.'">';
       $data.='<img class="BotonIco" onclick="javascript:AgregarDependencia('.intval($id_subprod).');" ';
       $data.='src="'.base_url().'imagenes/add_dependencia.png" title="Agregar dependencia" ';
       $data.='/>';
       $data.='</a>';
       $data.='</td>';
       $data.='</tr>';
       $data.='</tfoot>';
       
       $data.='<tbody>';
       
       if (!$subprod_depen) 
       {
         $data.='<tr><td colspan="4" title="Para vincular dependencias haga clic en el ícono">';
         $data.='<h6><center> No Posee Sub-Productos de los que Dependa</center></h6>';
         $data.='</td></tr>';          
       }
       else
       {
       foreach ($subprod_depen as $fila)
       {
          $data.='<tr>';
         $data.='<td title="Número de Sub-Producto">';          
          $data.=trim($fila['pcod']).'.'.trim($fila['scod']).'</td>';
          $data.='<td title="Sub-Producto del cual depende">';
          $data.=trim($fila['nombre_s']).'<br/>';
          $data.='<i>(Producto: '.trim($fila['pcod']).'. '.trim($fila['nombre_p']).')</i>';
          $data.='</td>';
          $data.='<td title="Unidad a la que pertenece el Sub-Producto"><center>';
          $data.='<strong>'.trim($fila['cod_estructura']).' - '.trim($fila['unidad']).'</strong><br/>'.trim($fila['superior']).'</center></td>';
           // BOTON BORRAR DEPENDENCIA
          $data.='<td>';
          $data.='<img class="BotonIco" onclick="javascript:EliminarDependencia('.intval($fila['id_dependencia']).','.$id_subprod.');" ';
          $data.='src="'.base_url().'imagenes/del_dep.png" title="Eliminar Dependencia" ';
          $data.='/>';
          $data.='</td>';          
          $data.='</tr>';          
       }
       }
       $data.='</tbody>';
       $data.='</table>';
       die($data);
    }
    
    function listar_insumo()
    {
       if (!$this->input->is_ajax_request()) die('Acceso Denegado'); 
       $id_subprod=  $this->input->post('id'); 
       $subprod_depen=$this->Productos->listar_insumo($id_subprod);
       
       //Encabezado de la tabla       
       $data='<table width="100%">';
       $data.='<thead><tr>';
       $data.='<th width="50px">Nº</th>';
       $data.='<th width="300px">Nombre del Sub-Producto</th>';
       $data.='<th>Unidad a la que Pertence el Sub-Producto para el que es Insumo</th>';
       $data.='</tr></thead>';
       
       if (!$subprod_depen) 
       {
         $data.='<tr><td colspan="3">';
         $data.='<h6><center>No Posee Sub-Productos para los que sea Insumo</center></h6>';
         $data.='</td></tr>';          
       }
       else
       {
       foreach ($subprod_depen as $fila)
       {
          $data.='<tr>';
          $data.='<td title="Número de Sub-Producto">';          
          $data.=trim($fila['pcod']).'.'.trim($fila['scod']).'</td>';
          $data.='<td title="Sub-Producto para el que es Insumo">';
          $data.=trim($fila['nombre_s']).'<br/>';
          $data.='<i>(Producto: '.trim($fila['pcod']).'. '.trim($fila['nombre_p']).')</i>';
          $data.='</td>';
          $data.='<td title="Unidad a la que pertenece el Sub-Producto"><center>';
          $data.='<strong>'.trim($fila['cod_estructura']).' - '.trim($fila['unidad']).'</strong><br/>'.trim($fila['superior']).'</center></td>';
          $data.='</tr>';
       }
       }
       $data.='</table>';
       die($data);
    }    
    
    function agregar_producto()
    {      
        if (!$this->input->is_ajax_request()) die('Acceso Denegado');
        $id_estructura= intval($this->input->post('id_estructura'));
        $tabla='<div class="EntraDatos">';
        $tabla.='<table>';
        $tabla.='<thead>';
        $tabla.='<tr><th>';            
        $tabla.='Creación del Nuevo Producto Administrativo Nº ';
        $tabla.='<input type="text" id="codNvo" readonly="readonly" value="';
        $tabla.=$this->Crud->contar_items('c_productos', array('id_estructura' => $id_estructura))+1;
        $tabla.='" />';            
        $tabla.='</th></tr>';           
        $tabla.='</thead>';
        $tabla.='<tbody>';
        $tabla.='<tr><td>';              
        $tabla.='<label>Escriba el Nombre del Nuevo Producto:</label>';
        $tabla.='<textarea id="nombreNvo" class="Nom Editable" rows="2" tabindex="1000" title="Escriba el Nombre del Producto"></textarea>';
        $tabla.='</td></tr>';
        $tabla.='<tr><td>';
        $tabla.='<label>Escriba la Definición del Nuevo Producto:</label>';
        $tabla.='<textarea id="defNvo" class="Nom Editable" rows="6" tabindex="1001" title="Escriba la Definición del Producto"></textarea>';
        $tabla.='</td></tr>';
        $tabla.='</tbody>';
        $tabla.='<tfoot>';
        $tabla.='<tr><td>';
        $tabla.='<div class="BotonIco" onclick="javascript:GuardarProducto()" title="Guardar Producto">';
        $tabla.='<img src="imagenes/guardar32.png"/>&nbsp;';   
        $tabla.='Guardar';
        $tabla.= '</div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        $tabla.='<div class="BotonIco" onclick="javascript:CancelarModal()" title="Cancelar">';
        $tabla.='<img src="imagenes/cancel.png"/>&nbsp;';
        $tabla.='Cancelar';
        $tabla.= '</div>';
        $tabla.='</td></tr>';
        $tabla.='</tfoot>';
        $tabla.='</table>';   
        $tabla.='</div>';
        die ($tabla);
    }
    
    function agregar_subproducto()
    {      
       if (!$this->input->is_ajax_request()) die('Acceso Denegado');
       $id_producto= intval($this->input->post('id_producto'));
       $cod_prod= intval($this->input->post('cod_prod'));
        
       $tabla='<div class="EntraDatos">';
       $tabla.='<table>';
       $tabla.='<thead>';
       $tabla.='<tr><th colspan="2">';            
       $tabla.='Nuevo Sub-Producto Administrativo Nº ';
       $tabla.='<input type="hidden" id="codNvoS" value="';
       $tabla.=$this->Crud->contar_items('c_subproductos', array('id_producto' => $id_producto))+1;
       $tabla.='"/>';
       $tabla.='<input type="text" readonly="readonly" value="';
       $tabla.=$cod_prod.'.'; 
       $tabla.=$this->Crud->contar_items('c_subproductos', array('id_producto' => $id_producto))+1;
       $tabla.='" />';            
       $tabla.='</th></tr>';           
       $tabla.='</thead>';            
       $tabla.='<tbody>';
       $tabla.='<tr><td colspan="2">';              
       $tabla.='<label>Nombre del Sub-Producto:</label>';
       $tabla.='<textarea id="nombreNvoS" class="Nom Editable" rows="1" tabindex="1000" title="Escriba el Nombre del Sub-Producto"></textarea>';
       $tabla.='</td></tr>';
       $tabla.='<tr><td colspan="2">';
       $tabla.='<label>Definición del Sub-Producto:</label>';
       $tabla.='<textarea id="defNvoS" class="Nom Editable" rows="4" tabindex="1001" title="Escriba la Definición del Sub-Producto"></textarea>';
       $tabla.='</td></tr>';
       $tabla.='<tr><td colspan="2">';
       $tabla.='<label>Unidad de Medida:</label>';
       $tabla.='<textarea id="UMNvoS" class="Nom Editable" rows="1" tabindex="1002" title="Escriba la Unidad de Medida del Sub-Producto"></textarea>';
       $tabla.='</td></tr>';            
       $tabla.='<tr>';
       $tabla.='<td width="50%">';
       $tabla.='<div class="ToggleBoton" onclick="javascript:ToggleBotonDet()" title="Haga clic para cambiar">';
       $tabla.='<img id="imgDet" src="imagenes/indeterminado.png"/>';            
       $tabla.='</div>';            
       $tabla.='<span id="spanDet">&nbsp;Sub-Producto Indeterminado</span>';
       $tabla.='<input type="hidden" id="hideDet" value="f" />';
       $tabla.='</td>';
       $tabla.='<td>';
       $tabla.='<div class="ToggleBoton" onclick="javascript:ToggleBotonTra()" title="Haga clic para cambiar">';
       $tabla.='<img id="imgTra" src="imagenes/notramite.png"/>';
       $tabla.='</div>';
       $tabla.='<span id="spanTra">&nbsp;No es Trámite Administrativo a Terceros</span>';
       $tabla.='<input type="hidden" id="hideTra" value="f" />';
       $tabla.='</td>';
       $tabla.='</tr>'; 
       $tabla.='<tr>';
       $tabla.='<td>'; 
       
       $tabla.='</td>'; 
       $tabla.='<td>'; 
       $tabla.='<div class="ToggleBoton" onclick="javascript:ToggleBotonExtra()" title="Haga clic para cambiar">';
       $tabla.='<img id="imgExtra" src="imagenes/lego.png"/>';
       $tabla.='</div>';
       $tabla.='<span id="spanExtra">&nbsp;Sub-Producto Ordinario</span>';
       $tabla.='<input type="hidden" id="hideExtra" value="f" />';            
       $tabla.='</td>'; 
       $tabla.='</tr>'; 
       $tabla.='</tbody>';
       $tabla.='<tfoot>';
       $tabla.='<tr><td colspan="2">';
       $tabla.='<div class="BotonIco" onclick="javascript:GuardarSubproduto('.$id_producto.')" title="Guardar Sub-Producto">';
       $tabla.='<img src="imagenes/guardar32.png"/>&nbsp;';   
       $tabla.='Guardar';
       $tabla.= '</div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
       $tabla.='<div class="BotonIco" onclick="javascript:CancelarModal()" title="Cancelar">';
       $tabla.='<img src="imagenes/cancel.png"/>&nbsp;';
       $tabla.='Cancelar';
       $tabla.= '</div>';
       $tabla.='</td></tr>';
       $tabla.='</tfoot>';
       $tabla.='</table>';   
       $tabla.='</div>';        
       die($tabla);
    }
    
    function insertar_producto()
    {      
        if (!$this->input->is_ajax_request()) die('Acceso Denegado');
        $producto=array(
                'id_estructura'     => $this->input->post('id_estructura'),
                'codigo'      => $this->input->post('cod_nvo'),
                'nombre'   => $this->input->post('nombre_nvo'),
                'definicion'        => $this->input->post('def_nvo')              
                );        
        $insertado=$this->Crud->insertar_registro('c_productos', $producto);
        if (!$insertado){die('Error');}
        else 
        {
           $registro='id_producto: '.$this->db->insert_id();
           $registro.='. Producto: '.$producto['nombre'];           
           $registro.='. Registrado por: '.$this->session->userdata('usuario');
           $bitacora=array(
               'direccion_ip'   =>$this->session->userdata('ip_address'),
               'navegador'      =>$this->session->userdata('user_agent'),
               'id_usuario'     =>$this->session->userdata('id_usuario'),
               'controlador'    =>$this->uri->uri_string(),
               'tabla_afectada' =>'c_productos',
               'tipo_accion'    =>'INSERT',
               'registro'       =>$registro
           );
           $this->Crud->insertar_registro('z_bitacora', $bitacora);
        }
    }
    
    function actualizar_producto()
    {
        if (!$this->input->is_ajax_request()) die('Acceso Denegado');
        $donde=array(
                  'id_producto'     => intval($this->input->post('id_prod'))       
                    );
        $datos=array(                
                'codigo'    => intval($this->input->post('cod')),
                'nombre'    => $this->input->post('nom'),
                'definicion'=> $this->input->post('def')                
                );        
        $actualizado=$this->Crud->actualizar_registro('c_productos', $datos, $donde);        
        if (!$actualizado){die('Error');}
        else 
        {           
           $registro='id_producto: '.$donde['id_producto'];
           $registro.='. Producto: '.$datos['nombre'];
           $registro.='. Actualizado por: '.$this->session->userdata('usuario');
           $bitacora=array(
               'direccion_ip'   =>$this->session->userdata('ip_address'),
               'navegador'      =>$this->session->userdata('user_agent'),
               'id_usuario'     =>$this->session->userdata('id_usuario'),
               'controlador'    =>$this->uri->uri_string(),
               'tabla_afectada' =>'c_productos',
               'tipo_accion'    =>'UPDATE',
               'registro'       =>$registro
           );
           $this->Crud->insertar_registro('z_bitacora', $bitacora);            
        };
    }
    
    function insertar_subproducto()
    {
       if (!$this->input->is_ajax_request()) die('Acceso Denegado');
        $datos=array(
                'id_producto'      => $this->input->post('id_prod'),
                'codigo'           => $this->input->post('cod_nvo'),
                'nombre'           => $this->input->post('nombre_nvo'),
                'definicion'       => $this->input->post('def_nvo'),
                'unidad_medida'    => $this->input->post('um_nvo'),
                'es_determinado'   => $this->input->post('det_nvo'),
                'es_tramite'       => $this->input->post('tra_nvo'),
                'es_extraordinario'=> $this->input->post('extra_nvo')
                );
        $insertado=$this->Crud->insertar_registro('c_subproductos', $datos);
        if (!$insertado){die('Error');}
        else
        {
           $registro='id_subproducto: '.$this->db->insert_id();
           $registro.='. Para el id_producto: '.$datos['id_producto'];           
           $registro.='. Registrado por: '.$this->session->userdata('usuario');
           $bitacora=array(
               'direccion_ip'   =>$this->session->userdata('ip_address'),
               'navegador'      =>$this->session->userdata('user_agent'),
               'id_usuario'     =>$this->session->userdata('id_usuario'),
               'controlador'    =>$this->uri->uri_string(),
               'tabla_afectada' =>'c_subproductos',
               'tipo_accion'    =>'INSERT',
               'registro'       =>$registro
           );
           $this->Crud->insertar_registro('z_bitacora', $bitacora);             
        }
    }
    
    function actualizar_subproducto()
    {
       if (!$this->input->is_ajax_request()) die('Acceso Denegado'); 
       $codigo= trim($this->input->post('scod'));
       $pos = strpos($codigo, '.');
       $codigo= ($pos===false)?$codigo:intval(substr("$codigo", ($pos+1)));
       $codigo=intval(str_replace($this->input->post('pcod'),'',$codigo));
       $datos=array(              
                'codigo'           => $codigo,
                'nombre'           => $this->input->post('nom'),
                'definicion'       => $this->input->post('def'),
                'unidad_medida'    => $this->input->post('um'),
                'es_determinado'   => $this->input->post('det'),
                'es_tramite'       => $this->input->post('tra'),
                'es_extraordinario'=> $this->input->post('extra'),
                'activo'           => $this->input->post('activo')
                );
       $donde=array(
                'id_subproducto' => $this->input->post('id_subprod')                
                );
       $actualizado=$this->Crud->actualizar_registro('c_subproductos', $datos, $donde);
       if (!$actualizado){die('Error');}
       else 
       {
           $registro='id_subproducto: '.$donde['id_subproducto'];           
           $registro.='. Actualizado por: '.$this->session->userdata('usuario');
           $bitacora=array(
               'direccion_ip'   =>$this->session->userdata('ip_address'),
               'navegador'      =>$this->session->userdata('user_agent'),
               'id_usuario'     =>$this->session->userdata('id_usuario'),
               'controlador'    =>$this->uri->uri_string(),
               'tabla_afectada' =>'c_subproductos',
               'tipo_accion'    =>'UPDATE',
               'registro'       =>$registro
                 );
           $this->Crud->insertar_registro('z_bitacora', $bitacora); 
           
       }
    }
    
    function listar_subproductos_unidad()
    {
      if (!$this->input->is_ajax_request()) die('Acceso Denegado'); 
      $id_estructura=$this->input->post('id_unidad'); 
      $id_subproducto=$this->input->post('id_subprod');
      $subproductos=$this->Productos->listar_subproductos_unidad($id_estructura, $id_subproducto);       
      
      //Encabezado de la tabla
      $data='<table width="100%">';
      $data.='<thead>';
      $data.='<tr><th width="5%">Nº</th>';
      $data.='<th width="30%">Nombre del Sub-Producto</th>';
      $data.='<th>Definición del Sub-Producto</th>';
      $data.='<th width="35px"></th>';
      $data.='</tr></thead>';
      
      $data.='<tfoot>';
      $data.='<tr><td colspan="4">';
      $data.='(Haga Click en el Ícono "Crear Dependencia" de cada Sub-Producto que Desee Agregar)';
      $data.='</td></tr>';
      $data.='</tfoot>';
      
      $data.='<tbody>';
      
      if (!$subproductos) // SI NO HAY SUB-PRODUCTOS ADMINISTRATIVOS
      {           
        $data.='<tr><td colspan="4" title="Seleccione otra Unidad que posea sub-productos">';
        $data.='<h2><center> No Posee Sub-Productos Administrativos Activos</center></h2>';
        $data.='</td></tr>';
      }   
      else 
      {    
      foreach ($subproductos as $fila)
      {
       $data.='<tr><td title="Número de Sub-Producto">';
       $data.=trim($fila['pcodigo']).'.'.trim($fila['scodigo']);
       $data.='</td>';
       $data.='<td title="Nombre del Sub-Producto">';
       $data.=trim($fila['nombre']).'<br/>';
       $data.='<i>(Producto: '.trim($fila['pcodigo']).'. '.trim($fila['producto']).')</i>';       
       $data.='</td>';
       $data.='<td title="Definición del Sub-Producto">';
       $data.=trim($fila['definicion']);
       $data.='</td>';
       $data.='<td>'; 
       $data.='<img class="BotonIco" id="Imagen_'.$id_subproducto.'_'.trim($fila['id_subproducto']).'" ';
       $data.='onclick="javascript:CrearDependencia('.$id_subproducto.','.trim($fila['id_subproducto']).')" ';
       $data.='src="'.base_url().'imagenes/add_dependencia.png" title="Crear Dependencia" ';
       $data.='/>';
       $data.='</td></tr>';
      }
      }
      $data.='</tbody></table>';
      die($data);  
    }
    
    function crear_dependencia()
    {      
        if (!$this->input->is_ajax_request()) die('Acceso Denegado');
        $subproducto=array(
                'id_subproducto'     => $this->input->post('id_subprod'),
                'id_subprod_depen'   => $this->input->post('id_subprod_depen')
                );
        
        $insertado=$this->Crud->insertar_registro('c_dependencias', $subproducto);        
        if (!$insertado){die('Error');}
        else 
        {
           $registro='id_dependencia: '.$this->db->insert_id();
           $registro.='. Sub-Producto id_subproducto: '.$subproducto['id_subproducto'];
           $registro.=' Depende de id_subproducto: '.$producto['id_subprod_depen'];           
           $registro.='. Registrado por: '.$this->session->userdata('usuario');
           $bitacora=array(
               'direccion_ip'   =>$this->session->userdata('ip_address'),
               'navegador'      =>$this->session->userdata('user_agent'),
               'id_usuario'     =>$this->session->userdata('id_usuario'),
               'controlador'    =>$this->uri->uri_string(),
               'tabla_afectada' =>'c_dependencias',
               'tipo_accion'    =>'INSERT',
               'registro'       =>$registro
           );
           $this->Crud->insertar_registro('z_bitacora', $bitacora); 
        }
    }       
    
    function eliminar_dependencia()
    {
        if (!$this->input->is_ajax_request()) die('Acceso Denegado');
        $dependencia=array(
            'id_dependencia' => intval($this->input->post('id_dependencia'))
                          );        
        $borrado=$this->Crud->eliminar_registro('c_dependencias', $dependencia);
        if (!$borrado){die('Error');}
        else 
        {
           $registro='id_dependencia: '.$dependencia['id_dependencia'];           
           $registro.='. Borrado por: '.$this->session->userdata('usuario');
           $bitacora=array(
               'direccion_ip'   =>$this->session->userdata('ip_address'),
               'navegador'      =>$this->session->userdata('user_agent'),
               'id_usuario'     =>$this->session->userdata('id_usuario'),
               'controlador'    =>$this->uri->uri_string(),
               'tabla_afectada' =>'c_dependencias',
               'tipo_accion'    =>'DELETE',
               'registro'       =>$registro
           );
           $this->Crud->insertar_registro('z_bitacora', $bitacora);    
        }
    }
    
    function eliminar_producto()
    {
        if (!$this->input->is_ajax_request()) die('Acceso Denegado');
        $producto=array(
            'id_producto' => intval($this->input->post('id_prod'))
                       );        
        $borrado=$this->Crud->eliminar_registro('c_productos', $producto);
        if (!$borrado){die('Error');}
        else
        {
           $registro='id_producto: '.$producto['id_producto'];           
           $registro.='. Borrado por: '.$this->session->userdata('usuario');
           $bitacora=array(
               'direccion_ip'   =>$this->session->userdata('ip_address'),
               'navegador'      =>$this->session->userdata('user_agent'),
               'id_usuario'     =>$this->session->userdata('id_usuario'),
               'controlador'    =>$this->uri->uri_string(),
               'tabla_afectada' =>'c_productos',
               'tipo_accion'    =>'DELETE',
               'registro'       =>$registro
           );
           $this->Crud->insertar_registro('z_bitacora', $bitacora); 
        }
    }
    
    function eliminar_subproducto()
    {
        if (!$this->input->is_ajax_request()) die('Acceso Denegado');
        $dato=array(
            'id_subproducto' => intval($this->input->post('id_subproducto'))
                       );        
        $borrado=$this->Crud->eliminar_registro('c_subproductos', $dato);
        if (!$borrado){die('Error');}
        else 
        {
           $registro='id_subproducto: '.$dato['id_subproducto'];           
           $registro.='. Borrado por: '.$this->session->userdata('usuario');
           $bitacora=array(
               'direccion_ip'   =>$this->session->userdata('ip_address'),
               'navegador'      =>$this->session->userdata('user_agent'),
               'id_usuario'     =>$this->session->userdata('id_usuario'),
               'controlador'    =>$this->uri->uri_string(),
               'tabla_afectada' =>'c_subproductos',
               'tipo_accion'    =>'DELETE',
               'registro'       =>$registro
           );
           $this->Crud->insertar_registro('z_bitacora', $bitacora); 
        }
    } 
}?>