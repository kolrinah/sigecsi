<?php if ( ! defined('BASEPATH')) exit('Sin Acceso Directo al Script');?>
<div class="container_16 CuerpoPpal">
<div class="grid_16 alpha omega AjusteCuerpo">
  <div class="grid_16 alpha omega">
    <h1><center>PRODUCTOS ADMINISTRATIVOS</center></h1>   
  </div>  
  <div class="clear"></div>
  <div class="prefix_4 grid_12 alpha omega" id="superiores">
  </div>
  <div class="clear"></div>  
  <div class="grid_3 omega alpha" style="text-align:right; padding-top: 10px;">
       Unidad: <?php echo form_input($formulario['codigo']);?>
  </div>
  <div class="grid_13">
       <?php echo form_input($formulario['unidad']);?>
       <?php echo form_input($formulario['id_unidad']);?>
       <?php echo form_input($formulario['base_url']);?>
  </div>  
  <div class="clear"></div>  
  <div class="grid_16" id="productos" style="margin-top: 30px; min-height: 100%">
  <br/><br/>
  </div>
  <div class="clear"></div>

  <div class="grid_16 alpha omega" id="errores" style="text-align: center">  
  </div> 
  <div class="clear"></div>
</div>
</div>