<?php
class Estructura extends CI_Model
{
     function busca_oficina($frase)
     {           
       $sql="SELECT e.id_estructura as id, e.id_tipo_estructura as tipo, e.codigo, e.descripcion,
              case 
                  when e.id_tipo_estructura<3 then e.descripcion
                  when e.id_tipo_estructura=4 then e1.descripcion
                  else e.descripcion
              end as direccion,
              case 
                  when e.id_tipo_estructura<3 then e.descripcion
                  when e.id_tipo_estructura=4 then e2.descripcion
                  else e1.descripcion
              end as oficina
             FROM e_estructura e
             INNER JOIN e_estructura e1 ON e1.id_estructura=e.id_sup 
             INNER JOIN e_estructura e2 ON e2.id_estructura=e1.id_sup 
             WHERE TRANSLATE(upper(e.descripcion),'áÁéÉíÍóÓúÚ', 'aAeEiIoOuU') 
             LIKE TRANSLATE(upper('%$frase%'),'áÁéÉíÍóÓúÚ', 'aAeEiIoOuU') AND e.activo=TRUE
             AND e.servicio_interno=TRUE OR e.codigo LIKE '$frase%' 
             ORDER BY tipo DESC, oficina, direccion, descripcion ASC LIMIT 10;";

        $query = $this->db->query($sql);
        if($query->num_rows()>0)
        {
           return $query->result_array();          
        }
        else {return array('No hubo coincidencias');}
     }
     
     function listar_unidades($frase,$id_estructura)
     {           
       $sql="SELECT e.id_estructura as id, e.id_tipo_estructura as tipo, e.codigo, e.descripcion, 
              case 
                  when e.id_tipo_estructura<3 then e.descripcion
                  when e.id_tipo_estructura=4 then e1.descripcion
                  else e.descripcion
              end as direccion,
              case 
                  when e.id_tipo_estructura<3 then e.descripcion
                  when e.id_tipo_estructura=4 then e2.descripcion
                  else e1.descripcion
              end as oficina
             FROM (-- OBTENEMOS OFICINAS
		select id_estructura, codigo, descripcion, id_tipo_estructura, id_sup, servicio_interno, activo, fecha_tope from e_estructura
		where (id_sup=$id_estructura or id_estructura=$id_estructura) and servicio_interno=true and activo=true
		union
		-- OBTENEMOS DIRECCIONES DE LINEA
		select d.id_estructura, d.codigo, d.descripcion, d.id_tipo_estructura, d.id_sup, d.servicio_interno, d.activo, d.fecha_tope from e_estructura d
		join (select id_estructura, codigo, descripcion, id_tipo_estructura, id_sup, servicio_interno, activo, fecha_tope from e_estructura where id_sup=$id_estructura or id_estructura=$id_estructura ) o on o.id_estructura=d.id_sup
		where d.servicio_interno=true and d.activo=true
		union
		-- OBTENEMOS AREAS
		select a.id_estructura, a.codigo, a.descripcion, a.id_tipo_estructura, a.id_sup, a.servicio_interno, a.activo, a.fecha_tope from e_estructura a
		join (select d.id_estructura, d.codigo, d.descripcion, d.id_tipo_estructura, d.id_sup, d.servicio_interno, d.activo from e_estructura d
		join (select id_estructura, codigo, descripcion, id_tipo_estructura, id_sup, servicio_interno, activo, fecha_tope from e_estructura where id_sup=$id_estructura or id_estructura=$id_estructura ) o on o.id_estructura=d.id_sup) m on m.id_estructura= a.id_sup) e
             INNER JOIN e_estructura e1 ON e1.id_estructura=e.id_sup 
             INNER JOIN e_estructura e2 ON e2.id_estructura=e1.id_sup 
             WHERE TRANSLATE(upper(e.descripcion),'áÁéÉíÍóÓúÚ', 'aAeEiIoOuU') 
             LIKE TRANSLATE(upper('%$frase%'),'áÁéÉíÍóÓúÚ', 'aAeEiIoOuU') AND e.activo=TRUE
             AND e.servicio_interno=TRUE OR e.codigo LIKE '%$frase%' 
             ORDER BY 1 LIMIT 10;";

        $query = $this->db->query($sql);
        if($query->num_rows()>0)
        {
           return $query->result_array();          
        }
        else {return array('No hubo coincidencias');}
     }
     
     // OBTENER ESTRUCTURAS INFERIORES A UNA ESTRUCTURA DADA
     function obtener_estructuras_inferiores($id_estructura)
     {
        $sql="-- OBTENEMOS OFICINAS
              select id_estructura, codigo, descripcion, id_tipo_estructura, 
                     id_sup, servicio_interno, activo, fecha_tope from e_estructura
              where (id_sup=$id_estructura or id_estructura=$id_estructura) 
              and servicio_interno=true and activo=true
              union
              -- OBTENEMOS DIRECCIONES DE LINEA
              select d.id_estructura, d.codigo, d.descripcion, d.id_tipo_estructura, 
                     d.id_sup, d.servicio_interno, d.activo, d.fecha_tope 
              from e_estructura d
              join 
               (select id_estructura, codigo, descripcion, id_tipo_estructura, 
                     id_sup, servicio_interno, activo, fecha_tope
                from e_estructura where id_sup=$id_estructura or id_estructura=$id_estructura ) o
                on o.id_estructura=d.id_sup
              where d.servicio_interno=true and d.activo=true
              union
              -- OBTENEMOS AREAS
              select a.id_estructura, a.codigo, a.descripcion, a.id_tipo_estructura, a.id_sup, 
                     a.servicio_interno, a.activo, a.fecha_tope 
              from e_estructura a
              join 
               (select d.id_estructura, d.codigo, d.descripcion, d.id_tipo_estructura, 
                       d.id_sup, d.servicio_interno, d.activo from e_estructura d
                join (select id_estructura, codigo, descripcion, id_tipo_estructura, 
                             id_sup, servicio_interno, activo, fecha_tope
                     from e_estructura where id_sup=$id_estructura or id_estructura=$id_estructura) o 
                      on o.id_estructura=d.id_sup) m on m.id_estructura= a.id_sup
              where a.servicio_interno=true and a.activo=true
              order by 1";
        
        $query = $this->db->query($sql);
        if($query->num_rows()>0)
        {
           return $query->result_array();
        }
        else {return false;}
     }
     
     // LISTAR UNIDADES QUE DEPENDEN DIRECTAMENTE DE UNA ESTRUCTURA DADA
     function unidades_inferiores($id_estructura)
     {
        $sql="select * from e_estructura
              where activo=true and servicio_interno=true 
              and id_sup=$id_estructura
              and id_estructura!=$id_estructura
              order by 1";
        
        $query = $this->db->query($sql);
        if($query->num_rows()>0)
        {
           return $query->result_array();
        }
        else {return false;}
     }
     
     // OBTENER DETALLES DE UNA ESTRUCTURA
     function obtener_estructura($id_estructura)
     {        
        $this->db->where('id_estructura', $id_estructura);         
        $query=$this->db->get('e_estructura');
        if($query->num_rows()==1){return $query->row_array();}
        else {return false;}
     }
     
     // VERIFICAMOS SI LA UNIDAD ENVIO SU POA PARA EL AÑO INDICADO
     function poaEnviado($id_estructura, $yearpoa)
     {
        $sql = "select * from e_estructura
               left join e_envio_poa using(id_estructura)
               where id_estructura=$id_estructura and yearpoa=$yearpoa";
         
        return $this->db->query($sql);
     }
}
?>