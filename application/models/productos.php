<?php
class Productos extends CI_Model
{
// LISTAR PRODUCTOS A PARTIR DEL CODIGO DE ESTRUCTURA
   function listar_productos($id_estructura) 
   {
        $sql = "select p.id_producto, p.id_estructura, p.codigo, p.nombre, p.definicion, 
                       case when s.cantidad>0 then true else false end as subproductos 
               from c_productos p
               left join (select id_producto, count(codigo) as cantidad 
                          from c_subproductos 
                          group by id_producto 
                          order by id_producto) s using (id_producto)
               where p.id_estructura=$id_estructura
               order by cast(p.codigo as integer);";

        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) 
        {
          return $query->result_array();
        } 
        else 
        {
          return false;
        }
   }

// LISTAR SUB-PRODUCTOS A PARTIR DE UN PRODUCTO ADMINISTRATIVO
   function listar_subproductos($id_prod)
   {           
        $sql="select q.activo, q.es_extraordinario, q.id_subproducto,
                     q.pcodigo, q.scodigo, q.nombre, q.definicion, q.unidad_medida, 
                     q.es_determinado, q.es_tramite, q.dependencia, count(i.id_dependencia) as insumo
              from (select s.activo, s.es_extraordinario, s.id_subproducto, 
                           p.codigo as pcodigo, s.codigo as scodigo, s.nombre, 
                           s.definicion, s.es_determinado, s.es_tramite, s.unidad_medida, 
                           count(id_dependencia) as dependencia 
                     from c_subproductos s
                     join c_productos p using (id_producto)
                     left join c_dependencias d using(id_subproducto)
                     where id_producto=$id_prod
                     group by 1, 2, 3, 4, 5, 6, 7, 8, 9, 10) q
              left join c_dependencias i on q.id_subproducto=i.id_subprod_depen
              group by 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11
              order by cast(q.pcodigo as integer), cast(q.scodigo as integer)";
        
        $query = $this->db->query($sql);
        if($query->num_rows()>0)
        {
           return $query->result_array();
        }
        else {return false;}
   }
     
// LISTAR SUB-PRODUCTOS EXISTENTES EN LA UNIDAD QUE NO DEPENDAN DEL SUBPRODUCTO
   function listar_subproductos_unidad($id_unidad, $id_subprod=null)
   {  
      $donde='';
      if (!is_null($id_subprod))
      {
        $this->db->select('id_subprod_depen');
        $query = $this->db->get_where('c_dependencias', array('id_subproducto' => $id_subprod));        
        if ($query->num_rows()>0)
        {
          $subproductos=$query->result_array();          
          foreach ($subproductos as $fila)
          {
            $donde.= ' AND s.id_subproducto!='.$fila['id_subprod_depen'];
          } 
        }
        $donde.= ' AND s.id_subproducto!='.$id_subprod;
      }
      
      $sql="select q.id_subproducto, q.pcodigo, q.scodigo, producto, 
                   q.nombre, q.definicion, q.unidad_medida, q.es_determinado,
                   q.es_tramite, q.dependencia, count(i.id_dependencia) as insumo
            from (select s.id_subproducto, p.codigo as pcodigo, s.codigo as scodigo, 
                        p.nombre as producto, s.nombre, s.definicion, s.es_determinado, 
                        s.es_tramite, s.unidad_medida, count(id_dependencia) as dependencia 
                 from c_subproductos s
                 join c_productos p using (id_producto)
                 left join c_dependencias d using(id_subproducto)
                 where id_estructura=$id_unidad $donde       
                 group by 1, 2, 3, 4, 5, 6, 7, 8, 9) q
            left join c_dependencias i on q.id_subproducto=i.id_subprod_depen
            group by 1, 2, 3, 4, 5, 6, 7, 8, 9, 10
            order by 2, 3";
        
        $query = $this->db->query($sql);
        if($query->num_rows()>0)
        {
           return $query->result_array();
        }
        else {return false;}
     }
     
