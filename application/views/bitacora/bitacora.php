<?php if ( ! defined('BASEPATH')) exit('Sin Acceso Directo al Script');?>
<div class="container_16 CuerpoPpal">
<div class="grid_16 alpha omega AjusteCuerpo">
  <div class="grid_4" style="margin-bottom: 10px">
    <legend>&nbsp;</legend>
    <table class="SubFormulario">                 
        <tbody>
            <tr>
              <td>
                  <label>Desde:</label>
              </td>
              <td>
                  <?php echo form_input($fecha_ini);?>
              </td>
            </tr>
            <tr>
              <td>                 
              </td>
            </tr> 
            <tr>
              <td>
                  <label>Hasta:</label>
              </td>
              <td>
                  <?php echo form_input($fecha_fin);?>
              </td>
            </tr>
            <tr>
              <td>                 
              </td>
            </tr>                          
        </tbody>
    </table>         
  </div> 
  <div class="grid_8 suffix_4 alpha omega">
    <h1>BITACORA DEL SISTEMA</h1>   
  </div>      
  <div class="clear"></div>    
  <div class="grid_16" id="Tabla">
    <?php echo $tabla_bitacora;?>
  </div>
 
  <div class="clear"></div>
</div>
</div>