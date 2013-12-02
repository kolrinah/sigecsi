<?php if ( ! defined('BASEPATH')) exit('Sin Acceso Directo al Script');?>

<div class="EntraDatos">
 <table>
  <thead>
   <tr>
    <th colspan="2">Selección de Reporte</th>
   </tr>           
  </thead>            
  <tbody>
   <tr>
       <td style="text-align: right; width: 100px; vertical-align: top">Unidad: </td>
       <td style="">
        <?php echo $p->codigo.' '.mb_convert_case($p->descripcion, MB_CASE_UPPER) ?></td>
   </tr>        
   <tr>
       <td style="text-align: right; width: 100px; vertical-align: top">Proyecto: </td>
       <td style="">
        <?php echo $p->cod_proy.' '.mb_convert_case($p->obj_esp, MB_CASE_UPPER) ?></td>
   </tr>
   <tr>
       <td colspan="2"><hr/> Seleccione el Reporte que desee ver:</td>
   </tr>
   <tr>
       <td></td>
       <td>
        <ul>
         <li>Marco Lógico del Proyecto</li>
         <li class="Resaltado" onclick="javascript:presupuestoProyecto(<?php echo $p->id_proyecto?>)"
             >Distribución Presupuestaria del Proyecto</li>
         <li class="Resaltado" onclick="javascript:metasActividadesProyecto(<?php echo $p->id_proyecto?>)"
             >Metas de Actividades del Proyecto</li>
        </ul>  
       </td>
   </tr>   
  </tbody>      
  <tfoot>
   <tr>
    <td colspan="2">   
     <div class="BotonIco" onclick="javascript:CancelarModal()" title="Cerrar">
      <img src="imagenes/cancel.png"/>&nbsp;
      Cerrar
     </div>
    </td>
   </tr>
  </tfoot>
 </table>   
</div>