// OBTENER DETALLES DE UN SUBPRODUCTO A PARTIR DE SU id    
     function obtener_subproducto($id_subprod)
     {           
        $sql="select p.id_producto, p.codigo as pcodigo, p.nombre as pnombre, p.definicion as pdefinicion,
                     s.id_subproducto,
                     s.codigo as scodigo, s.nombre, s.definicion, s.es_determinado,
                     s.es_tramite, s.unidad_medida, s.activo, s.es_extraordinario,
                     e.id_estructura, e.codigo as ecodigo, e.descripcion as estructura
              from c_subproductos s
              join c_productos p using(id_producto)
              join e_estructura e using(id_estructura)
              where id_subproducto=$id_subprod";
        
        $query = $this->db->query($sql);
        if($query->num_rows()>0)
        {
           return $query->row_array();
        }
        else {return false;}
     }
     
// OBTENER DETALLES DE LA PLANIFICACION DE UN SUBPRODUCTO A PARTIR DE SU id
     function obtener_planificacion($id_plan_producto)
     {           
        $sql="select pp.*,
                     p.codigo as pcodigo, p.nombre as pnombre, p.definicion as pdefinicion,                     
                     s.codigo as scodigo, s.nombre, s.definicion, s.es_determinado,
                     s.es_tramite, s.unidad_medida, s.activo, s.es_extraordinario,
                     e.id_estructura, e.codigo as ecodigo, e.descripcion as estructura
              from c_plan_productos pp
              join c_subproductos s using(id_subproducto)
              join c_productos p using(id_producto)
              join e_estructura e using(id_estructura)
              where id_plan_producto=$id_plan_producto";
        
        $query = $this->db->query($sql);
        if($query->num_rows()>0)
        {
           return $query->row_array();
        }
        else {return false;}
     }     

// LISTAR DEPENDENCIAS DE UN SUBPRODUCTO
     function listar_dependencias($id_subprod)
     {           
        $sql="select d.id_dependencia, d.id_subproducto, d.id_subprod_depen, 
                     p.codigo as pcod, s.codigo as scod, p.nombre as nombre_p, 
                     s.nombre as nombre_s, e.codigo as cod_estructura, 
                     e.descripcion as unidad, j.descripcion as superior
             from c_dependencias d
             join c_subproductos s on s.id_subproducto=d.id_subprod_depen
             join c_productos p using (id_producto)  
             join e_estructura e using (id_estructura)
             join e_estructura j on e.id_sup=j.id_estructura
             where d.id_subproducto=$id_subprod order by cod_estructura, pcod, scod";
        
        $query = $this->db->query($sql);
        if($query->num_rows()>0)
        {
           return $query->result_array();
        }
        else {return false;}
     }
 
// LISTAR INSUMO DE UN SUBPRODUCTO     
     function listar_insumo($id_subprod)
     {           
        $sql="select d.id_dependencia, d.id_subproducto, d.id_subprod_depen, 
                     p.codigo as pcod, s.codigo as scod, p.nombre as nombre_p, 
                     s.nombre as nombre_s, e.codigo as cod_estructura, 
                     e.descripcion as unidad, j.descripcion as superior
              from c_dependencias d
              join c_subproductos s using (id_subproducto)
              join c_productos p using (id_producto)
              join e_estructura e using (id_estructura)
              join e_estructura j on e.id_sup=j.id_estructura
              where d.id_subprod_depen=$id_subprod order by cod_estructura, pcod, scod";
        
        $query = $this->db->query($sql);
        if($query->num_rows()>0)
        {
           return $query->result_array();
        }
        else {return false;}
     } 
 
