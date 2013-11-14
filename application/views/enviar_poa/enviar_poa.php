<?php if ( ! defined('BASEPATH')) exit('Sin Acceso Directo al Script');?>
<div class="container_16 CuerpoPpal">
<div class="grid_16 alpha omega AjusteCuerpo">
  <div class="grid_16 alpha omega">
    <h1><?php echo $titulo;?></h1>
    <h4><?php echo $subtitulo;
              echo img($flecha_sus);
              echo form_input($year_poa); 
              echo img($flecha_sum);
              echo form_input($id_unidad); 
        ?>
    </h4>
  </div>  
  <div class="clear"></div>
  <div class="grid_16" id="Planes">
    <?php echo $tabla;?>
  </div>
  <div class="grid_16">
    <center><?php //echo $boton_gantt ?></center>  
  </div>
  <div class="clear"></div>
</div>
</div>