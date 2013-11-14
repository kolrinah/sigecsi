<?php if ( ! defined('BASEPATH')) exit('Sin Acceso Directo al Script');?>
<div class="container_16 CuerpoPpal">
    <div class="grid_16 AjusteCuerpo">
    <?php if (($this->session->userdata('id_usuario'))==1)echo var_dump($_SESSION);?>
    
    <?php echo $data;    ?>
   </div>
    
<div class="clear"></div>
</div>