// OBTENER LA PLANIFICACION DE UNA ESTRUCTURA Y AÑO DADOS     
     function planificacion_productos($estructuras,$yearpoa )
     {
        $sql="select *
              from
                  (select e.*, s.id_subproducto, p.codigo as pcodigo, 
                          p.nombre as pnombre, s.codigo as scodigo, s.nombre as snombre
                   from c_subproductos s
                   join c_productos p using (id_producto)
                   join e_estructura e using (id_estructura)
                   where s.es_determinado=true and s.activo=true) h
              left join 
                  (
                   select s.id_subproducto, 
                   sum(case 
                       when date_part('month',c.fecha_fin)='01' then c.cantidad
                       else 0
                   end) as ene,
                   sum(case 
                       when date_part('month',c.fecha_fin)='02' then c.cantidad
                       else 0
                   end) as feb,
                   sum(case 
                       when date_part('month',c.fecha_fin)='03' then c.cantidad
                       else 0
                   end) as mar,
                   sum(case 
                       when date_part('month',c.fecha_fin)='04' then c.cantidad
                       else 0
                   end) as abr,
                   sum(case 
                       when date_part('month',c.fecha_fin)='05' then c.cantidad
                       else 0
                   end) as may,
                   sum(case 
                       when date_part('month',c.fecha_fin)='06' then c.cantidad
                       else 0
                   end) as jun,
                   sum(case 
                       when date_part('month',c.fecha_fin)='07' then c.cantidad
                       else 0
                   end) as jul,
                   sum(case 
                       when date_part('month',c.fecha_fin)='08' then c.cantidad
                       else 0
                   end) as ago,
                   sum(case 
                       when date_part('month',c.fecha_fin)='09' then c.cantidad
                       else 0
                   end) as sep,
                   sum(case 
                       when date_part('month',c.fecha_fin)='10' then c.cantidad
                       else 0
                   end) as oct,
                   sum(case 
                       when date_part('month',c.fecha_fin)='11' then c.cantidad
                       else 0
                   end) as nov,
                   sum(case 
                       when date_part('month',c.fecha_fin)='12' then c.cantidad
                       else 0
                   end) as dic,
                   sum(c.cantidad) as anual
                   from c_plan_productos c
                   join c_subproductos s using (id_subproducto)
                   where date_part('year',c.fecha_fin)='$yearpoa'
                   group by 1
                   ) pl using (id_subproducto)
              $estructuras
              order by id_estructura, cast(pcodigo as integer), cast(scodigo as integer);";
        
        $query = $this->db->query($sql);        
        return $query;
     }
     
     // OBTENER LA PLANIFICACION EN GANTT DE UNA ESTRUCTURA Y AÑO DADOS     
     function gantt_productos($estructuras, $yearpoa)
     {
        $sql="select * from
               	(select e.*, 
               		p.codigo as pcodigo, p.nombre as pnombre, s.id_subproducto,
                        s.codigo as scodigo, s.nombre as snombre, s.unidad_medida
               	 from c_subproductos s
               	 join c_productos p using (id_producto)
               	 join e_estructura e using (id_estructura)
               	 where s.es_determinado=true and e.activo=true) s
               left join 
               	(select n.id_plan_producto, n.id_subproducto, n.descripcion as actividad, n.fecha_ini, n.fecha_fin,
               		n.id_responsable, u.nombre ||' '|| u.apellido as responsable
               	 from c_plan_productos n
               	 join a_usuarios u on (id_responsable=id_usuario)
               	 where date_part('year',n.fecha_fin)='$yearpoa'
               	 order by n.id_subproducto) p using (id_subproducto)
               $estructuras
               order by id_estructura, cast(pcodigo as integer), cast(scodigo as integer), fecha_ini";
        $query = $this->db->query($sql);
        return $query;
     }
     
     // OBTENER EL ESTADO DE LA PLANIFICACION DE UNA ESTRUCTURA (INCLUYE SUBORDINADAS) Y AÑO DADOS
     function estado_planificacion($estructuras, $yearpoa)
     {
        $sql="select * from (
	      select min(id_estructura) as id_estructura, 
	             coalesce(sum(sp_det),0) as sp_det, 
                     coalesce(sum(sp_plan),0) as sp_plan, 
                     coalesce(sum(proy),0) as proyectos,
                     coalesce(sum(personal),0) as personal, 
                     coalesce(sum(insumos),0) as insumos
	      from e_estructura e
              left join 
              (-- OBTENEMOS LA CANTIDAD DE SUBPRODUCTOS DETERMINADOS POR ESTRUCTURA
                  select e.id_estructura, count(e.id_estructura) as sp_det from e_estructura e
                  join c_productos p using (id_estructura)
                  join c_subproductos s using (id_producto)
                  where es_determinado=true and s.activo=true and e.activo=true and e.servicio_interno=true
                  group by 1    
              ) det using (id_estructura)
              left join
              (-- OBTENEMOS LA CANTIDAD DE SUBPRODUCTOS PLANIFICADOS POR ESTRUCTURA EN EL AÑO DADO
                  select id_estructura, count(id_estructura) as sp_plan 
                  from
                  (-- OBTENEMOS LOS SUBPRODUCTOS CON PLANIFICACION POR ESTRUCTURA EN EL AÑO DADO
              	select e.id_estructura, s.id_subproducto
              	from e_estructura e
              	join c_productos p using (id_estructura)
              	join c_subproductos s using (id_producto)
              	join c_plan_productos c using (id_subproducto)
              	where date_part('year',c.fecha_fin)='$yearpoa' and e.activo=true and e.servicio_interno=true
              	group by 1, 2
                  ) plan
              group by 1
              ) plan using (id_estructura)
	      left join
	      (-- OBTENEMOS LA CANTIDAD DE PROYECTOS CREADOS POR ESTRUCTURA EN EL AÑO DADO
                  select id_estructura, count(id_estructura) as proy 
                  from
                  (-- OBTENEMOS LOS PROYECTOS POR ESTRUCTURA EN EL AÑO DADO
              	      select e.id_estructura, p.id_proyecto
              	      from e_estructura e
                      join p_proyectos p using (id_estructura)              	
                      where p.yearpoa='$yearpoa' and e.activo=true and e.servicio_interno=true
              	      group by 1, 2
                  ) pry
                group by 1
              ) pry using (id_estructura) 
              left join
              (--OBTENEMOS LOS REQUERIMIENTOS DE PERSONAL
	       select id_estructura, sum(femenino+masculino) as personal from rp_requerimiento_personal
	       where yearpoa='$yearpoa' 
	       group by id_estructura
               order by id_estructura) rp using(id_estructura)
              left join
	      (--OBTENEMOS LOS REQUERIMIENTOS DE INSUMO
	       select id_estructura, count(id_estructura) as insumos from ri_requerimiento_insumos
	       where yearpoa='$yearpoa'
	       group by id_estructura
	       order by id_estructura) ri using(id_estructura)              
	      $estructuras
	      ) e
             left join
	      (-- OBTENEMOS LOS POA ENVIADOS EN EL AÑO
		select * from e_envio_poa
		where yearpoa='$yearpoa' 
		order by 1
              ) poa using (id_estructura)
              left join
              (-- ESTATUS DE PROYECTOS POR ESTRUCTURA
                select id_estructura, 
                       bit_and(case when (coalesce(cantidad,0)*coalesce(costo_unitario,0))>0 then 1
			       else 0 
			       end) as estatus
                from p_proyectos
                left join p_acciones using (id_proyecto)
                left join p_actividades using (id_accion)
                left join p_presupuesto_actividad p using (id_actividad)
                where yearpoa=$yearpoa
                group by 1
              ) estatus using (id_estructura);";
                      
        $query = $this->db->query($sql);
        if($query->num_rows()==1){return $query->row_array();}
        else {return false;}
     }
     
}
?>