<?php
class Personal extends CI_Model
{
// LISTAR REQUERIMIENTO DE PERSONAL A PARTIR DEL CODIGO DE ESTRUCTURA Y AÑO DEL POA
   function listar_requerimiento_personal($estructuras,$yearpoa) 
   {
        $sql = "select * from
                (
                  select * from rp_requerimiento_personal
                  join rp_personal using (id_personal)
                  join rp_tipo_personal using (id_tipo_personal)                  
                  where yearpoa=$yearpoa
                ) p
                join e_estructura using (id_estructura)
                $estructuras
                order by id_estructura asc, accion_centralizada desc, id_tipo_personal, id_personal asc;";            

        $query = $this->db->query($sql);
        return $query;       
   }

   // OBTENER FICHA DE REQUERIMIENTO DE PERSONAL A PARTIR DE SU id    
     function obtener_rp($id_requerimiento_personal)
     {           
         $this->db->join('rp_personal','rp_personal.id_personal=rp_requerimiento_personal.id_personal');
         $this->db->join('e_estructura','e_estructura.id_estructura=rp_requerimiento_personal.id_estructura');
         $this->db->join('rp_tipo_personal','rp_tipo_personal.id_tipo_personal=rp_personal.id_tipo_personal');
         $query = $this->db->get_where('rp_requerimiento_personal', array('id_requerimiento_personal' => $id_requerimiento_personal));         

        if($query->num_rows()>0)
        {
           return $query->row();
        }
        else {return false;}
     }
     
     // CONSULTA PARA OBTENER EL CONSOLIDADO DE REQUERIMIENTOS DE PERSONAL POR AÑO         
     function consolidar_requerimiento_personal($yearpoa)
     {
         $sql="select accion_centralizada, tipo_personal, personal, 
                      sum(femenino) as femenino, sum(masculino) as masculino
               from rp_requerimiento_personal
               join rp_personal using(id_personal)
               join rp_tipo_personal using(id_tipo_personal)
               where yearpoa=$yearpoa
               group by accion_centralizada, tipo_personal, personal
               order by accion_centralizada desc, tipo_personal, personal";
         
         $query = $this->db->query($sql);
         return $query;
     }
}
?>