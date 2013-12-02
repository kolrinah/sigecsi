<?php if ( ! defined('BASEPATH')) exit('Sin Acceso Directo al Script');?>
<table width="100%">
 <tr>
  <td style="vertical-align:middle; text-align:left">
      <h4><?php echo $ecodigo.' - '.$estructura; ?></h4>
  </td>
  <td width="10%" >
  </td>
 </tr>
</table>

<table width="100%">
 <tr>
  <td width="30px" class="BotonIco">
   <img src="<?php echo base_url(); ?>imagenes/back.png" 
        onclick="javascript:actualiza();" 
        title="clic para regresar" /> 
  </td>
  <td style="width:160px; text-align: right;">
      <input type="hidden" id="id_subproducto" value="<?php echo $id_subproducto; ?>" />
      <h5>Sub-Producto: <?php echo $pcodigo.'.'.$scodigo; ?>&nbsp;</h5>
  </td>
  <td>
      <h5>   
   <?php echo $nombre; ?>
      </h5>    
  </td>
 </tr>
 <tr>
  <td colspan="3" style="height:40px">
   <h4>Registros de Ejecución
   </h4>
  </td>
 </tr>
</table>