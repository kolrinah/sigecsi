<?php if ( ! defined('BASEPATH')) exit('Sin Acceso Directo al Script');?>
<div class="container_16 CuerpoPpal">
<div class="grid_16 alpha omega AjusteCuerpo">
  <div class="grid_16 alpha omega">
    <h1><?php echo $titulo;?></h1>
    <?php echo $selectores ?>
    <table width="100%">
        <tr>
            <td>
                <h4><?php echo $subtitulo;
                          echo img($flecha_sus);
                          echo form_input($year_poa); 
                          echo img($flecha_sum);                
                    ?>
                </h4>                
            </td>
            <td style="text-align: right; width:50px">
               <?php echo img($pdf); ?> 
            </td>
            <td style="text-align: right; width:50px">
               <form action="ficheroExcel.php" method="post" id="formExportar" >   
               <?php echo img($xls); ?> 
                   <input type="hidden" id="datos_a_enviar" name="datos_a_enviar">
               </form>    
            </td>
        </tr>
    </table>
  </div>  
  <div class="clear"></div>
  <div class="grid_16" id="Planes">
    <?php echo $tabla;?>
  </div>
  <div class="clear"></div>
</div>
</div>