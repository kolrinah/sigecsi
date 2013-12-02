<?php if ( ! defined('BASEPATH')) exit('Sin Acceso Directo al Script');?>

<div class="EntraDatos">
 <table>
  <thead>
   <tr>
    <th colspan="2">Registro de Ejecución</th>
   </tr>           
  </thead>            
  <tbody> 
   <tr>
       <td colspan="2"> </td>
   </tr>   
   <tr>
    <td style="vertical-align:top; text-align:right; padding:2px; width: 200px">
        <i>Cantidad Ejecutada: </i>
    </td>       
    <td style="vertical-align:top; padding:2px 25px 2px 5px">
      <input type="text" style="text-align:center" size="5" maxlength="10"
                      id="cantidadEjecutada" class="Editable"
       onblur="this.value=formatNumber(this.value,0);"
       onkeyup="formatNumber(this.value,0);" 
       onkeypress="return onlyDigits(event, this.value, false,false,false,',','.',0);" 
       value="" /> <?php echo $sp['unidad_medida']; ?>     
    </td>
   </tr>
   <tr>
    <td style="vertical-align:top; text-align:right; padding:2px">
     <i>Fecha de Ejecución: </i>
    </td>
    <td style="vertical-align:top; padding:2px 25px 2px 5px">
     <input type="text" class="Fechas Editable" id="fechaEjecucion" 
            title="Fecha de Ejecución del Producto"  readonly="readonly"
            value="<?php echo date("d/m/Y",time()); ?> " />
    </td>
   </tr>
   <tr>
    <td style="vertical-align:top; text-align:right; padding:2px"><i>Descripción: </i>
    </td>
    <td style="vertical-align:top; padding:2px 25px 2px 5px">
      <textarea class="CampoFicha Editable" id="descripcion" rows="2" ></textarea>
    </td>    
   </tr>
   <tr>
    <td style="vertical-align:top; text-align:right; padding:2px; width: 200px">
        <i>Usuario Ejecutante: </i>
    </td>       
    <td style="vertical-align:top; padding:2px 25px 2px 5px">
      <select class="Editable" id="idUsuario" >
        <?php echo $u; ?>  
      </select>    
    </td>
   </tr>   
   <tr>
       <td colspan="2"> </td>
   </tr>    
  </tbody>      
  <tfoot>
   <tr>
    <td colspan="2"> 
  
     
     <div class="BotonIco" title="Guardar Cambios"
          onclick="javascript:guardarRegistro(<?php echo $sp['id_subproducto'];?>)" >
      <img src="imagenes/guardar32.png"/>&nbsp;   
        Guardar
     </div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;    
  
     <div class="BotonIco" onclick="javascript:CancelarModal()" title="Cerrar">
      <img src="imagenes/cancel.png"/>&nbsp;
      Cerrar
     </div>
    </td>
   </tr>
  </tfoot>
 </table>   
</div>