<?php if ( ! defined('BASEPATH')) exit('Sin Acceso Directo al Script');?>

<div class="EntraDatos">
 <table>
  <thead>
   <tr>
    <th colspan="2">            
       Edición de la Planificación del Sub-Producto 
    </th>
   </tr>           
  </thead>            
  <tbody>
   <tr>
    <td colspan="2">
      <label>Sub-Producto:</label>
      <input type="text" class="Campos" id="Correo" title="Nombre del Sub-Producto" 
             readonly="readonly" 
             value="<?php echo $plan['pcodigo'].'.'.$plan['scodigo'].'&nbsp;'.trim($plan['nombre']); ?>" />
    </td>
   </tr> 
   <tr>
    <td colspan="2">  
     <label>Descripción de la Actividad:</label>
     <input type="text" class="Campos Editable" id="Actividad" 
            value="<?php echo trim($plan['descripcion']); ?>"
            title="Descripción de la Actividad" tabindex="1002" />
    </td>  
   </tr>
   <tr>
    <td colspan="2">  
     <label>Cantidad:</label>
     <input type="text" class="Editable" id="Cantidad" title="Debe ser mayor a uno (1)"
            maxlength="5" size="5" style="text-align:center"
            title="Cantidad Planificada" tabindex="1003" 
            value="<?php echo $plan['cantidad']; ?>"
            onblur="this.value=formatNumber(this.value,0);"
            onkeyup="formatNumber(this.value,0);" 
            onkeypress="return onlyDigits(event, this.value, false,false,false,',','.',0);" />
     <span><?php echo $plan['unidad_medida']; ?></span>
    </td>  
   </tr>  
   <tr>
    <td width="50%">
     <label>Responsable de la Actividad:</label>
     <select class="Campos Editable" id="Responsable" title="Seleccione el Responsable" 
             tabindex="1004">
     <?php echo $usuarios; ?>
     </select>
    </td>
   </tr>
   <tr> 
    <td>
     <label>Fecha de Inicio:</label>
     <input type="text" class="Fechas Editable" id="fechaI" title="Fecha Inicial"
            value="<?php echo date("d/m/Y",strtotime($plan['fecha_ini'])); ?>"
            tabindex="1005" readonly="readonly"/>
    </td>
    <td>
     <label>Fecha de Culminación:</label>
     <input type="text" class="Fechas Editable" id="fechaF" title="Fecha Final" 
            value="<?php echo date("d/m/Y",strtotime($plan['fecha_fin'])); ?>"
            tabindex="1006" readonly="readonly" />
    </td>
   </tr>
  </tbody>

  <tfoot>
   <tr>
    <td colspan="2">
     <div class="BotonIco" 
          onclick="javascript:eliminarActividad(<?php echo $plan['id_plan_producto']; ?>)" 
          title="Eliminar Actividad">
      <img src="imagenes/plan_del.png"/>&nbsp;
      Eliminar
     </div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;        
     <div class="BotonIco" 
          onclick="javascript:actualizarActividad(<?php echo $plan['id_plan_producto']; ?>)" 
          title="Guardar Actividad">
       <img src="imagenes/guardar32.png"/>&nbsp;   
                        Guardar
     </div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
     <div class="BotonIco" onclick="javascript:CancelarModal()" title="Cancelar">
       <img src="imagenes/cancel.png"/>&nbsp;
                        Cancelar
     </div>
    </td>
   </tr>
  </tfoot>
 </table>   
</div>