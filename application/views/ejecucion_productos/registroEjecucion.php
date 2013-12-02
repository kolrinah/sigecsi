<?php if ( ! defined('BASEPATH')) exit('Sin Acceso Directo al Script');?>
<table class="TablaNivel1 Zebrado">
  <thead>
   <tr>
     <th style="width: 100px"> Fecha </th>
     <th style="width: 100px"> Cantidad </th>
     <th style="width: 200px; text-align: left"> Unidad Medida </th>  
     <th style="text-align: left"> Descripción </th>
     <th style="width: 200px; text-align: left"> Ejecutante </th>
   </tr>  
  </thead>
  <tbody>
  <?php foreach ($ejecucion as $e) { ?>  
   <tr class="Resaltado" 
       onclick="javascript: revisarRegistro(<?php echo $e->id_ejecucion;  ?> )">
    <td>
     <?php echo $e->fecha_ejecucion;  ?>   
    </td>   
    <td>
     <?php echo $e->cantidad_ejecutada;  ?>   
    </td>
    <td style="text-align: left">
     <?php echo $e->unidad_medida;  ?>   
    </td>       
    <td style="text-align: left">
     <?php echo $e->descripcion;  ?>   
    </td>   
    <td style="text-align: left">
     <?php echo $e->nombre.' '.$e->apellido;  ?>   
    </td>       
   </tr>
  <?php } ?>            
  </tbody>
  <tfoot>
   <tr>
    <td colspan="5"></td>      
   </tr>   
  </tfoot>      
</table><br/>